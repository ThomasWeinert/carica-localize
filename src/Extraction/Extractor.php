<?php

declare(strict_types=1);

namespace Carica\Localize\Extraction {

  interface Extractor {
    public function extract(\SplFileInfo|string $target): \Iterator;
  }

}
