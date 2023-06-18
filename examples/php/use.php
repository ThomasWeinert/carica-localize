<?php
require __DIR__.'/../../vendor/autoload.php';

use I18N\Messages\Localize;

echo Localize::message('Example', meaning: 'test');
