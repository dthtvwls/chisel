<?php
class GuzzleTest extends PHPUnit_Framework_TestCase {
  function testFacade() {
    $this->assertEquals(preg_match('#^Guzzle/.+ curl/.+ PHP/.+$#', Guzzle::getDefaultUserAgent()), 1);
  }
}
