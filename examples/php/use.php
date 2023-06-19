<?php
require __DIR__.'/../../vendor/autoload.php';

use Carica\Localize\Localize;
use Carica\Localize\Messages\XliffFileMessages;

Localize::messages(
  new XliffFileMessages(__DIR__.'/example.de.xlf')
);

echo Localize::message('Example', meaning: 'test'), "\n";
echo Localize::message('Example', id: 'test.id'), "\n";
echo Localize::message('Example: {foo}', values: ['foo'=>'VALUE']), "\n";
