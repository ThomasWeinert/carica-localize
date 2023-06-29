# Carica Localize

Allows to localize messages in a PHP application. It uses the `ext/intl` MessageFormatter to 
allow ICU message syntax.

The goal is to have a mostly automated way to maintain the localized texts continuously.

## Requirements

* PHP >= 8.2
* ext/intl
* ext/dom
* ext/xsl (when using XSL features)
* nikic/php-parser

## Define

The library provides functions/templates for different contexts (PHP and XSL at the moment). 
The arguments are the same.

* `message` - The message text itself. ICU message syntax is supported.
* `meaning` - A category label for the message. (`financial` or `furniture` for `bank`)
* `id` - The message identifier (Auto generated from `message` and `meaning` if not provided.)
* `description` - A description of the message for translators.
* `values` - Placeholder values for the message.

It is mandatory to use string literals for all arguments except the `values`. 
!DO NOT USE VARIABLES!. The extractor will not work otherwise.

### PHP

Use the static call `Localize::message` to define your texts.

```php
use Carica\Localize\Localize;

echo Localize::message('Example', meaning: 'test'), "\n";
echo Localize::message('Example', id: 'test.id'), "\n";
echo Localize::message('Example: {foo}', values: ['foo'=>'VALUE']), "\n";
```

### XSL

You need to register the stream wrapper and the callback function.

```php
use Carica\Localize\XSL\Callbacks;

$processor = new \XSLTProcessor();
$processor->registerPHPFunctions([Callbacks::XSL_CALLBACK]);
```

The file loader allows your template to import the localize template. The
callback function provides connection to the `ext/intl` MessageFormatter.

The message template is `{urn:carica:localize}message`. It uses a namespace to 
avoid conflicts.

```
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:localize="urn:carica:localize"
  exclude-result-prefixes="localize">

  <xsl:import href="../vendor/carica/localize/src/localize.xsl"/>

  <xsl:template match="/">
    <div>
      <xsl:call-template name="localize:message">
        <xsl:with-param name="message">Example</xsl:with-param>
      </xsl:call-template>
    </div>
    <div>
      <xsl:call-template name="localize:message">
        <xsl:with-param name="id">example.other</xsl:with-param>
        <xsl:with-param name="message">Example: {foo}</xsl:with-param>
        <xsl:with-param name="values">
          <foo>PARAMETER</foo>
        </xsl:with-param>
      </xsl:call-template>
    </div>
  </template>
  
</xsl:stylesheet>
```

## Extract + Merge

Define an extraction for your project as an PHP helper script.

```php
use Carica\Localize\Extraction;
use Carica\Localize\Serializer\Report\ConsoleReport;

$sourceLanguage = 'en';
$targetLanguages = ['de', 'fr'];

$messages = new Extraction(
  __DIR__.'../src/',
  [
    '(\\.php$)i' => new Extraction\PHPStaticFunctionExtractor()
    '(\\.xsl$)i' => new Extraction\XSLExtractor()
  ],
  function (Extraction\ConflictException $e) {
    echo $e->getMessage(), "\n";
  }
);
$messages->output(
  __DIR__.'../src/l10n',
  $sourceLanguage,
  $targetLanguages,
  'example',
  report: new ConsoleReport()
);
```

Running the script will generate XLIFF files for the source and each target
language in the output directory. If the files exists they will be updated and 
existing translation in the target language files will be merged.

## Load Translations

Add some logic to your PHP/XSL to load the messages for the current language.

### PHP

```php
use Carica\Localize\Localize;
use Carica\Localize\Messages\XliffFileMessages;

Localize::messages(
  new XliffFileMessages(__DIR__.'/example.de.xlf')
);
```

### XSL

XLIFF files are XML, so they can be loaded using the `document()` function.
`{urn:carica:localize}messages-file` helps to compile the message file name.

```xsl
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:localize="urn:carica:localize"
  exclude-result-prefixes="localize">

  <xsl:import href="carica-localize://messages"/>

  <xsl:param name="LOCALIZE_LANGUAGE">de</xsl:param>
  <xsl:param 
    name="LOCALIZE_MESSAGES" 
    select="document(localize:messages-file($LOCALIZE_LANGUAGE, './example'))"/>

</xsl:stylesheet>
```

### Translate

For local translations you can use a tool like [POEdit](https://poedit.net/). 

If you work in a team I suggest taking a look at [Weblate](https://weblate.org/de/). 
Weblate can connect to your GIT repository.
