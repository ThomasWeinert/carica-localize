<?php
declare(strict_types=1);

namespace I18N\Messages\Serializer {

  interface Serializer {


    public function getFileName(string $name, string $targetLanguage = ''): string;

    public function serializeToString(
      iterable $units,
      string $sourceLanguage,
      string $targetLanguage = '',
      string $mergeFromFile = '',
    ): string;
  }
}
