# Cakepdf

Easily generate PDFs from your HTML content.

This versatile tool can be used as:

* A CakePHP plugin: Seamlessly integrate PDF generation into your CakePHP 2.x and above applications.  
* A standalone library: Use it independently outside of CakePHP for maximum flexibility.  

Powered by Snappy (https://github.com/KnpLabs/snappy)  
I want to give a big thanks to the creators of the fantastic Snappy library, which makes this functionality possible.

## Installation

You can install this library using [composer](http://getcomposer.org).

```
composer require guenbakku/cakepdf
```

## Usage

```php
<?php

use Guenbakku\Cakepdf\Pdf;

// Instance object.
// Different with original Snappy, this libary comes with 
// wkhtmltopdf binary which is installed as composer dependencies. 
// So following setup also automatically set wkhtmltopdf binary's path 
// corresponding to current processor's structure (32 or 64 bit).
$Pdf = new Pdf();

// Add html to render to pdf.
// Break page will be inserted automatically by wkhtmltopdf.
$html = '<p>Long html for long pdf</p>';
$Pdf->add($html);

// Add each html as a seperated pdf page.
$page = '<p>Page</p>';
$Pdf->addPage($page)
    ->addPage($page);

// Render output to display in browser.
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="file.pdf"');
$result = $Pdf->render();
echo $result;

// Or render output to pdf file.
$output = '/tmp/cakepdf.pdf';
$Pdf->render($output);

// Set options for wkhtmltopdf.
// Basically same with Snappy's interface.
$Pdf = new Pdf();
$Pdf->setOption('page-size', 'A4')
    ->setOption('orientation', 'Landscape');
```
