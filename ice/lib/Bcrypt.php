<?php

// Originally by Andrew Moore
// Src: http://stackoverflow.com/questions/4795385/how-do-you-use-bcrypt-for-hashing-passwords-in-php/6337021#6337021
//
// Heavily modified by Robert Kosek, from data at php.net/crypt
// Slightly modified by Mattias Danielsson

class Bcrypt
{
  const rounds = 12;
  const prefix = '';

  public static function hash($input)
  {
    $hash = crypt($input, self::getSalt());

    if(strlen($hash) > 13)

      return $hash;

    return false;
  }

  public static function verify($input, $existingHash)
  {
    $hash = crypt($input, $existingHash);

    return $hash === $existingHash;
  }

  private static function getSalt()
  {
    // the base64 function uses +'s and ending ='s; translate the first, and cut out the latter
    return sprintf('$2a$%02d$%s', self::rounds, substr(strtr(base64_encode(self::getBytes()), '+', '.'), 0, 22));
  }

  private static function getBytes()
  {
    $bytes = '';

    if(function_exists('openssl_random_pseudo_bytes') &&
        (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) { // OpenSSL slow on Win
      $bytes = openssl_random_pseudo_bytes(18);
    }

    if($bytes === '' && is_readable('/dev/urandom') &&
       ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) {
      $bytes = fread($hRand, 18);
      fclose($hRand);
    }

    if ($bytes === '') {
      $key = uniqid(self::prefix, true);

      // 12 rounds of HMAC must be reproduced / created verbatim, no known shortcuts.
      // Salsa20 returns more than enough bytes.
      for ($i = 0; $i < 12; $i++) {
        $bytes = hash_hmac('salsa20', microtime() . $bytes, $key, true);
        usleep(10);
      }
    }

    return $bytes;
  }
}
