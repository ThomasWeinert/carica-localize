<?php
declare(strict_types=1);

namespace Carica\Localize\XSL {

  use Carica\Localize\TranslationUnit;
  use Carica\Localize\TranslationUnitDataType;

  abstract class XSLLocalize {

    public static function generateId(string $source, string $meaning): string {
      return TranslationUnit::generateId(trim($source), trim($meaning));
    }

    public static function formatMessage(
      string $locale, string $pattern, $values = null, string $type = TranslationUnitDataType::PlainText->value
    ): string | \DOMDocumentFragment | \DOMDocument {
      $formatter = new \MessageFormatter($locale, $pattern);
      $values = self::getArrayFromArgument($values);
      $formatted = $formatter->format($values);
      $dataType = TranslationUnitDataType::tryFrom($type) ?: TranslationUnitDataType::PlainText;
      if ($dataType === TranslationUnitDataType::Html) {
        $document = new \DOMDocument();
        try {
          $document->loadHTML(
            '<?xml version="1.0" charset="utf-8" ?>'.$formatted,
            LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
          );
          foreach ($document->childNodes as $node) {
            if ($node instanceof \DOMProcessingInstruction) {
              $document->removeChild($node);
              break;
            }
          }
          return $document;
        } catch (\Throwable $e) {
          return '';
        }
      } else if ($dataType === TranslationUnitDataType::XHtml) {
        $document = new \DOMDocument();
        $fragment = $document->createDocumentFragment();
        try {
          $fragment->appendXML($formatted);
          return $fragment;
        } catch (\Throwable $e) {
          return '';
        }
      }
      return html_entity_decode($formatted, ENT_QUOTES, 'UTF-8');
    }

    private static function getArrayFromArgument($argument): array {
      if ($argument instanceof \DOMElement || $argument instanceof \DOMDocument) {
        return array_reduce(
          iterator_to_array($argument->childNodes),
          function (array $carry, \DOMNode $node) {
            if ($node instanceof \DOMElement) {
              $carry[$node->localName] = $node->textContent;
            }
            return $carry;
          },
          []
        );
      }
      if (is_array($argument) || $argument instanceof \DOMNodeList) {
        $array = is_array($argument) ? $argument : iterator_to_array($argument);
        return array_reduce(
          array_keys($array),
          function (array $carry, $key) use ($array) {
            $item = $array[$key];
            if ($item instanceof \DOMDocument) {
              $carry = array_merge(
                $carry,
                self::getArrayFromArgument($item)
              );
            } else if ($item instanceof \DOMElement) {
                $carry[$item->localName] = $item->textContent;
            } else if (is_scalar($item)) {
              $carry[$key] = $item;
            }
            return $carry;
          },
          []
        );
      }
      return [];
    }

    public static function serializeNodes($data, string $type): string {
      if ($data instanceof \DOMDocument) {
        return $type === 'xhtml' ? $data->saveXML():  $data->saveHTML();
      }
      if ($data instanceof \DOMNode) {
        return $type === 'xhtml'
          ? $data->ownerDocument->saveXML($data)
          : $data->ownerDocument->saveHTML($data);
      }
      if (is_array($data) || $data instanceof \DOMNodeList) {
        $array = is_array($data) ? $data : iterator_to_array($data);
        return implode(
          '',
          array_map(
            static fn($item) => self::serializeNodes($item, $type),
            $data,
          )
        );
      }
      return '';
    }
  }
}
