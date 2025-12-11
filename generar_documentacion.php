<?php
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 20,
    'margin_right' => 20,
    'margin_top' => 25,
    'margin_bottom' => 25,
    'margin_header' => 10,
    'margin_footer' => 10
]);

$mpdf->SetTitle('Documentación BiblioTec');
$mpdf->SetAuthor('Maryjose Martínez Regalado');

$mpdf->SetHeader('Tecnológico Nacional de México - Campus San Pedro||Documentación del Sistema');
$mpdf->SetFooter('BiblioTec v1.0||Página {PAGENO}');

$css = '
<style>
    body { font-family: sans-serif; color: #333; line-height: 1.5; }
    h1 { color: #1b396b; font-size: 24pt; text-align: center; margin-top: 50px; text-transform: uppercase; }
    h2 { color: #1b396b; font-size: 16pt; border-bottom: 2px solid #ccc; margin-top: 30px; padding-bottom: 10px; }
    h3 { color: #2c3e50; font-size: 13pt; margin-top: 20px; }
    p { text-align: justify; margin-bottom: 10px; }
    ul { margin-bottom: 10px; }
    li { margin-bottom: 5px; }
    
    /* Clases para imágenes */
    .img-container { text-align: center; margin: 20px 0; }
    .img-diagram { width: 80%; border: 1px solid #ddd; padding: 5px; }
    .img-screenshot { width: 95%; border: 1px solid #999; box-shadow: 2px 2px 5px #ccc; }
    .caption { font-size: 9pt; color: #666; font-style: italic; margin-top: 5px; text-align: center; }
    
    /* Portada */
    .cover-logos { text-align: center; margin-bottom: 50px; }
    .cover-logo { height: 80px; margin: 0 20px; }
    .cover-info { text-align: center; margin-top: 100px; font-size: 12pt; }
    .cover-author { font-weight: bold; font-size: 14pt; margin-bottom: 10px; }
</style>
';

$html_portada = '
<div class="cover-logos">
    <img src="img/logo_tecnm.png" class="cover-logo" />
    <img src="img/logo_tec_san_pedro.png" class="cover-logo" />
</div>

<h1>Documentación Técnica<br>Sistema BiblioTec</h1>

<div class="img-container">
    <img src="img/bibliotecas.jpg" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 10px;">
</div>

<div class="cover-info">
    <p class="cover-author">Maryjose Martínez Regalado</p>
    <p>Ingeniería en Sistemas Computacionales</p>
    <p>Email: Mmtz5818@gmail.com</p>
    <p>Fecha: ' . date("d de F de Y") . '</p>
</div>

<pagebreak />
';

$html_c4 = '
<h2>1. Documentación de Arquitectura (Modelo C4)</h2>
<p>BiblioTec es un sistema de gestión de inventarios, usuarios, catálogos y ventas de librerías desarrollado con PHP, JS, CSS, HTML y MySQL.</p>

<h3>1.1 Nivel 1: Diagrama de Contexto</h3>
<p>El diagrama de contexto sitúa a BiblioTec en su entorno operativo. Identifica a los usuarios principales (Administrador y Vendedor) y sus interacciones de alto nivel con el sistema.</p>
<div class="img-container">
    <img src="img/c4_contexto.png" class="img-diagram">
    <div class="caption">Imagen 1: Diagrama de contexto del sistema.</div>
</div>

<h3>1.2 Nivel 2: Diagrama de Contenedores</h3>
<p>Este diagrama desglosa la arquitectura técnica. Muestra una aplicación web monolítica en PHP servida por Apache, comunicándose con una base de datos MySQL.</p>
<div class="img-container">
    <img src="img/c4_contenedores.png" class="img-diagram" style="width: 200px; max-height: 300px">
    <div class="caption" >Imagen 2: Diagrama de contenedores.</div>
</div>

<h3>1.3 Nivel 3: Diagrama de Componentes</h3>
<p>Dentro de la aplicación web, el sistema se organiza en módulos funcionales: Seguridad, Inventario, y Ventas, todos utilizando un componente central de Acceso a Datos.</p>
<div class="img-container">
    <img src="img/c4_componentes.png" class="img-diagram" style="width: 300px; max-height: 400px">
    <div class="caption">Imagen 3: Diagrama de componentes.</div>
</div>

<h3>1.4 Nivel 4: Diagrama de Código (ERD)</h3>
<p>El esquema físico de la base de datos "pubs" adaptada, mostrando las relaciones de integridad referencial y la tabla de usuarios aislada.</p>
<div class="img-container">
    <img src="img/erd.png" class="img-diagram">
    <div class="caption">Imagen 4: Diagrama Entidad-Relación.</div>
</div>
';
$html_flujos = '
<h2>2. Flujos Principales del Sistema</h2>

<h3>Login y Acceso</h3>
<p>El usuario ingresa sus credenciales. Si es administrador, accede al dashboard completo (Autores, Libros, Librerías, Ventas, Usuarios). Si es vendedor, solo accede al módulo de Ventas. Existe una vista pública para clientes que solo consultan stock.</p>

<h3>Gestión de Autores y Obras</h3>
<p>El administrador puede registrar autores nuevos. Mediante el botón "Obras", se accede a una interfaz especial para asignar múltiples libros a un autor, actualizando la relación muchos a muchos.</p>

<h3>Flujo de Venta</h3>
<p>En el punto de venta, al seleccionar una tienda y un libro, el sistema valida el stock en tiempo real mediante JavaScript. Al procesar la venta, se genera una transacción en la base de datos que registra la venta y descuenta el inventario simultáneamente. Finalmente, se ofrece la opción de imprimir un recibo PDF.</p>

<h3>Gestión de Inventario (Stock)</h3>
<p>Desde la lista de libros, el botón "Stock" permite distribuir ejemplares de un título específico a las diferentes sucursales, inicializando el inventario o reabasteciendo.</p>
';

$html_codigo = '
<h2>3. Estructura del Código</h2>
<p>El código fuente se encuentra en la carpeta raíz del proyecto. Se utiliza Composer para la gestión de dependencias (mPDF).</p>
<ul>
    <li><strong>/img:</strong> Recursos gráficos y capturas.</li>
    <li><strong>/vendor:</strong> Librerías externas (mPDF).</li>
    <li><strong>conexion.php:</strong> Archivo central de conexión a BD.</li>
    <li><strong>Archivos .php:</strong> Controladores de cada módulo (autores.php, libros.php, ventas.php, etc).</li>
</ul>
<div class="img-container">
    <img src="img/estructura_vscode.png" class="img-diagram" style="width: 40%;"> 
    <div class="caption">Imagen 5: Estructura de directorios en VS Code.</div>
</div>
';

$html_infra = '
<h2>4. Infraestructura y Herramientas</h2>
<ul>
    <li><strong>Entorno de Ejecución:</strong> XAMPP v8.2 (Apache Web Server + MariaDB Database).</li>
    <li><strong>Lenguajes:</strong> PHP 8.2 (Backend), HTML5/CSS3/JS (Frontend).</li>
    <li><strong>Librerías:</strong> mPDF (Generación de reportes).</li>
    <li><strong>IDE:</strong> Visual Studio Code.</li>
    <li><strong>Diagramación:</strong> Draw.io y C4 Model.</li>
</ul>
';

$html_capturas = '
<pagebreak />
<h2>5. Evidencia de Funcionamiento</h2>

<h3>5.1 Acceso al Sistema</h3>
<div class="img-container">
    <img src="img/captura_login.png" class="img-screenshot">
    <div class="caption">Imagen 6: Pantalla de Inicio de Sesión.</div>
</div>

<h3>5.2 Dashboard Principal</h3>
<div class="img-container">
    <img src="img/captura_dashboard.png" class="img-screenshot">
    <div class="caption">Imagen 7: Panel de control del Administrador.</div>
</div>

<pagebreak />

<h3>5.3 Módulo de Ventas (Validación de Stock)</h3>
<div class="img-container">
    <img src="img/captura_venta.png" class="img-screenshot">
    <div class="caption">Imagen 8: Registro de venta con validación de inventario en tiempo real.</div>
</div>

<h3>5.4 Reportes Generados</h3>
<div class="img-container">
    <img src="img/captura_recibo.png" class="img-screenshot">
    <div class="caption">Imagen 9: Recibo PDF generado automáticamente.</div>
</div>

<div class="img-container">
    <img src="img/captura_stock.png" class="img-screenshot">
    <div class="caption">Imagen 10: Reporte de Stock global disponible.</div>
</div>
';

$mpdf->WriteHTML($css . $html_portada . $html_c4 . $html_flujos . $html_codigo . $html_infra . $html_capturas);

$mpdf->Output('Documentacion_BiblioTec.pdf', 'I');
?>