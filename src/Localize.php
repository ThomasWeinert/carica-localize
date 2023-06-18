<?php

namespace Carica\Localize {

  abstract class Localize {

    public static function message(
      string $message,
      array $values = [],
      string $id = '',
      string $meaning = '',
      string $description = ''
    ): string {

    }

  }
}
