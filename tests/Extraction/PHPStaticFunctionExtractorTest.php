<?php
declare(strict_types=1);

namespace Carica\Localize\Extraction {

  use Carica\Localize\DataURL;
  use Carica\Localize\TranslationUnit;
  use Carica\Localize\TranslationUnitDataType;
  use PHPUnit\Framework\TestCase;

  final class PHPStaticFunctionExtractorTest extends TestCase {

    public function testWithMessageAndAutoId(): void {
      $xsl = new DataURL(
        '<'."?php use Carica\Localize\Localize;
          echo Localize::message('Example');",
        'application/xml'
      );
      $extractor = new PHPStaticFunctionExtractor();
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
            TranslationUnitDataType::PlainText,
            '',
            2
          )
        ],
        $units
      );
    }

    public function testWithIdAndMessage(): void {
      $xsl = new DataURL(
        '<'."?php use Carica\Localize\Localize;
          echo Localize::message('Example', id: 'example.id');",
        'application/xml'
      );
      $extractor = new PHPStaticFunctionExtractor();
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
            TranslationUnitDataType::PlainText,
            '',
            2
          )
        ],
        $units
      );
    }
  }
}
