<?php
declare(strict_types=1);

namespace Carica\Localize\Serializer {

  use Carica\Localize\Serializer\Report\CreateUnitMessage;
  use Carica\Localize\Serializer\Report\EqualUnitMessage;
  use Carica\Localize\Serializer\Report\ReportMessage;
  use Carica\Localize\Serializer\Report\ChangeUnitMessage;
  use Carica\Localize\TranslationUnit;
  use Carica\Localize\Serializer\Report\Report;

  class XliffSerializer implements Serializer {

    private const XMLNS_XLIFF = 'urn:oasis:names:tc:xliff:document:1.2';

    public function getFileName(string $name, string $targetLanguage = ''): string {
      return $name.($targetLanguage ? '.'.$targetLanguage : '').'.xlf';
    }

    public function serializeToString(
      iterable $units,
      string $sourceLanguage,
      string $targetLanguage = '',
      string $mergeFromFile = '',
      ?Report $report = null,
    ): string {
      $previousDocument = new \DOMDocument();
      if ($mergeFromFile) {
        try {
          @$previousDocument->load($mergeFromFile);
        } catch (\Throwable $e) {
        }
      }
      $xpath = new \DOMXPath($previousDocument);
      $xpath->registerNamespace('xliff', self::XMLNS_XLIFF);
      $body = $this->createDocument($sourceLanguage, $targetLanguage);
      foreach ($units as $unit) {
        $previousUnit = $this->firstNodeOf(
          '(//xliff:trans-unit[@id="'.$unit->id.'"])[1]', $previousDocument, $xpath
        );
        $this->writeUnit($unit, $body, (bool)$targetLanguage, $report, $previousUnit);
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
      $document->formatOutput = true;
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
      $file->setAttribute('original', 'carica.localize');
      $file->append(
        $body = $document->createElementNS(self::XMLNS_XLIFF, 'body')
      );
      return $body;
    }

    private function writeUnit(
      TranslationUnit $unit,
      \DOMElement $body,
      bool $withTarget,
      ?Report $report = null,
      ?\DOMElement $current = null,
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
            $report?->message(new EqualUnitMessage($unit));
            $target->setAttribute('state', $currentState);
          } else {
            $report?->message(new ChangeUnitMessage($unit));
            $target->setAttribute('state', 'needs-l10n');
          }
        } else {
          $report?->message(new CreateUnitMessage($unit));
        }
      } else {
        if ($current) {
          $xpath = new \DOMXPath($current->ownerDocument);
          $xpath->registerNamespace('xliff', self::XMLNS_XLIFF);
          $currentSource = $xpath->evaluate('string(xliff:source)', $current);
          $currentMeaning = $xpath->evaluate('string(xliff:note[@from="meaning"])', $current);
          if ($currentSource === $unit->source && $currentMeaning === $unit->meaning) {
            $report?->message(new EqualUnitMessage($unit));
          } else {
            $report?->message(new ChangeUnitMessage($unit));
          }
        } else {
          $report?->message(new CreateUnitMessage($unit));
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
