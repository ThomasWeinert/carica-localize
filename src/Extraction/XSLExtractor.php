<?php

declare(strict_types=1);

namespace I18N\Messages\Extraction {

  use I18N\Messages\TranslationUnit;

  readonly class XSLExtractor implements FileExtractor {

    private const XMLNS_XSL = 'http://www.w3.org/1999/XSL/Transform';

    public function __construct(
      private string $namespaceURI = 'urn:i18n'
    ) {
    }

    public function extract(string $target): \Iterator {
      $document = new \DOMDocument();
      $document->load($target);
      $xpath = new \DOMXPath($document);
      $xpath->registerNamespace('xsl', self::XMLNS_XSL);
      $calls = $xpath->evaluate('//xsl:call-template[contains(@name, ":message")]');
      foreach ($calls as $call) {
        [$prefix, $localName] = explode(':', $call->getAttribute('name'));
        $namespaceURI = $call->lookupNamespaceURI($prefix);
        if (!($localName === 'message' && $namespaceURI === $this->namespaceURI)) {
          continue;
        }
        yield new TranslationUnit(
          $xpath->evaluate('string(xsl:with-param[@name="id"])', $call),
          $xpath->evaluate('string(xsl:with-param[@name="message"])', $call),
          $xpath->evaluate('string(xsl:with-param[@name="meaning"])', $call),
          $xpath->evaluate('string(xsl:with-param[@name="description"])', $call),
          $target,
          $call->getLineNo()
        );
      }
    }
  }

}