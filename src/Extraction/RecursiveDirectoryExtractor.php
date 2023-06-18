<?php
declare(strict_types=1);

namespace I18N\Messages\Extraction {

  readonly class RecursiveDirectoryExtractor implements Extractor {

    /**
     * @var ExtractorFilter[]
     */
    private array $_extractors;

    public function __construct(
      ExtractorFilter ...$extractors
    ) {
      $this->_extractors = $extractors;
    }

    public function extract(\SplFileInfo|string $directory): \Iterator {
      $files = new \RecursiveDirectoryIterator($directory);
      $units = new \AppendIterator();
      foreach ($files as $file) {
        foreach ($this->_extractors as $extractor) {
          $units->append(
            new \NoRewindIterator($extractor->extract($file))
          );
        }
      }
      return $units;
    }
  }
}
