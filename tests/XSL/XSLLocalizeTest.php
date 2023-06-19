<?php
declare(strict_types=1);

namespace Carica\Localize\XSL {

  use PHPUnit\Framework\TestCase;

  final class XSLLocalizeTest extends TestCase {

    public function testMessage(): void {
      $formatted = XSLLocalize::formatMessage(
        'en',
        'Example'
      );
      $this->assertEquals('Example', $formatted);
    }

    public function testMessageWithPlaceholder(): void {
      $formatted = XSLLocalize::formatMessage(
        'en',
        'Example: {value}',
        [
          'value' => 'success'
        ]
      );
      $this->assertEquals('Example: success', $formatted);
    }

  }
}
