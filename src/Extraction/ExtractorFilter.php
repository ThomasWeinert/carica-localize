<?php
declare(strict_types=1);

namespace Carica\Localize\Extraction {

  readonly class ExtractorFilter implements FileExtractor {

    private array $_extractors;

    public function __construct(
      private string $pattern,
      FileExtractor ...$extractors
    ) {
      $this->_extractors = $extractors;
    }

    public function extract(\SplFileInfo|string $target): \Iterator {
      if (preg_match($this->pattern, (string)$target)) {
        $iterator = new \AppendIterator();
        foreach ($this->_extractors as $extractor) {
          $iterator->append($extractor->extract($target));
        }
        return $iterator;
      }
      return new \EmptyIterator();
    }
  }
}
