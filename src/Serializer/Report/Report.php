<?php
declare(strict_types=1);

namespace Carica\Localize\Serializer\Report {

  interface Report {

    public function message(ReportMessage $message): void;
    public function startFile(string $fileName): void;
    public function endFile(string $fileName): void;
  }
}
