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
        $Pdf = new Pdf();

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

        $this->assertEquals($Snappy, $Pdf->getSnappy());
    }

    public function testSetVendorManually() {
        $Pdf = new Pdf('vendor');
        $this->assertEquals(true, $Pdf instanceof Pdf);
    }

    public function testNotFoundVendorFullPath() {
        $this->expectException(RuntimeException::class);
        new Pdf('xxx');
    }

    public function testSetOuput() {
        $Pdf = new Pdf();
        $Pdf->add('<p>Test</p>');
        $Pdf->setOutput($this->output);
        $Pdf->render();
        $this->assertEquals(true, is_file($this->output));
    }

    public function testGetOutput() {
        $Pdf = new Pdf();
        $Pdf->setOutput($this->output);
        $this->assertEquals($this->output, $Pdf->getOutput());
    }

    public function testGenerateFileByAdd() {
        $Pdf = new Pdf();
        $Pdf->add('<p>Test</p>');
        $Pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }

    public function testGenerateFileByAddPage() {
        $Pdf = new Pdf();
        $Pdf->add('<p>Test1</p>', true);
        $Pdf->addPage('<p>Test2</p>');
        $Pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }

    public function testOutputByAdd() {
        $Pdf = new Pdf();
        $Pdf->add('<p>Test</p>');
        $result = $Pdf->render();
        $this->assertEquals(true, !is_null($result));
    }

    public function testOutputByAddPage() {
        $Pdf = new Pdf();
        $Pdf->addPage('<p>Test1</p>');
        $Pdf->addPage('<p>Test2</p>');
        $result = $Pdf->render();
        $this->assertEquals(true, !is_null($result));
    }

    public function testMagicCall() {
        $Pdf = new Pdf();
        $Pdf->setOption('orientation', 'Landscape');
        $Pdf->add('<p>Test</p>');
        $Pdf->render($this->output);
        $this->assertEquals(true, is_file($this->output));
    }
}
