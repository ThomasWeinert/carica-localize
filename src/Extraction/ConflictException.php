<?php

declare(strict_types=1);

namespace Carica\Localize\Extraction {

  use Carica\Localize\TranslationUnit;

  final class ConflictException extends \Exception {

    public function __construct(
      public readonly ConflictProperty $property,
      public readonly TranslationUnit  $currentUnit,
      public readonly TranslationUnit $previousUnit
    ) {
      parent::__construct(
        sprintf(
          "Error: Conflicting message id \u{0001F5C8} %s with different %s in\n %s:%d\n".
          "Already found as \u{0001F5C8} %s in\n %s:%d\n",
          $currentUnit->label,
          match($property) {
            ConflictProperty::Meaning => 'meaning',
            ConflictProperty::Source => 'source'
          },
          $currentUnit->file,
          $currentUnit->line,
          $previousUnit->label,
          $previousUnit->file,
          $previousUnit->line,
        )
      );
    }
  }

}
