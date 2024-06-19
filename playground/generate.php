<?php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('PG_PATH')) {
    define('PG_PATH', dirname(__FILE__));
}

if (!defined('HTML_PATH')) {
    define('HTML_PATH', PG_PATH . DS . 'html');
}

require_once dirname(PG_PATH) . DS . 'vendor' . DS . 'autoload.php';

use Guenbakku\Cakepdf\Pdf;

$pdf = new Pdf();

$page = '<p>Page</p>';
$pdf->addPage($page)
    ->addPage($page);

$pdf->setOption('page-size', 'A4')
    ->setOption('orientation', 'Landscape')
    ->render(PG_PATH . DS . 'simple.pdf', [], true);

// ---------

$pdf = new Pdf();
$page = file_get_contents(HTML_PATH . DS . 'table.html');
$pdf->addPage($page);
$pdf->render(PG_PATH . DS . 'table.pdf', [], true);

// ---------

$pdf = new Pdf();
$page = file_get_contents(HTML_PATH . DS . 'README.html');
$pdf->addPage($page);
$pdf->render(PG_PATH . DS . 'readme.pdf', [], true);
