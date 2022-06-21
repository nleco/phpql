<?php

namespace ESOS\GraphQL;

class Encryption {
  private static function _hashUserPassword($userPassword) {
    $pepper = $_ENV['PEPPER_USER_PASSWORD'];
    return hash_hmac('sha256', $userPassword, $pepper);
  }

  public static function encryptUserPassword($userPassword) {
    $pwd_peppered = self::_hashUserPassword($userPassword);
    $pwd_hashed = password_hash($pwd_peppered, PASSWORD_ARGON2ID);
    return $pwd_hashed;
  }

  public static function verifyUserPassword($userPassword, $hashedPassword) {
    $pwd_peppered = self::_hashUserPassword($userPassword);
    return password_verify($pwd_peppered, $hashedPassword);
  }
}
