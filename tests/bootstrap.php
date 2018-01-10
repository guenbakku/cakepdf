<?php
/**
 * Test suite bootstrap for Cakeenv.
 *
 * This function is used to find the location of CakePHP whether CakePHP
 * has been installed as a dependency of the plugin, or the plugin is itself
 * installed as a dependency of an application.
 */
$findRoot = function ($root, $vendor) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root.'/'.$vendor)) {
            return $root;
        }
    } while ($root !== $lastRoot);

    return null;
};

foreach (array('vendor', 'vendors') as $vendor) {
    $root = $findRoot(__FILE__, $vendor);
    if (!empty($root)) {
        break;
    }
}
if (empty($root)) {
    throw new Exception("Cannot find the root of the application, unable to run tests");
}
unset($findRoot);

chdir($root);

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', $root);
define('VENDOR', ROOT.DS.$vendor);

require VENDOR.DS.'autoload.php';
