<?php
/**
 * Easily generate PDFs from your HTML content.
 * This is a simple wrapper of awesome library "Knp\Snappy\Pdf".
 *
 * @copyright   NVB
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Guenbakku\Cakepdf;

use RuntimeException;

class Pdf {
    /**
     * Path to vendor's folder
     * @var string[]
     */
    protected $vendor;

    /**
     * Snappy object
     * @var \Knp\Snappy\Pdf
     */
    protected $snappy;

    /**
     * Path of wkhtmltopdf executable binary
     * @var string
     */
    protected $binary;

    /**
     * Html that will be rendered to pdf
     * @var string
     */
    protected $html;

    /**
     * Path of output pdf
     * @var string
     */
    protected $output;

    protected $pageStyle = '
        <style type="text/css">
            .page-container {
                overflow: hidden;
                page-break-before: always;
                page-break-after: always;
            }
            .page-container:last-child {
                page-break-after: auto;
            }
        </style>';

    protected $pageContainer = '<div class="page-container">%s</div>';

    /**
     * @param string[]|string $vendor
     */
    public function __construct($vendor = ['vendor', 'vendors']) {
        $this->_setVendor($vendor);
        $this->_setBinary();
        $this->snappy = new \Knp\Snappy\Pdf($this->binary);
    }

    /**
     * Set output path of pdf file
     *
     * @param string $path path of output file
     * @return Pdf this object
     */
    public function setOutput(string $path): Pdf {
        $this->output = $path;
        return $this;
    }

    /**
     * Get output path of pdf file
     *
     * @return string
     */
    public function getOutput(): ?string {
        return $this->output;
    }

    /**
     * Add html as separated pdf page
     *
     * @param string $html html string of a page
     * @return Pdf this object
     */
    public function addPage(string $html): Pdf {
        $page = sprintf($this->pageContainer, $html);
        if (strpos($this->html, $this->pageStyle) === false) {
            $this->html .= $this->pageStyle . $page;
        } else {
            $this->html .= $page;
        }
        return $this;
    }

    /**
     * Add html to generate pdf
     *
     * @param string $html html string
     * @param bool $breakPage add as separated page or not
     * @return Pdf this object
     */
    public function add(string $html, bool $breakPage = false): Pdf {
        if ($breakPage === false) {
            $this->html .= $html;
        } else {
            $this->addPage($html);
        }
        return $this;
    }

    /**
     * Render pdf from added html
     *
     * @param string $output path of output file
     * @param array $options an array of options for this generation only
     * @param bool $overwrite overwrite existing file or not. Only take effect if output is not null
     * @return string|null pdf content or null if output to file
     */
    public function render(?string $output = null, array $options = [], bool $overwrite = false) {
        if ($output !== null) {
            $this->setOutput($output);
        }
        if (empty($this->getOutput())) {
            return $this->snappy->getOutputFromHtml($this->html, $options);
        } else {
            return $this->snappy->generateFromHtml($this->html, $this->getOutput(), $options, $overwrite);
        }
    }

    /**
     * Return Snappy object
     *
     * @return  object Snappy object
     */
    public function getSnappy(): \Knp\Snappy\Pdf {
        return $this->snappy;
    }

    /**
     * This magic is used for calling methods of Snappy object
     */
    public function __call($name, $arguments) {
        $this->snappy->$name(...$arguments);
        return $this;
    }

    /**
     * Find root of application
     *
     * @return string path of root
     */
    protected function _findVendorFullPath(): string {
        $findRoot = function ($vendor) {
            $root = __FILE__;
            do {
                $lastRoot = $root;
                $root = dirname($root);
                if (is_dir($root . DIRECTORY_SEPARATOR . $vendor)) {
                    return $root;
                }
            } while ($root !== $lastRoot);

            return null;
        };

        foreach ($this->vendor as $vendor) {
            $root = $findRoot($vendor);
            if (!empty($root)) {
                return implode(DIRECTORY_SEPARATOR, array(
                    $root, $vendor
                ));
            }
        }

        throw new RuntimeException('Cannot find the root of the application');
    }

    /**
     * Set path to vendor's folder
     *
     * @param string[]|string $vendor path(s) of vendor's folder
     * @return Pdf this object
     */
    protected function _setVendor($vendor): Pdf {
        if (!is_array($vendor)) {
            $vendor = array($vendor);
        }
        $this->vendor = $vendor;
        return $this;
    }

    /**
     * Set path of wkhtmltopdf.
     * This will set the correct wkhtmltopdf binary
     * corresponding to structure of current processor (32 or 64 bit)
     *
     * @return string path of binary
     */
    protected function _setBinary(): string {
        $is32bit = PHP_INT_SIZE === 4;
        if ($is32bit) {
            $binary = 'wkhtmltopdf-i386';
        } else {
            $binary = 'wkhtmltopdf-amd64';
        }

        $vendorFullPath = $this->_findVendorFullPath();
        $binary = implode(DIRECTORY_SEPARATOR, array(
            $vendorFullPath, 'bin', $binary
        ));

        if (is_file($binary) || is_link($binary)) {
            $this->binary = $binary;
        }

        return $this->binary;
    }
}
