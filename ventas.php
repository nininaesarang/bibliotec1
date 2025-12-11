<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = null;
$error_message = null;
$detalles_venta = null;

try {

    $stmt_stores = $pdo->query("SELECT stor_id, stor_name FROM stores ORDER BY stor_name");
    $tiendas = $stmt_stores->fetchAll(PDO::FETCH_ASSOC);

    $stmt_titles = $pdo->query("SELECT title_id, title, price FROM titles ORDER BY title");
    $libros = $stmt_titles->fetchAll(PDO::FETCH_ASSOC);

    $stock_map = [];
    $stmt_stock = $pdo->query("SELECT stor_id, title_id, qty FROM stockInStores");
    while ($row = $stmt_stock->fetch(PDO::FETCH_ASSOC)) {
        $stock_map[$row['stor_id']][$row['title_id']] = $row['qty'];
    }
    $json_stock = json_encode($stock_map);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $stor_id  = $_POST['stor_id'];
    $title_id = $_POST['title_id'];
    $qty      = (int)$_POST['qty'];
    $payterms = $_POST['payterms'];
    $ord_date = $_POST['ord_date'];
    $ord_num  = !empty($_POST['ord_num']) ? trim($_POST['ord_num']) : 'ORD-' . time();

    if ($qty <= 0) {
        $error_message = "La cantidad debe ser mayor a 0.";
    } else {
        try {
            $pdo->beginTransaction();

            $stmt_check = $pdo->prepare("SELECT qty FROM stockInStores WHERE stor_id = ? AND title_id = ?");
            $stmt_check->execute([$stor_id, $title_id]);
            $stock_actual = $stmt_check->fetchColumn();

            if ($stock_actual === false || $stock_actual < $qty) {
                throw new Exception("Stock insuficiente. Disponibles: " . (int)$stock_actual);
            }

            $stmt_info = $pdo->prepare("SELECT price FROM titles WHERE title_id = ?");
            $stmt_info->execute([$title_id]);
            $precio_unitario = $stmt_info->fetchColumn();

            $sql_discount = "SELECT discount, discounttype FROM discounts 
                             WHERE (stor_id = :stor OR stor_id IS NULL) 
                             AND (:qty >= lowqty AND :qty <= highqty)
                             ORDER BY discount DESC LIMIT 1";
            $stmt_disc = $pdo->prepare($sql_discount);
            $stmt_disc->execute([':stor' => $stor_id, ':qty' => $qty]);
            $regla = $stmt_disc->fetch(PDO::FETCH_ASSOC);

            $desc_pct = $regla ? $regla['discount'] : 0;
            $desc_tipo = $regla ? $regla['discounttype'] : "Ninguno";

            $subtotal = $precio_unitario * $qty;
            $monto_desc = $subtotal * ($desc_pct / 100);
            $total = $subtotal - $monto_desc;

            $sql_sale = "INSERT INTO sales (stor_id, ord_num, ord_date, qty, payterms, title_id) 
                         VALUES (:stor, :ord, :date, :qty, :pay, :title)";
            $stmt_sale = $pdo->prepare($sql_sale);
            $stmt_sale->execute([
                ':stor' => $stor_id, ':ord' => $ord_num, ':date' => $ord_date, 
                ':qty' => $qty, ':pay' => $payterms, ':title' => $title_id
            ]);

            $sql_upd = "UPDATE stockInStores SET qty = qty - :qty WHERE stor_id = :stor AND title_id = :title";
            $stmt_upd = $pdo->prepare($sql_upd);
            $stmt_upd->execute([':qty' => $qty, ':stor' => $stor_id, ':title' => $title_id]);

            $pdo->commit();
            $message = "¡Venta registrada!";
            
            $detalles_venta = "
                <div class='receipt'>
                    <h3>Orden: $ord_num</h3>
                    <p>Subtotal: $" . number_format($subtotal, 2) . "</p>
                    <p>Descuento ($desc_pct%): -$" . number_format($monto_desc, 2) . "</p>
                    <p class='total'>Total: $" . number_format($total, 2) . "</p>
                    <a href='generar_recibo.php?ord_num=$ord_num' target='_blank' class='btn-dashboard btn-historial'>Imprimir Recibo PDF</a>
                </div>
            ";

        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = ($e->getCode() == '23000') ? "Error: Orden duplicada." : $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Punto de Venta</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .error-msg { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .success-msg { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .receipt { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .receipt .total { font-size: 1.2em; color: #27ae60; font-weight: bold; }
        .form-group select, .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        
        #stock_display { font-weight: bold; margin-top: 5px; display: block; font-size: 0.9em; transition: color 0.3s; }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Venta</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Inicio</a></li>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="autores.php">Autores</a></li>
                    <?php endif; ?>
                    <li><a href="#">Nueva Venta</a></li>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="librerias.php">Librerías</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="libros.php">Libros</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="#">Usuarios</a></li>
                    <?php endif; ?>
                    <li class="user-welcome">
                        Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </li>
                    <li><a href="logout.php">Salir</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="form-container" style="max-width: 600px;">
            <h2>Registrar Venta</h2>
            
            <?php if ($error_message): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="success-msg"><?php echo $message; ?></div>
                <?php echo $detalles_venta; ?>
            <?php endif; ?>
            
            <form action="" method="POST">
                
                <div class="form-group">
                    <label for="stor_id">Librería / Sucursal:</label>
                    <select id="stor_id" name="stor_id" required onchange="actualizarStock()">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($tiendas as $t): ?>
                            <option value="<?php echo $t['stor_id']; ?>"><?php echo htmlspecialchars($t['stor_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title_id">Libro:</label>
                    <select id="title_id" name="title_id" required onchange="actualizarStock()">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($libros as $l): ?>
                            <option value="<?php echo $l['title_id']; ?>">
                                <?php echo htmlspecialchars($l['title']); ?> - $<?php echo number_format($l['price'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <span id="stock_display">Seleccione tienda y libro para ver stock.</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="qty">Cantidad:</label>
                        <input type="number" id="qty" name="qty" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="ord_date">Fecha:</label>
                        <input type="date" id="ord_date" name="ord_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ord_num">N° Orden:</label>
                        <input type="text" id="ord_num" name="ord_num" placeholder="(Auto-generado)">
                    </div>
                    <div class="form-group">
                        <label for="payterms">Pago:</label>
                        <select id="payterms" name="payterms" required>
                            <option value="Net 30">Net 30</option>
                            <option value="Net 60">Net 60</option>
                            <option value="On Invoice">Contado</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" id="btn_submit" class="btn-primary" style="width: 100%;">Procesar Venta</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        const inventario = <?php echo $json_stock; ?>;

        function actualizarStock() {
            const tiendaSelect = document.getElementById('stor_id');
            const libroSelect = document.getElementById('title_id');
            const stockLabel = document.getElementById('stock_display');
            const qtyInput = document.getElementById('qty');
            const btnSubmit = document.getElementById('btn_submit');

            const tiendaId = tiendaSelect.value;
            const libroId = libroSelect.value;

            if (!tiendaId || !libroId) {
                stockLabel.textContent = "Seleccione tienda y libro para ver stock.";
                stockLabel.style.color = "#666";
                return;
            }

            let cantidadDisponible = 0;
            if (inventario[tiendaId] && inventario[tiendaId][libroId]) {
                cantidadDisponible = parseInt(inventario[tiendaId][libroId]);
            }

            if (cantidadDisponible > 0) {
                stockLabel.textContent = "Stock Disponible: " + cantidadDisponible + " unidades.";
                stockLabel.style.color = "green";
                
                qtyInput.max = cantidadDisponible;
                qtyInput.disabled = false;
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = "1";
            } else {
                stockLabel.textContent = "Agotado en esta sucursal (Stock: 0).";
                stockLabel.style.color = "red";
                
                qtyInput.value = 0;
                qtyInput.disabled = true;
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = "0.5";
            }
        }
    </script>
</body>
</html>