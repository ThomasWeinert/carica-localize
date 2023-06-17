<?php
declare(strict_types=1);

namespace I18N\Messages\Extraction {

  use PHPUnit\Framework\Attributes\DataProvider;
  use PHPUnit\Framework\TestCase;

  final class FileExtractorFilterTest extends TestCase {

    public static function provideMatchingFileNames(): array {
      return [
        ['test.xsl'],
        ['test.XSL'],
        ['test.php']
      ];
    }

    /**
     * @param string $fileName
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[DataProvider('provideMatchingFileNames')]
    public function testWithMatchingFileNames(string $fileName): void {
      $filteredFor = $this
        ->createMock(Extractor::class);
      $filteredFor
        ->expects($this->once())
        ->method('extract')
        ->with($fileName)
        ->willReturn(new \EmptyIterator());
      $filter = new ExtractorFilter(
        '(\.(?:xsl|php)$)i',
        $filteredFor
      );
      $filter->extract($fileName);
    }

    public static function provideNonMatchingFileNames(): array {
      return [
        ['test.css'],
        ['test.xsd']
      ];
    }

    /**
     * @param string $fileName
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[DataProvider('provideNonMatchingFileNames')]
    public function testWithNoneMatchingFileNames(string $fileName): void {
      $filteredFor = $this
        ->createMock(Extractor::class);
      $filteredFor
        ->expects($this->never())
        ->method('extract');
      $filter = new ExtractorFilter(
        '(\.(?:xsl|php)$)i',
        $filteredFor
      );
      $filter->extract($fileName);
    }
  }
}
