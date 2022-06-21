<?php
namespace ESOS\GraphQL;

use ESOS\GraphQL\JWT;

class Authentication {
  const HEADER_KEY = 'HTTP_AUTHORIZATION';

  private static function _getToken() {
    $token = empty($_SERVER[self::HEADER_KEY])
      ? null
      : $_SERVER[self::HEADER_KEY];

    if (
      $token &&
      preg_match('/^Bearer (.*)$/', $token, $matches) &&
      !empty($matches[1])
    ) {
      $token = $matches[1];
    }
    return $token;
  }

  public static function getAuthorizedUserId() {
    $token = self::_getToken();
    if ($token) {
      $payload = JWT::decode($token);

      if ($payload && $payload['sub']) {
        return $payload['sub'];
      }
    }
    return null;
  }
}
