<?php
require __DIR__.'/../../vendor/autoload.php';

use I18N\Messages\XSL\Callbacks;
use I18N\Messages\XSL\FileLoader;

FileLoader::register();
$processor = new \XSLTProcessor();
$processor->registerPHPFunctions([Callbacks::XSL_CALLBACK]);

$template = new \DOMDocument();
$template->load('template.xsl');

$processor->importStylesheet($template);

$document = new \DOMDocument();
$document->loadXML('<values><foo>DOCUMENT VALUE</foo></values>');

echo $processor->transformToXml($document);
