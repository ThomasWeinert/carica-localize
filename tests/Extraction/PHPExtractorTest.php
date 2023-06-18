<?php
declare(strict_types=1);

namespace I18N\Messages\Extraction {

  use I18N\Messages\DataURL;
  use I18N\Messages\TranslationUnit;
  use PHPUnit\Framework\TestCase;

  final class PHPExtractorTest extends TestCase {

    public function testWithMessageAndAutoId(): void {
      $xsl = new DataURL(
        '<'."?php use I18N\Messages\Localize;
          echo Localize::message('Example');",
        'application/xml'
      );
      $extractor = new PHPExtractor();
      $units = iterator_to_array(
        $extractor->extract((string)$xsl)
      );
      $this->assertEquals(
        [
          new TranslationUnit(
            'Example',
            'fd50843194309267b53a13993393d2a2',
            '',
            '',
            '',
            2
          )
        ],
        $units
      );
    }

    public function testWithIdAndMessage(): void {
      $xsl = new DataURL(
        '<'."?php use I18N\Messages\Localize;
          echo Localize::message('Example', id: 'example.id');",
        'application/xml'
      );
      $extractor = new PHPExtractor();
      $units = iterator_to_array(
        $extractor->extract((string)$xsl)
      );
      $this->assertEquals(
        [
          new TranslationUnit(
            'Example',
            'example.id',
            '',
            '',
            '',
            2
          )
        ],
        $units
      );
    }
  }
}
