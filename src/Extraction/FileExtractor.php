<?php

declare(strict_types=1);

namespace I18N\Messages\Extraction {

  interface FileExtractor {
    public function extract(string $fileName): \Iterator;
  }

}
