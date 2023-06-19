<?php

namespace Carica\Localize\Messages {

  use Carica\Localize\TranslationUnit;

  class XliffFileMessages implements Messages {

    const XMLNS_XLIFF = 'urn:oasis:names:tc:xliff:document:1.2';

    private ?\DOMDocument $_document = null;

    public function __construct(
      private readonly string $fileName
    ) {

    }

    private function load(): \DOMDocument {
      if (!($this->_document instanceof \DOMDocument)) {
        $this->_document = new \DOMDocument();
        $this->_document->load($this->fileName);
      }
      return $this->_document;
    }

    public function get(string $id): ?string {
      $document = $this->load();
      $xpath = new \DOMXPath($document);
      $xpath->registerNamespace(
        'xliff', self::XMLNS_XLIFF
      );
      foreach (
        $xpath->evaluate(
        '(//xliff:trans-unit[@id="'.$id.'"])[1]', $document) as $unit
      ) {
        $pattern = (
          $xpath->evaluate('string(xliff:target)', $unit)
            ?: $xpath->evaluate('string(xliff:source)', $unit)
        );
        return $pattern;
      }
      return null;
    }
  }
}
