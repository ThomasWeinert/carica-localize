<?php

declare(strict_types=1);

namespace I18N\Messages\Extraction {

  interface Extractor {
    public function extract(string $target): \Iterator;
  }

}
