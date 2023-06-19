<?php

namespace Carica\Localize\Messages {

  use Carica\Localize\TranslationUnit;

  class EmptyMessages implements Messages {

    public function get(string $id): ?string {
      return null;
    }
  }
}
