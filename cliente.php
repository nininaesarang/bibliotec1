<?php
require 'conexion.php';

try {
    $sql_vista = "
        SELECT
            S.stor_name AS Tienda,
            T.title AS Titulo,
            CONCAT(A.au_fname, ' ', A.au_lname) AS AutorPrincipal,
            T.price AS Precio,
            SIS.qty AS StockDisponible
        FROM 
            stores S
        INNER JOIN 
            stockInStores SIS ON S.stor_id = SIS.stor_id
        INNER JOIN 
            titles T ON SIS.title_id = T.title_id
        INNER JOIN 
            titleauthor TA ON T.title_id = TA.title_id
        INNER JOIN 
            authors A ON TA.au_id = A.au_id
        WHERE
            TA.au_ord = 1
            AND SIS.qty > 0 
        ORDER BY
            S.stor_name, T.title;
    ";

    $stmt = $pdo->query($sql_vista);
    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error al obtener la vista: " . $e->getMessage();
    $inventario = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista de Inventario</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-image: url('img/bibliotecas.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed; padding: 30px; }
        .data-section { max-width: 1200px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2d3748; margin-bottom: 30px; border-bottom: 2px solid #8f0d0d; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border: 1px solid #e2e8f0; }
        th { background-color: #a11915; color: white; font-weight: 600; }
        tr:nth-child(even) { background-color: #f7fafc; }
        tr:hover { background-color: #ebf4ff; }
        a{display: inline-block;
        padding: 10px 15px;
        margin-bottom: 20px;
        background-color: #de2720;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        justify-content:center;}
        a:hover {background-color: #a11915}
    </style>
</head>
<body>

    <div class="data-section">
        <h1>Stock Disponible</h1>
    <div class="data-section">
        <table>
            <thead>
                <tr>
                    <th>Tienda</th>
                    <th>Título</th>
                    <th>Autor Principal</th>
                    <th>Precio</th>
                    <th>Stock Disponible</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($inventario)): ?>
                    <?php foreach ($inventario as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['Tienda']); ?></td>
                            <td><?php echo htmlspecialchars($fila['Titulo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['AutorPrincipal']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($fila['Precio'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($fila['StockDisponible']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No se encontraron datos de inventario.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <a href="generar_pdf.php" target="_blank">Generar Reporte PDF</a>
    </div>

</body>
</html>