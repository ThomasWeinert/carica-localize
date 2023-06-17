<?php
declare(strict_types=1);

namespace I18N\Messages\Extraction {

  readonly class RecursiveDirectoryFileExtractor {

    /**
     * @var FileExtractorFilter[]
     */
    private array $_extractors;

    public function __construct(
      FileExtractorFilter ...$extractors
    ) {
      $this->_extractors = $extractors;
    }

    public function extract(string $directory): \Iterator {
      $files = new \RecursiveDirectoryIterator($directory);
      $units = new \AppendIterator();
      foreach ($files as $file) {
        foreach ($this->_extractors as $extractor) {
          $units->append($extractor->extract($file));
        }
      }
      return $units;
    }
  }
}
