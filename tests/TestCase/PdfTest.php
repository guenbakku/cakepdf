<?php

use PHPUnit\Framework\TestCase;
use Guenbakku\Cakepdf\Pdf;

class PdfTest extends TestCase {
        
    public function setUp() {
        $this->Pdf = new Pdf();
        $this->output = dirname(__FILE__)."/pdfTest.pdf";
        if (is_file($this->output)) {
            unlink($this->output);
        }
    }
    
    public function tearDown() {
        if (is_file($this->output)) {
            unlink($this->output);
        }
    }
    
    public function testInstanceSnappy() {
        $is32bit = PHP_INT_SIZE === 4;
        if ($is32bit) {
            $binary = 'wkhtmltopdf-i386';
        } else {
            $binary = 'wkhtmltopdf-amd64';
        }
        
        $binary = implode(DS, array(
            ROOT, 'vendors', 'bin', $binary
        ));
        
        $Snappy = new Knp\Snappy\Pdf($binary);
        
        $this->assertEquals($Snappy, $this->Pdf->getSnappy());
    }
    
    public function testGenerateFileByAdd() {
        $this->Pdf->add('<p>Test</p>');
        $this->Pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }
    
    public function testGenerateFileByAddPage() {
        $this->Pdf->add('<p>Test1</p>', true);
        $this->Pdf->addPage('<p>Test2</p>');
        $this->Pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }
    
    public function testOutputByAdd() {
        $this->Pdf->add('<p>Test</p>');
        $result = $this->Pdf->render();
        $this->assertEquals(true, !is_null($result));
    }
    
    public function testOutputByAddPage() {
        $this->Pdf->addPage('<p>Test1</p>');
        $this->Pdf->addPage('<p>Test2</p>');
        $result = $this->Pdf->render();
        $this->assertEquals(true, !is_null($result));
    }
    
    public function testMagicCall() {
        $this->Pdf->setOption('orientation', 'Landscape');
        $this->Pdf->add('<p>Test</p>');
        $this->Pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }
}