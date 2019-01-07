<?php

namespace pkpudev\components\helpers;

class UserIP
{
  public static function getRealIpAddress()
  {
    $paramsCheck = [
      'HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED',
      'HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR',
    ];

    foreach ($paramsCheck as $key) {
      if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
      }
    }
    return null;
  }
}