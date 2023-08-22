<?php

declare(strict_types=1);

namespace Carica\Localize\Extraction {

  use Carica\Localize\TranslationUnit;
  use Carica\Localize\TranslationUnitDataType;

  readonly class XSLExtractor implements FileExtractor {

    private const XMLNS_XSL = 'http://www.w3.org/1999/XSL/Transform';

    public function __construct(
      private string $namespaceURI = 'urn:carica:localize'
    ) {
    }

    public function extract(\SplFileInfo|string $target): \Iterator {
      $document = new \DOMDocument();
      $document->load((string)$target);
      $xpath = new \DOMXPath($document);
      $xpath->registerNamespace('xsl', self::XMLNS_XSL);
      $calls = $xpath->evaluate('//xsl:call-template[contains(@name, ":message")]');
      foreach ($calls as $call) {
        [$prefix, $localName] = explode(':', $call->getAttribute('name'));
        $namespaceURI = $call->lookupNamespaceURI($prefix);
        if (!($localName === 'message' && $namespaceURI === $this->namespaceURI)) {
          continue;
        }
        $dataType = TranslationUnitDataType::tryFrom(
          trim(
            $xpath->evaluate('string(xsl:with-param[@name="type"])', $call)
          )
        ) ?? TranslationUnitDataType::PlainText;
        if (
          $dataType === TranslationUnitDataType::Html ||
          $dataType === TranslationUnitDataType::XHtml
        ) {
          $source = $this->normalizeSpace(
            implode(
              '',
              array_map(
                static function (\DOMNode $node) use ($document) {
                  return $document->saveXML($node);
                },
                iterator_to_array(
                  $xpath->evaluate('xsl:with-param[@name="message"]/node()', $call)
                )
              )
            )
          );
        } else {
          $sourceNode = $xpath->evaluate('(xsl:with-param[@name="message"])[1]', $call)[0] ?? NULL;
          if (!$sourceNode) {
            continue;
          }
          $source = $sourceNode instanceof \DOMCdataSection
            ? $sourceNode->textContent
            : $this->normalizeSpace($sourceNode->textContent);
        }
        yield new TranslationUnit(
          source: $source,
          id: $this->normalizeSpace(
            $xpath->evaluate('string(xsl:with-param[@name="id"])', $call)
          ),
          meaning: $this->normalizeSpace(
            $xpath->evaluate('string(xsl:with-param[@name="meaning"])', $call)
          ),
          description: $this->normalizeSpace(
            $xpath->evaluate('string(xsl:with-param[@name="description"])', $call)
          ),
          dataType: $dataType,
          file: (string)$target,
          line: $call->getLineNo()
        );
      }
    }

    private function normalizeSpace($value) {
      return trim(preg_replace('(\s+)', ' ', $value));
    }
  }

}
