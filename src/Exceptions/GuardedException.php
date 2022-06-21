<?php
declare(strict_types=1);

namespace ESOS\GraphQL\Exceptions;

use GraphQL\Error\ClientAware;

abstract class GuardedException extends \Exception implements ClientAware {
  public function __construct(
    $message,
    $code = 0,
    \Throwable $previous = null
  ) {
    parent::__construct($message, $code, $previous);
  }
  public function isClientSafe(): bool {
    return $_ENV['ENVIRONMENT'] !== 'production';
  }

  public function getCategory() {
    return 'guarded_exception';
  }

  public static function throw() {
    $class = get_called_class();
    throw new $class();
  }
}
