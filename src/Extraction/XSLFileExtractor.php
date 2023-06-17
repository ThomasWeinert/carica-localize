<?php

declare(strict_types=1);

namespace I18N\Messages\Extraction {

  use I18N\Messages\TranslationUnit;

  class XSLFileExtractor implements FileExtractor {

    private const XMLNS_XSL = '';
    private const XMLNS_I18N = 'urn:i18n';

    public function extract(string $fileName): \Iterator {
      $document = new \DOMDocument();
      $document->load($fileName);
      $xpath = new \DOMXPath($document);
      $xpath->registerNamespace('xsl', self::XMLNS_XSL);
      $calls = $xpath->evaluate('//xsl:call-template[contains(@name, ":message")]');
      foreach ($calls as $call) {
        [$prefix, $localName] = explode(':', $call->getAttribute('name'));
        $namespaceURI = $call->lookupNamespaceURI($prefix);
        if (!($localName === 'message' && $namespaceURI === self::XMLNS_I18N)) {
          continue;
        }
        yield new TranslationUnit(
          $xpath->evaluate('string(xsl:with-param[@name="message"])', $call),
          $xpath->evaluate('string(xsl:with-param[@name="identifier"])', $call),
          $xpath->evaluate('string(xsl:with-param[@name="meaning"])', $call),
          $xpath->evaluate('string(xsl:with-param[@name="description"])', $call),
          $fileName,
          $call->getLineNo()
        );
      }
    }
  }

}
