<?php
/*
 * Facade for Guzzle client
 */
class Guzzle {
  static $client;
  static function __callStatic($name, $args) {
    if (!self::$client) self::$client = new GuzzleHttp\Client;
    return call_user_func_array([self::$client, $name], $args);
  }
}
