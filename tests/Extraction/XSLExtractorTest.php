<?php
declare(strict_types=1);

namespace Carica\Localize\Extraction {

  use Carica\Localize\DataURL;
  use Carica\Localize\TranslationUnit;
  use Carica\Localize\TranslationUnitDataType;
  use PHPUnit\Framework\TestCase;

  final class XSLExtractorTest extends TestCase {

    public function testWithMessageAndAutoId(): void {
      $xsl = new DataURL(
        '<xsl:stylesheet
            version="1.0"
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
            xmlns:i18n="urn:carica:localize">

            <xsl:template name="ExampleTemplate">
              <span>
                <xsl:call-template name="i18n:message">
                  <xsl:with-param name="message">Example</xsl:with-param>
                </xsl:call-template>
              </span>
            </xsl:template>

          </xsl:stylesheet>',
        'application/xml'
      );
      $extractor = new XSLExtractor();
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
            8
          )
        ],
        $units
      );
    }

    public function testWithIdAndMessage(): void {
      $xsl = new DataURL(
        '<xsl:stylesheet
            version="1.0"
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
            xmlns:i18n="urn:carica:localize">

            <xsl:template name="ExampleTemplate">
              <span>
                <xsl:call-template name="i18n:message">
                  <xsl:with-param name="id">example.id</xsl:with-param>
                  <xsl:with-param name="message">Example</xsl:with-param>
                </xsl:call-template>
              </span>
            </xsl:template>

          </xsl:stylesheet>',
        'application/xml'
      );
      $extractor = new XSLExtractor();
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
            8
          )
        ],
        $units
      );
    }
    public function testWithMeaningAndDescription(): void {
      $xsl = new DataURL(
        '<xsl:stylesheet
            version="1.0"
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
            xmlns:i18n="urn:carica:localize">

            <xsl:template name="ExampleTemplate">
              <span>
                <xsl:call-template name="i18n:message">
                  <xsl:with-param name="id">example.id</xsl:with-param>
                  <xsl:with-param name="message">Example</xsl:with-param>
                  <xsl:with-param name="meaning">Category</xsl:with-param>
                  <xsl:with-param name="description">Note for translator</xsl:with-param>
                </xsl:call-template>
              </span>
            </xsl:template>

          </xsl:stylesheet>',
        'application/xml'
      );
      $extractor = new XSLExtractor();
      $units = iterator_to_array(
        $extractor->extract((string)$xsl)
      );
      $this->assertEquals(
        [
          new TranslationUnit(
            'Example',
            'example.id',
            'Category',
            'Note for translator',

            TranslationUnitDataType::PlainText,
            '',
            8
          )
        ],
        $units
      );
    }
  }
}
