<?php

declare(strict_types=1);

namespace Carica\Localize {

  use Carica\Localize\Extraction\ConflictValidator;
  use Carica\Localize\Extraction\ExtractorFilter;
  use Carica\Localize\Extraction\RecursiveDirectoryExtractor;
  use Carica\Localize\Serializer\Report\Report;
  use Carica\Localize\Serializer\Serializer;
  use Carica\Localize\Serializer\XliffSerializer;

  class Extraction implements \IteratorAggregate {

    private array $_extractors = [];

    private ?array $_messages = null;

    /**
     * @var string[]
     */
    private array $_directories;

    public function __construct(
      string|array $directories,
      array $fileExtractors,
      private ?\Closure $onConflict = null,
    ) {
      $this->_directories = is_string($directories)
        ? [$directories]
        : array_map(
          static fn($directory) => (string)$directory,
          $directories
        );
      foreach ($fileExtractors as $pattern => $extractor) {
        $this->_extractors[] = new ExtractorFilter($pattern, $extractor);
      }
    }

    public function getIterator(): \Iterator {
      if (!isset($this->_messages)) {
        $iterator = new \AppendIterator();
        $messages = new RecursiveDirectoryExtractor(...$this->_extractors);
        foreach ($this->_directories as $directory) {
          $iterator->append($messages->extract($directory));
        }
        $this->_messages = iterator_to_array(
          new ConflictValidator($iterator, $this->onConflict)
        );
      }
      return new \ArrayIterator($this->_messages);
    }

    public function output(
      string $directory,
      string $sourceLanguage = 'en',
      array $targetLanguages = [],
      string $projectName = 'messages',
      Serializer $serializer = new XliffSerializer(),
      ?Report $report = null,
    ): void {
      file_put_contents(
        $directory.'/'.$serializer->getFileName($projectName),
        $serializer->serializeToString(
          $this,
          $sourceLanguage
        )
      );
      foreach ($targetLanguages as $targetLanguage) {
        $fileName = $directory.'/'.$serializer->getFileName($projectName, $targetLanguage);
        file_put_contents(
          $fileName,
          $serializer->serializeToString(
            $this,
            $sourceLanguage,
            $targetLanguage,
            $fileName,
          )
        );
      }
    }
  }

}
