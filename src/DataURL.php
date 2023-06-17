<?php
declare(strict_types=1);

namespace I18N\Messages {

  readonly class DataURL {

    public function __construct(
      private string $content,
      private string $contentType = 'text/plain',
    ) {
    }

    public function __toString(): string {
      return 'data://'.$this->contentType.';base64,'.base64_encode($this->content);
    }
  }
}
