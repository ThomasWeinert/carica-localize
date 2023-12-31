<?php
require __DIR__.'/../../vendor/autoload.php';

use Carica\Localize\Extraction;

$sourceLanguage = 'en';
$targetLanguages = ['de', 'fr'];

$messages = new Extraction(
  __DIR__,
  [
    '(\\.php)i' => new Extraction\PHPStaticFunctionExtractor()
  ]
);
$messages->output(
  __DIR__,
  $sourceLanguage,
  $targetLanguages,
  'example'
);
