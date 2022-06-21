<?php
declare(strict_types=1);

namespace ESOS\GraphQL\Exceptions;

class UnauthorizedException extends GuardedException {
  protected const MESSAGE = 'Unauthorized';

  public function __construct(
    $message = self::MESSAGE,
    $code = 0,
    \Throwable $previous = null
  ) {
    parent::__construct($message, $code, $previous);
  }
}
