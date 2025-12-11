<?php
session_start();
require 'conexion.php';

require_once __DIR__ . '/vendor/autoload.php';

if (!isset($_GET['ord_num'])) {
    die("Error: No se especificó el número de orden.");
}

$ord_num = $_GET['ord_num'];

try {
    $sql = "SELECT 
                S.ord_date, S.qty, S.payterms,
                ST.stor_name, ST.stor_address, ST.city, ST.state, ST.zip,
                T.title, T.price, T.title_id, ST.stor_id
            FROM sales S
            INNER JOIN stores ST ON S.stor_id = ST.stor_id
            INNER JOIN titles T ON S.title_id = T.title_id
            WHERE S.ord_num = :ord";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ord' => $ord_num]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venta) {
        die("Error: Orden no encontrada.");
    }

    $qty = $venta['qty'];
    $precio = $venta['price'];
    $subtotal = $qty * $precio;

    $sql_desc = "SELECT discount, discounttype FROM discounts 
                 WHERE (stor_id = :stor OR stor_id IS NULL) 
                 AND (:qty >= lowqty AND :qty <= highqty)
                 ORDER BY discount DESC LIMIT 1";
    $stmt_desc = $pdo->prepare($sql_desc);
    $stmt_desc->execute([':stor' => $venta['stor_id'], ':qty' => $qty]);
    $regla = $stmt_desc->fetch(PDO::FETCH_ASSOC);

    $porc_desc = $regla ? $regla['discount'] : 0;
    $tipo_desc = $regla ? $regla['discounttype'] : "";
    
    $monto_desc = $subtotal * ($porc_desc / 100);
    $total_final = $subtotal - $monto_desc;

} catch (PDOException $e) {
    die("Error BD: " . $e->getMessage());
}

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #292222ff; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #da7878; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16pt; color: #2d3748; }
        .header p { margin: 2px 0; font-size: 9pt; color: #666; }
        
        .info-box { width: 100%; margin-bottom: 20px; }
        .info-col { width: 49%; float: left; }
        .info-label { font-weight: bold; color: #555; }
        
        .items-table { width: 100%; margin-bottom: 20px; }
        .items-table th { border-bottom: 1px solid #rgba(184, 144, 144, 1) padding: 8px; text-align: left; }
        .items-table td { border-bottom: 1px solid #ca0b0bff; padding: 8px; }
        
        .totals-box { width: 40%; float: right; text-align: right; }
        .totals-row { margin-bottom: 5px; }
        .total-final { font-size: 14pt; font-weight: bold; color: #27ae60; border-top: 1px solid #ccc; padding-top: 5px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #888; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>BiblioTec</h1>
        <p>Recibo de Venta / Orden de Compra</p>
        <p>Orden #: <strong>' . $ord_num . '</strong></p>
    </div>

    <div class="info-box">
        <table width="100%">
            <tr>
                <td width="50%">
                    <span class="info-label">Cliente (Librería):</span><br>
                    ' . htmlspecialchars($venta['stor_name']) . '<br>
                    ' . htmlspecialchars($venta['stor_address']) . '<br>
                    ' . htmlspecialchars($venta['city']) . ', ' . htmlspecialchars($venta['state']) . ' ' . htmlspecialchars($venta['zip']) . '
                </td>
                <td width="50%" style="text-align: right;">
                    <span class="info-label">Fecha de Emisión:</span><br>
                    ' . date('d/m/Y', strtotime($venta['ord_date'])) . '<br><br>
                    <span class="info-label">Condiciones de Pago:</span><br>
                    ' . htmlspecialchars($venta['payterms']) . '
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="50%">Descripción / Libro</th>
                <th width="15%" align="center">Cant.</th>
                <th width="15%" align="right">Precio U.</th>
                <th width="20%" align="right">Importe</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>' . htmlspecialchars($venta['title']) . '</strong><br>
                    <small>ID: ' . htmlspecialchars($venta['title_id']) . '</small>
                </td>
                <td align="center">' . $qty . '</td>
                <td align="right">$' . number_format($precio, 2) . '</td>
                <td align="right">$' . number_format($subtotal, 2) . '</td>
            </tr>
        </tbody>
    </table>

    <div class="totals-box">
        <div class="totals-row">
            Subtotal: <strong>$' . number_format($subtotal, 2) . '</strong>
        </div>';

        if ($monto_desc > 0) {
            $html .= '
            <div class="totals-row" style="color: #c0392b;">
                Descuento (' . floatval($porc_desc) . '%): -$' . number_format($monto_desc, 2) . '<br>
                <small>(' . htmlspecialchars($tipo_desc) . ')</small>
            </div>';
        }

        $html .= '
        <div class="totals-row total-final">
            Total: $' . number_format($total_final, 2) . '
        </div>
    </div>

    <div class="footer">
        Gracias por su preferencia. | BiblioTec Sistema de Gestión
    </div>

</body>
</html>';
try {
    $mpdf = new \Mpdf\Mpdf([
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 15,
        'margin_bottom' => 15,
        'margin_header' => 10,
        'margin_footer' => 10
    ]);
    
    $mpdf->SetProtection(array('print'));
    $mpdf->SetTitle("Recibo BiblioTec - " . $ord_num);
    $mpdf->SetAuthor("BiblioTec System");
    $mpdf->WriteHTML($html);
    
    $mpdf->Output('Recibo_' . $ord_num . '.pdf', 'I');

} catch (\Mpdf\MpdfException $e) {
    echo "Error al crear PDF: " . $e->getMessage();
}
?>