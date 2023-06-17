<?php

declare(strict_types=1);

namespace I18N\Messages\Extraction {

  use I18N\Messages\TranslationUnit;

  enum ConflictProperty {
    case Source;
    case Meaning;
  }

  class ConflictException extends \Exception {

    public function __construct(
      ConflictProperty $property,
      TranslationUnit $current,
      TranslationUnit $previous
    ) {
      parent::__construct(
        sprintf(
          "Error: Conflicting message id \u{0001F5C8} %s with different %s in\n %s:%d\n".
          "Already found as \u{0001F5C8} %s in\n %s:%d\n",
          $current->label,
          match($property) {
            ConflictProperty::Meaning => 'meaning',
            ConflictProperty::Source => 'source'
          },
          $current->file,
          $current->line,
          $previous->label,
          $previous->file,
          $previous->line,
        )
      );
    }
  }

}
