<?php
declare(strict_types=1);

namespace Carica\Localize\Serializer\Report {

  use Carica\Localize\TranslationUnit;

  readonly class UpdateUnitMessage implements ReportMessage {

    private string $_message;

    public function __construct(
      public TranslationUnit $unit
    ) {
      $this->_message = sprintf(
        'Update: %s', $unit->label
      );
    }

    public function getMessage(): string {
      return $this->_message;
    }
  }
}
