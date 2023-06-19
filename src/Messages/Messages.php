<?php

namespace Carica\Localize\Messages {

  use Carica\Localize\TranslationUnit;

  interface Messages {

    public function get(string $id): ?string;
  }
}
