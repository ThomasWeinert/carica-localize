<?php
declare(strict_types=1);

namespace Carica\Localize {

  final readonly class TranslationUnit
  {
    public string $file;
    public string $label;
    public string $id;

    public function __construct(
      public string $source,
      ?string $id = null,
      public string $meaning = '',
      public string $description = '',
      string $file = '',
      public int $line = -1
    )
    {
      $this->id = $id ?: self::generateId($source, $meaning);
      $this->label = $this->id.($meaning ? " \u{0001F5C0} ".trim($meaning) : '');
      $this->file = (str_starts_with($file, 'data://')) ? '' : $file;
    }

    public static function generateId(string $source, string $meaning): string {
      return md5(trim($source)."\u{0001F5C0}".$meaning);
    }
  }
}
