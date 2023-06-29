<?php
require __DIR__.'/../../vendor/autoload.php';

use Carica\Localize\XSL\Callbacks;

$processor = new \XSLTProcessor();
$processor->registerPHPFunctions([Callbacks::XSL_CALLBACK]);

$template = new \DOMDocument();
$template->load(__DIR__.'/template.xsl');

$processor->importStylesheet($template);

$document = new \DOMDocument();
$document->loadXML('<values><foo>DOCUMENT VALUE</foo></values>');

echo $processor->transformToXml($document);
