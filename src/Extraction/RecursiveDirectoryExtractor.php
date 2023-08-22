<?php
declare(strict_types=1);

namespace Carica\Localize\Extraction {

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

    public function extract(\SplFileInfo|string $target): \Iterator {
      $files = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator(
          $target,
          \FilesystemIterator::SKIP_DOTS
        )
      );
      $units = new \AppendIterator();
      foreach ($files as $file) {
        if ($file->isDir()) {
          continue;
        }
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
