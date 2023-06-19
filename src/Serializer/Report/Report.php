<?php
declare(strict_types=1);

namespace Carica\Localize\Serializer\Report {

  interface Report {

    public function push(ReportMessage $message): void;
  }
}
