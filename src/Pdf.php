<?php
/**
 * CakePHP plugin for converting HTML to PDF.
 * This plugin could not be made without awesome library "Knp\Snappy\Pdf".
 * Thankful to the authors of library Snappy.
 * 
 * @copyright   NVB
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Guenbakku\Cakepdf;

use RuntimeException;

class Pdf {
    // Path to vendor's folder
    protected $vendor;
    
    // Snappy object
    protected $Snappy;
    
    // Path of wkhtmltopdf executable binary
    protected $binary;
    
    // Html that will be rendered to pdf
    protected $html;
    
    // Path of output pdf
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
    
    public function __construct($vendor = array('vendor', 'vendors')) {
        $this->_setVendor($vendor);
        $this->_setBinary();
        $this->Snappy = new \Knp\Snappy\Pdf($this->binary);
    }
    
    /**
     * Set output path of pdf file
     *
     * @param   string: output path
     * @return  object: this object
     */
    public function setOutput($path) {
        $this->output = $path;
        return $this;
    }

    /**
     * Get output path of pdf file
     *
     * @param   void
     * @return  string
     */
    public function getOutput() {
        return $this->output;
    }

    /**
     * Add html as seperated pdf page
     *
     * @param   string: html
     * @return  object: this object
     */
    public function addPage($html) {
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
     * @param   string: html
     * @param   bool: add as seperated page or not
     * @return  object: this object
     */
    public function add($html, $breakPage = false) {
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
     * @param   string: output path
     * @param   array: an array of options for this generation only
     * @param   bool: overwrite existing file or not. 
     *                Only take effect if output is not null
     * @return  string|null: pdf content or null if output to file
     */
    public function render($output = null, $options = array(), $overwrite = false) {
        if ($output !== null) {
            $this->setOutput($output);
        }
        if (empty($this->getOutput())) {
            return $this->Snappy->getOutputFromHtml($this->html, $options);
        } else {
            return $this->Snappy->generateFromHtml($this->html, $this->getOutput(), $options, $overwrite);
        }
    }
    
    /**
     * Return Snappy object
     *
     * @param   void
     * @return  object: Snappy object
     */
    public function getSnappy() {
        return $this->Snappy;
    }
    
    /**
     * This magic is used for calling methods of Snappy object
     */
    public function __call($name, $arguments) {
        $this->Snappy->$name(...$arguments);
        return $this;
    }
    
    /**
     * Find root of application
     *
     * @param   string: path of current script
     * @return  string: path of root
     */
    protected function _findVendorFullPath() {
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
     * @param   string|array: path(s) to vendor's folder
     * @return  object: this object
     */
    protected function _setVendor($vendor) {
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
     * @param   void
     * @return  string: path of binary
     */
    protected function _setBinary() {
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
