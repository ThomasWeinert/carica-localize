<?php
declare(strict_types=1);

namespace Carica\Localize\XSL {

  use PHPUnit\Framework\TestCase;

  final class IntegrationTest extends TestCase {

    public function testMessage(): void {
      FileLoader::register();
      $processor = new \XSLTProcessor();
      $processor->registerPHPFunctions(
        [Callbacks::class.'::handleFunctionCall']
      );

      $template = new \DOMDocument();
      $template->loadXML(
        '<xsl:stylesheet
            version="1.0"
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
            xmlns:localize="urn:carica:localize"
            exclude-result-prefixes="localize">

            <xsl:import href="carica-localize://localize"/>

            <xsl:template match="/">
              <div>
                <xsl:call-template name="localize:message">
                  <xsl:with-param name="id">example.id</xsl:with-param>
                  <xsl:with-param name="message">Example</xsl:with-param>
                </xsl:call-template>
              </div>
            </xsl:template>

          </xsl:stylesheet>'
      );

      $processor->importStylesheet($template);

      $document = new \DOMDocument();
      $document->loadXML('<dummy/>');

      $output = $processor->transformToXml($document);
      $this->assertXmlStringEqualsXmlString(
        '<div>Example</div>',
         $output
      );
    }

    public function testMessageWithValueFromDocument(): void {
      FileLoader::register();
      $processor = new \XSLTProcessor();
      $processor->registerPHPFunctions(
        [Callbacks::class.'::handleFunctionCall']
      );

      $template = new \DOMDocument();
      $template->loadXML(
        '<xsl:stylesheet
            version="1.0"
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
            xmlns:localize="urn:carica:localize"
            exclude-result-prefixes="localize">

            <xsl:import href="carica-localize://localize"/>

            <xsl:template match="/">
              <div>
                <xsl:call-template name="localize:message">
                  <xsl:with-param name="id">example.id</xsl:with-param>
                  <xsl:with-param name="message">Example: {foo}</xsl:with-param>
                  <xsl:with-param name="values" select="/values/*"/>
                </xsl:call-template>
              </div>
            </xsl:template>

          </xsl:stylesheet>'
      );

      $processor->importStylesheet($template);

      $document = new \DOMDocument();
      $document->loadXML('<values><foo>BAR</foo></values>');

      $output = $processor->transformToXml($document);
      $this->assertXmlStringEqualsXmlString(
        '<div>Example: BAR</div>',
        $output
      );
    }

    public function testMessageWithValueFromParameter(): void {
      FileLoader::register();
      $processor = new \XSLTProcessor();
      $processor->registerPHPFunctions(
        [Callbacks::class.'::handleFunctionCall']
      );

      $template = new \DOMDocument();
      $template->loadXML(
        '<xsl:stylesheet
            version="1.0"
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
            xmlns:localize="urn:carica:localize"
            exclude-result-prefixes="localize">

            <xsl:import href="carica-localize://localize"/>

            <xsl:template match="/">
              <div>
                <xsl:call-template name="localize:message">
                  <xsl:with-param name="id">example.id</xsl:with-param>
                  <xsl:with-param name="message">Example: {foo}</xsl:with-param>
                  <xsl:with-param name="values">
                    <foo>BAR</foo>
                  </xsl:with-param>
                </xsl:call-template>
              </div>
            </xsl:template>

          </xsl:stylesheet>'
      );

      $processor->importStylesheet($template);

      $document = new \DOMDocument();
      $document->loadXML('<values><foo>BAR</foo></values>');

      $output = $processor->transformToXml($document);
      $this->assertXmlStringEqualsXmlString(
        '<div>Example: BAR</div>',
        $output
      );
    }

  }
}
