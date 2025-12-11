<?php
require 'vendor/autoload.php'; 
require 'conexion.php'; 

use Mpdf\Mpdf;

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
        ORDER BY
            S.stor_name, T.title;
    ";

    $stmt = $pdo->query($sql_vista);
    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos al generar el PDF: " . $e->getMessage());
}

$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Stock Disponible</title>
    <style>
        body { font-family: sans-serif; }
        h1 { text-align: center; color: #2d3748; margin-bottom: 30px; border-bottom: 2px solid #8f0d0d; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border: 1px solid #e2e8f0; }
        th { background-color: #d53333; color: white; font-weight: 600; }
        tr:nth-child(even) { background-color: #f7fafc; }
        tr:hover { background-color: #ebf4ff; }
    </style>
</head>
<body>

    <h1>Stock Disponible - ' . date('d/m/Y') . '</h1>

    <table>
        <thead>
            <tr>
                <th>Tienda</th>
                <th>Título</th>
                <th>Autor Principal</th>
                <th>Precio</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
';

foreach ($inventario as $fila) {
    $html .= '
        <tr>
            <td>' . htmlspecialchars($fila['Tienda']) . '</td>
            <td>' . htmlspecialchars($fila['Titulo']) . '</td>
            <td>' . htmlspecialchars($fila['AutorPrincipal']) . '</td>
            <td>$' . htmlspecialchars(number_format($fila['Precio'], 2)) . '</td>
            <td>' . htmlspecialchars($fila['StockDisponible']) . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>

</body>
</html>';


try {
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
    
    $mpdf->WriteHTML($html);
    $mpdf->Output('Reporte_Inventario_' . date('Ymd_His') . '.pdf', 'D');

} catch (\Mpdf\MpdfException $e) {
    echo "Error al generar el PDF: " . $e->getMessage();
}

exit;
?>