<?php
declare(strict_types=1);

namespace Carica\Localize\Extraction {

  readonly class ConflictValidator implements FileExtractor {

    public function __construct(
      private Extractor $extractor
    ) {
    }

    public function extract(\SplFileInfo|string $target): \Iterator {
      $done = [];
      foreach ($this->extractor->extract($target) as $unit) {
        if ($done[$unit->id] ?? null) {
          $existing = $done[$unit->id];
          if ($unit->meaning !== $existing->meaning) {
            throw new ConflictException(
              ConflictProperty::Meaning,
              $unit,
              $existing
            );
          }
          if ($unit->source !== $existing->source) {
            throw new ConflictException(
              ConflictProperty::Source,
              $unit,
              $existing
            );
          }
        }
        yield $unit;
      }
      return new \EmptyIterator();
    }
  }
}
