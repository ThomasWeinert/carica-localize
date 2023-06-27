<?php
require __DIR__.'/../../vendor/autoload.php';

use Carica\Localize\Extraction;
use Carica\Localize\Serializer\Report\ConsoleReport;

$sourceLanguage = 'en';
$targetLanguages = ['de', 'fr'];

$messages = new Extraction(
  __DIR__,
  [
    '(\\.xsl$)i' => new Extraction\XSLExtractor()
  ],
  function (Extraction\ConflictException $e) {
    echo $e->getMessage(), "\n";
  }
);
$messages->output(
  __DIR__,
  $sourceLanguage,
  $targetLanguages,
  'example',
  report: new ConsoleReport()
);
