<?php
declare(strict_types=1);

namespace I18N\Messages\Serializer {

  use I18N\Messages\DataURL;
  use I18N\Messages\TranslationUnit;
  use PHPUnit\Framework\TestCase;

  final class XliffSerializerTest extends TestCase {

    public function testSerializeSingleUnit(): void {
      $serializer = new XliffSerializer();
      $xliff = $serializer->serializeToString(
        new \ArrayIterator(
          [
            new TranslationUnit('test.id', 'test', '', '', '', 0)
          ]
        ),
        'en'
      );
      $this->assertXmlStringEqualsXmlString(
        '<xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2">
            <file datatype="plaintext" original="xsl.template" source-language="en">
              <body>
                <trans-unit id="test.id">
                  <source>test</source>
                </trans-unit>
              </body>
            </file>
          </xliff>',
        $xliff
      );
    }

    public function testSerializeSingleUnitWithTargetLanguage(): void {
      $serializer = new XliffSerializer();
      $xliff = $serializer->serializeToString(
        new \ArrayIterator(
          [
            new TranslationUnit('test.id', 'test', '', '', '', 0)
          ]
        ),
        'en',
        'de'
      );
      $this->assertXmlStringEqualsXmlString(
        '<xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2">
            <file datatype="plaintext" original="xsl.template" source-language="en" target-language="de">
              <body>
                <trans-unit id="test.id">
                  <source>test</source>
                  <target state="new">test</target>
                </trans-unit>
              </body>
            </file>
          </xliff>',
        $xliff
      );
    }

    public function testSerializeAndMerge(): void {
      $existingFile = new DataURL(
        '<xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2">
            <file datatype="plaintext" original="xsl.template" source-language="en" target-language="de">
              <body>
                <trans-unit id="test.id.translated">
                  <source>translated</source>
                  <target state="translated">translated target</target>
                </trans-unit>
                <trans-unit id="test.id.change-meaning">
                  <source>change-meaning</source>
                  <target state="translated">translated target</target>
                </trans-unit>
                <trans-unit id="test.id.change-source">
                  <source>change-source</source>
                  <target state="translated">translated target</target>
                </trans-unit>
              </body>
            </file>
          </xliff>',
        'application/xml'
      );
      $serializer = new XliffSerializer();
      $xliff = $serializer->serializeToString(
        new \ArrayIterator(
          [
            new TranslationUnit(
              'test.id.translated', 'translated', '', '', '', 0
            ),
            new TranslationUnit(
              'test.id.change-meaning', 'change-meaning', 'category', '', '', 0
            ),
            new TranslationUnit(
              'test.id.change-source', 'changed', '', '', '', 0
            ),
            new TranslationUnit(
              'test.id.new', 'new', '', '', '', 0
            ),
          ]
        ),
        'en',
        'de',
        (string)$existingFile,
      );
      $this->assertXmlStringEqualsXmlString(
        '<xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2">
            <file datatype="plaintext" original="xsl.template" source-language="en" target-language="de">
              <body>
                <trans-unit id="test.id.translated">
                  <source>translated</source>
                  <target state="translated">translated target</target>
                </trans-unit>
                <trans-unit id="test.id.change-meaning">
                  <source>change-meaning</source>
                  <target state="needs-l10n">translated target</target>
                  <note from="meaning" priority="1">category</note>
                </trans-unit>
                <trans-unit id="test.id.change-source">
                  <source>changed</source>
                  <target state="needs-l10n">translated target</target>
                </trans-unit>
                <trans-unit id="test.id.new">
                  <source>new</source>
                  <target state="new">new</target>
                </trans-unit>
              </body>
            </file>
          </xliff>',
        $xliff
      );
    }
  }
}
