<?php


$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Symfony\\' => array($baseDir . '/'),
    'Sabberworm\\' => array($baseDir . '/'),
    'Psr\\Container\\' => array($vendorDir . '/psr/container/src'),
    'Pelago\\' => array($baseDir . '/'),
    'AcyMailing\\' => array($baseDir . '/'),
    'AcyMailerPhp\\' => array($baseDir . '/'),
);
