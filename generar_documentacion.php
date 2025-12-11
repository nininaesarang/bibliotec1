<?php
// generar_pdf.php
session_start();
require 'conexion.php';
require_once __DIR__ . '/vendor/autoload.php'; // Carga mPDF

// 1. SEGURIDAD (Opcional, pero recomendado)
if (!isset($_SESSION['user_id'])) {
    die("Acceso denegado. Debes iniciar sesión.");
}

// 2. CONSULTA SQL (La misma de tu vista, filtrando stock > 0)
try {
    $sql = "
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

    $stmt = $pdo->query($sql);
    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

// 3. CONSTRUIR EL HTML DEL REPORTE
// Usamos estilos CSS dentro del HTML para darle formato profesional
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #a11915; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2d3748; font-size: 18pt; }
        .header p { margin: 5px 0; font-size: 9pt; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #a11915; color: white; padding: 8px; font-weight: bold; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 8px; }
        
        /* Filas alternadas para mejor lectura */
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        .stock-num { color: #276749; font-weight: bold; text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #888; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>BiblioTec - Reporte de Inventario</h1>
        <p>Fecha de emisión: ' . date('d/m/Y H:i') . '</p>
        <p>Generado por: ' . htmlspecialchars($_SESSION['user_name']) . '</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20%">Tienda</th>
                <th width="35%">Título</th>
                <th width="20%">Autor</th>
                <th width="15%" align="right">Precio</th>
                <th width="10%" align="center">Stock</th>
            </tr>
        </thead>
        <tbody>';

if (count($inventario) > 0) {
    foreach ($inventario as $fila) {
        $html .= '
            <tr>
                <td>' . htmlspecialchars($fila['Tienda']) . '</td>
                <td>' . htmlspecialchars($fila['Titulo']) . '</td>
                <td>' . htmlspecialchars($fila['AutorPrincipal']) . '</td>
                <td align="right">$' . number_format($fila['Precio'], 2) . '</td>
                <td class="stock-num">' . $fila['StockDisponible'] . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="5" style="text-align:center; padding:20px;">No hay inventario disponible actualmente.</td></tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        Documento interno de control de stock | BiblioTec v1.0
    </div>

</body>
</html>';

// 4. GENERAR PDF CON MPDF
try {
    // Configuración: Horizontal (Landscape) para que quepan mejor las columnas
    $mpdf = new \Mpdf\Mpdf([
        'format' => 'A4-L', // A4 Landscape
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 15,
        'margin_bottom' => 15
    ]);

    $mpdf->SetTitle('Inventario BiblioTec');
    $mpdf->SetAuthor('BiblioTec System');
    
    // Escribir el HTML
    $mpdf->WriteHTML($html);
    
    // Mostrar número de página en el pie
    $mpdf->setFooter('{PAGENO}');

    // Salida al navegador
    $mpdf->Output('Inventario_BiblioTec.pdf', 'I');

} catch (\Mpdf\MpdfException $e) {
    echo "Error al crear el PDF: " . $e->getMessage();
}
?>