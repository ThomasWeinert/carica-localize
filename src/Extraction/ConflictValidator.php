<?php
declare(strict_types=1);

namespace Carica\Localize\Extraction {

  use Traversable;

  readonly class ConflictValidator implements \IteratorAggregate
  {

    public function __construct(
      private iterable  $translationUnits,
      private ?\Closure $onConflict = null,
    )
    {
    }

    public function getIterator(): Traversable {
      $done = [];
      foreach ($this->translationUnits as $unit) {
        try {
          $existing = $done[$unit->id] ?? null;
          if ($existing) {
            if ($unit->meaning !== $existing->meaning) {
              throw new ConflictException(
                ConflictProperty::Meaning,
                $unit,
                $existing
              );
            }
            if ($unit->source !== $existing->source) {
              throw new ConflictException(
                ConflictProperty::Source,
                $unit,
                $existing
              );
            }
          }
          $done[$unit->id] = $unit;
          yield $unit;
        } catch (ConflictException $e) {
          if (is_callable($this->onConflict)) {
            ($this->onConflict)($e);
          } else {
            throw $e;
          }
        }
      }
      return new \EmptyIterator();
    }
  }
}
