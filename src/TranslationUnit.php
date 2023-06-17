<?php
declare(strict_types=1);

namespace I18N\Messages {

  final readonly class TranslationUnit
  {

    public function __construct(
      public string $id,
      public string $source,
      public string $meaning,
      public string $description,
      public string $file,
      public int $line
    )
    {
    }
  }
}
