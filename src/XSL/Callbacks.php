<?php
declare(strict_types=1);

namespace Carica\Localize\XSL {

  use BadMethodCallException;

  abstract  class Callbacks {

    private static array $_modules = [
      'Messages' => Messages::class,
    ];

    public const XSL_CALLBACK = self::class.'::handleFunctionCall';

    public static function handleFunctionCall(
      string $module, string $function, ...$arguments
    ): mixed {
      $call = self::getCallback($module, $function);
      $result = $call(...$arguments);
      if (
        is_object($result) &&
        !(
          $result instanceof \DOMNode ||
          $result instanceof \DOMNodeList
        ) &&
        (method_exists($result, '__toString'))
      ) {
        return $result->__toString();
      }
      return $result;
    }

    private static function getCallback(string $module, string $function): callable {
      $moduleName = isset(self::$_modules[$module]) ? $module : strtolower($module);
      if (!isset(self::$_modules[$moduleName])) {
        throw new BadMethodCallException("Invalid XSLT callback module: {$module}");
      }
      $callback = self::$_modules[$moduleName].'::'.$function;
      if (!is_callable($callback)) {
        throw new BadMethodCallException("Invalid XSLT callback function: {$module} -> {$function}");
      }
      return $callback;
    }
  }
}
