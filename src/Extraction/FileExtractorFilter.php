<?php
declare(strict_types=1);

namespace I18N\Messages\Extraction {

  readonly class FileExtractorFilter implements FileExtractor {

    private array $_extractors;

    public function __construct(
      private string $pattern,
      FileExtractor ...$extractors
    ) {
      $this->_extractors = $extractors;
    }

    public function extract(string $fileName): \Iterator {
      if (preg_match($this->pattern, $fileName)) {
        $iterator = new \AppendIterator();
        foreach ($this->_extractors as $extractor) {
          $iterator->append($extractor->extract($fileName));
        }
        return $iterator;
      }
      return new \EmptyIterator();
    }
  }
}
