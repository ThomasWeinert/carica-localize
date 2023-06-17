<?php
declare(strict_types=1);

namespace I18N\Messages\Serializer {

  use I18N\Messages\TranslationUnit;

  class XliffSerializer {

    private const XMLNS_XLIFF = 'urn:oasis:names:tc:xliff:document:1.2';

    public function serializeToString(
      \Iterator $units,
      string $sourceLanguage,
      string $targetLanguage = '',
      string $mergeFromFile = '',
    ): string {
      $previousDocument = new \DOMDocument();
      if ($targetLanguage && $mergeFromFile) {
        $previousDocument->load($mergeFromFile);
      }
      $xpath = new \DOMXPath($previousDocument);
      $xpath->registerNamespace('xliff', self::XMLNS_XLIFF);
      $body = $this->createDocument($sourceLanguage, $targetLanguage);
      foreach ($units as $unit) {
        $previousUnit = $this->firstNodeOf(
          '(//xliff:trans-unit[@id="'.$unit->id.'"])[1]', $previousDocument, $xpath
        );
        $this->writeUnit($unit, $body, (bool)$targetLanguage, $previousUnit);
      }
      return $body->ownerDocument->saveXML();
    }

    private function firstNodeOf(
      string $expression,
      \DOMNode $context,
      \DOMXpath $xpath
    ): ?\DOMNode {
      $nodes = $xpath->evaluate($expression, $context);
      return $nodes[0] ?? null;
    }

    private function createDocument(
      string $sourceLanguage,
      string $targetLanguage = ''
    ): \DOMElement {
      $document = new \DOMDocument('1.0', 'UTF-8');
      $document->append(
        $xliff = $document->createElementNS(self::XMLNS_XLIFF, 'xliff')
      );
      $xliff->setAttribute('version', '1.2');
      $xliff->append(
        $file = $document->createElementNS(self::XMLNS_XLIFF, 'file')
      );
      $file->setAttribute('source-language', $sourceLanguage);
      if ($targetLanguage) {
        $file->setAttribute('target-language', $targetLanguage);
      }
      $file->setAttribute('datatype', 'plaintext');
      $file->setAttribute('original', 'xsl.template');
      $file->append(
        $body = $document->createElementNS(self::XMLNS_XLIFF, 'body')
      );
      return $body;
    }

    private function writeUnit(
      TranslationUnit $unit,
      \DOMElement $body,
      bool $withTarget,
      ?\DOMElement $current = null
    ) {
      $document = $body->ownerDocument;
      $body->append(
        $transUnit = $document->createElementNS(self::XMLNS_XLIFF, 'trans-unit')
      );
      $transUnit->setAttribute('id', $unit->id);
      $transUnit->append(
        $source = $document->createElementNS(self::XMLNS_XLIFF, 'source')
      );
      $source->textContent = $unit->source;
      if ($withTarget) {
        $transUnit->append(
          $target = $document->createElementNS(self::XMLNS_XLIFF, 'target')
        );
        $target->textContent = $unit->source;
        $target->setAttribute('state', 'new');
        if ($current) {
          $xpath = new \DOMXPath($current->ownerDocument);
          $xpath->registerNamespace('xliff', self::XMLNS_XLIFF);
          $currentSource = $xpath->evaluate('string(xliff:source)', $current);
          $currentState = $xpath->evaluate('string(xliff:target/@state)', $current);
          $currentMeaning = $xpath->evaluate('string(xliff:note[@from="meaning"])', $current);
          $target->textContent = $xpath->evaluate('string(xliff:target)', $current);
          if ($currentSource === $unit->source && $currentMeaning === $unit->meaning) {
            $target->setAttribute('state', $currentState);
          } else {
            $target->setAttribute('state', 'needs-l10n');
          }
        }
      }
      if ($unit->meaning) {
        $transUnit->append(
          $note = $document->createElementNS(self::XMLNS_XLIFF, 'note')
        );
        $note->setAttribute('priority', '1');
        $note->setAttribute('from', 'meaning');
        $note->textContent = $unit->meaning;
      }
      if ($unit->description) {
        $transUnit->append(
          $note = $document->createElementNS(self::XMLNS_XLIFF, 'note')
        );
        $note->setAttribute('priority', '2');
        $note->setAttribute('from', 'description');
        $note->textContent = $unit->description;
      }
    }
  }
}
