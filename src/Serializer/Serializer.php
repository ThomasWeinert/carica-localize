<?php
declare(strict_types=1);

namespace I18N\Messages\Serializer {

  interface Serializer {

    public function create(\Iterator $units): string;
    public function merge(\Iterator $units, string $fileName): string;
  }
}
