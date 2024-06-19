<?php

use PHPUnit\Framework\TestCase;
use Guenbakku\Cakepdf\Pdf;

class PdfTest extends TestCase {

    protected $output;

    public function setUp(): void {
        $this->output = dirname(__FILE__)."/pdfTest.pdf";
        if (is_file($this->output)) {
            unlink($this->output);
        }
    }

    public function tearDown(): void {
        if (is_file($this->output)) {
            unlink($this->output);
        }
    }

    public function testInstanceSnappy() {
        $pdf = new Pdf();

        $is32bit = PHP_INT_SIZE === 4;
        if ($is32bit) {
            $binary = 'wkhtmltopdf-i386';
        } else {
            $binary = 'wkhtmltopdf-amd64';
        }

        $binary = implode(DS, array(
            VENDOR, 'bin', $binary
        ));

        $Snappy = new Knp\Snappy\Pdf($binary);

        $this->assertEquals($Snappy, $pdf->getSnappy());
    }

    public function testSetVendorManually() {
        $pdf = new Pdf('vendor');
        $this->assertEquals(true, $pdf instanceof Pdf);
    }

    public function testNotFoundVendorFullPath() {
        $this->expectException(RuntimeException::class);
        new Pdf('xxx');
    }

    public function testSetOuput() {
        $pdf = new Pdf();
        $pdf->add('<p>Test</p>');
        $pdf->setOutput($this->output);
        $pdf->render();
        $this->assertEquals(true, is_file($this->output));
    }

    public function testGetOutput() {
        $pdf = new Pdf();
        $pdf->setOutput($this->output);
        $this->assertEquals($this->output, $pdf->getOutput());
    }

    public function testGenerateFileByAdd() {
        $pdf = new Pdf();
        $pdf->add('<p>Test</p>');
        $pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }

    public function testGenerateFileByAddPage() {
        $pdf = new Pdf();
        $pdf->add('<p>Test1</p>', true);
        $pdf->addPage('<p>Test2</p>');
        $pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }

    public function testOutputByAdd() {
        $pdf = new Pdf();
        $pdf->add('<p>Test</p>');
        $result = $pdf->render();
        $this->assertEquals(true, !is_null($result));
    }

    public function testOutputByAddPage() {
        $pdf = new Pdf();
        $pdf->addPage('<p>Test1</p>');
        $pdf->addPage('<p>Test2</p>');
        $result = $pdf->render();
        $this->assertEquals(true, !is_null($result));
    }

    public function testMagicCall() {
        $pdf = new Pdf();
        $pdf->setOption('orientation', 'Landscape');
        $pdf->add('<p>Test</p>');
        $pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }
}
