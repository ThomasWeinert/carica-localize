<?php
require __DIR__.'/../../vendor/autoload.php';

use I18N\Messages\Extraction;

$sourceLanguage = 'en';
$targetLanguages = ['de', 'fr'];

$messages = new Extraction(
  __DIR__,
  [
    '(\\.php)i' => new Extraction\PHPExtractor()
  ]
);
$messages->output(
  __DIR__,
  'en',
  ['de', 'fr'],
  'example'
);
