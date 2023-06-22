<?php
declare(strict_types=1);

namespace Carica\Localize\Serializer\Report {

  class ConsoleReport implements Report {

    private array $_counts = [
    ];

    private array $_types = [
      CreateUnitMessage::class => '+',
      ChangeUnitMessage::class => '!',
      EqualUnitMessage::class => '=',
    ];

    public function message(ReportMessage $message): void {
      echo ' '.$message->getMessage(), ': ';
      echo grapheme_strlen($message->unit->source) > 30
        ? grapheme_substr($message->unit->source, 0, 29).'…'
        : $message->unit->source;
      echo "\n";
      if ($type = $this->_types[get_class($message)]) {
        $this->_counts[$type] = isset($this->_counts[$type])
          ? $this->_counts[$type] + 1 : 0;
      }
    }

    public function startFile(string $fileName): void {
      echo $fileName, "\n";
      $this->_counts = [];
    }

    public function endFile(string $fileName): void {
      printf(
        "[=] %d [+] %d [!] %d\n",
        $this->_counts['='] ?? 0,
        $this->_counts['+'] ?? 0,
        $this->_counts['!'] ?? 0
      );
    }
  }
}
