<?php
declare(strict_types=1);

namespace Carica\Localize {

  enum TranslationUnitDataType: string {
    case PlainText = 'plaintext';
    case Html = 'html';
    case XHtml = 'xhtml';
  }
}
