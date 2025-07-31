<?php
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Opções de renderização
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Carrega o HTML (pode ser por buffer ou string)
ob_start();
include 'boletim.php'; // ou o HTML gerado dinamicamente
$html = ob_get_clean();

$dompdf->loadHtml($html);

// Define o tamanho do papel A4 e orientação (portrait)
$dompdf->setPaper('A4', 'landscape');

// Renderiza o HTML em PDF
$dompdf->render();

// Força download
$dompdf->stream("boletim.pdf", ["Attachment" => true]);
?>
