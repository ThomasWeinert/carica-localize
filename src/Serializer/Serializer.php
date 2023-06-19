<?php
declare(strict_types=1);

namespace Carica\Localize\Serializer {


  use Carica\Localize\Serializer\Report\Report;

  interface Serializer {


    public function getFileName(string $name, string $targetLanguage = ''): string;

    public function serializeToString(
      iterable $units,
      string $sourceLanguage,
      string $targetLanguage = '',
      string $mergeFromFile = '',
      ?Report $report = null,
    ): string;
  }
}
