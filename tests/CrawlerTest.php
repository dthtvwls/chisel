<?php
class CrawlerTest extends PHPUnit_Framework_TestCase {
  function testFacade() {
    $crawler = new Crawler('<html/>');
    $this->assertEquals($crawler->filter('html')->count(), 1);
  }
}
