<?php

declare(strict_types=1);

namespace Carica\Localize {

  use Carica\Localize\Extraction\ConflictValidator;
  use Carica\Localize\Extraction\ExtractorFilter;
  use Carica\Localize\Extraction\RecursiveDirectoryExtractor;
  use Carica\Localize\Serializer\Serializer;
  use Carica\Localize\Serializer\XliffSerializer;

  class Extraction implements \IteratorAggregate {

    private array $_extractors = [];

    private ?array $_messages = null;

    public function __construct(
      private readonly string $directory,
      array $fileExtractors
    ) {
      foreach ($fileExtractors as $pattern => $extractor) {
        $this->_extractors[] = new ExtractorFilter($pattern, $extractor);
      }
    }

    public function getIterator(): \Iterator {
      if (!isset($this->_messages)) {
        $this->_messages = iterator_to_array(
          (
            new ConflictValidator(
              new RecursiveDirectoryExtractor(
                ...$this->_extractors
              )
            )
          )->extract($this->directory)
        );
      }
      return new \ArrayIterator($this->_messages);
    }

    public function output(
      string $directory,
      string $sourceLanguage = 'en',
      array $targetLanguages = [],
      string $projectName = 'messages',
      Serializer $serializer = new XliffSerializer()
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
            $fileName
          )
        );
      }
    }
  }

}
