<?php

namespace Carica\Localize {

  use Carica\Localize\Messages\Messages;
  use Carica\Localize\Messages\EmptyMessages;
  use Carica\Localize\XSL\Callbacks;

  abstract class Localize {

    private static Messages $_messages;
    private static string $locale = 'en';

    public const XSL_CALLBACKS = [
      Callbacks::XSL_CALLBACK
    ];

    public static function messages(Messages $messages = null): Messages {
      if (NULL !== $messages) {
        self::$_messages = $messages;
      }
      return self::$_messages ?? new EmptyMessages();
    }

    public static function message(
      string $message,
      array $values = [],
      string $id = '',
      string $meaning = '',
      string $description = '',
      ?string $locale = null,
    ): string {
      $locale = $locale ?: self::$locale;
      $pattern = self::messages()->get(
        $id ?: TranslationUnit::generateId($message, $meaning)
      ) ?: $message;
      $formatter = new \MessageFormatter($locale, $pattern);
      return $formatter->format($values) ?: 'default';
    }

  }
}
