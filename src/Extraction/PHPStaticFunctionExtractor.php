<?php

declare(strict_types=1);

namespace Carica\Localize\Extraction {

  use Carica\Localize\Localize;
  use Carica\Localize\TranslationUnit;
  use PhpParser;

  readonly class PHPStaticFunctionExtractor implements FileExtractor {

    private PhpParser\Parser $_parser;
    private string $_class;
    private string $_function;

    public function __construct(
      ?PhpParser\Parser $parser = null
    ) {
      $this->_parser = $parser
        ?? (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7);
      $this->_class = Localize::class;
      $this->_function = 'message';
    }

    public function extract(\SplFileInfo|string $target): \Iterator {
      $parser = $this->_parser;
      try {
        $ast = $parser->parse(file_get_contents((string)$target));
        $nodeFinder = new PhpParser\NodeFinder();
        $calls = $nodeFinder->find(
          $ast,
          function(PhpParser\Node $node) {
            return (
              $node instanceof PhpParser\Node\Expr\StaticCall &&
              $node->class !== $this->_class &&
              (string)$node->name === $this->_function
            );
          }
        );
      } catch (PhpParser\Error $error) {
        return new \EmptyIterator();
      }
      /** @var PhpParser\Node\Expr\StaticCall $call */
      foreach ($calls as $call) {
        $arguments = array_reduce(
          $call->args,
          static function($carry, PhpParser\Node\Arg $argument) {
            static $index = 0;
            $name = (string)($argument->name ?? $index);
            $value = $argument->value;
            $carry[$name] = $value instanceOf PhpParser\Node\Scalar\String_
              ? $value->value : '';
            return $carry;
          },
          []
        );
        yield (
          new TranslationUnit(
            source: $arguments['message'] ?? $arguments[0] ?? '',
            id: $arguments['id'] ?? $arguments[2] ?? '',
            meaning: $arguments['meaning'] ?? $arguments[3] ?? '',
            description: $arguments['description'] ?? $arguments[4] ?? '',
            file: (string)$target,
            line: $call->getStartLine(),
          )
        );
      }
    }


  }

}
