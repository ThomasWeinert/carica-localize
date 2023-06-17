<?php
declare(strict_types=1);

namespace I18N\Messages {

  final readonly class TranslationUnit
  {
    public string $file;
    public string $label;

    public function __construct(
      public string $id,
      public string $source,
      public string $meaning,
      public string $description,
      string $file,
      public int $line
    )
    {
      $this->label = $this->id.($meaning ? " \u{0001F5C0} ".$meaning : '');
      $this->file = (str_starts_with($file, 'data://')) ? '' : $file;
    }
  }
}
