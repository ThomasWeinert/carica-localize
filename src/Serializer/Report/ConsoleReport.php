<?php
declare(strict_types=1);

namespace Carica\Localize\Serializer\Report {

  class ConsoleReport implements Report {

    private array $_counts = [
      'new' => 0,
      'changed' => 0,
    ];

    public function push(ReportMessage $message): void {
      echo $message->getMessage();
      $type = $message instanceof CreateUnitMessage ? 'new' : 'changed';
      $this->_counts[$type]++;
    }

    public function getSummary(): array {
      return [...$this->_counts];
    }
  }
}
