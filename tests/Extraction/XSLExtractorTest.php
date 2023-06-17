<?php
declare(strict_types=1);

namespace I18N\Messages\Extraction {

  use I18N\Messages\DataURL;
  use I18N\Messages\TranslationUnit;
  use PHPUnit\Framework\TestCase;

  final class XSLExtractorTest extends TestCase {

    public function testWithIdAndMessage(): void {
      $xsl = new DataURL(
        '<xsl:stylesheet
            version="1.0"
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
            xmlns:i18n="urn:i18n">

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
            'example.id',
            'Example',
            '',
            '',
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
            xmlns:i18n="urn:i18n">

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
            'example.id',
            'Example',
            'Category',
            'Note for translator',
            '',
            8
          )
        ],
        $units
      );
    }
  }
}
