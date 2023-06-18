<?php
declare(strict_types=1);

namespace I18N\Messages\XSL {

  use I18N\Messages\TranslationUnit;

  abstract class Messages {

    public static function generateId(string $source, string $meaning): string {
      return TranslationUnit::generateId($source, $meaning);
    }

    public static function formatMessage(
      string $locale, string $pattern, $values = null
    ): string {
      $message = new \MessageFormatter($locale, $pattern);
      $values = self::getArrayFromArgument($values);
      return $message->format($values) ?: 'default';
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
  }
}
