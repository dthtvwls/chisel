<?php
/*
 * Facade for Symfony's Crawler
 */
class Crawler {
  var $crawler;
  function __construct($body) {
    $this->crawler = new Symfony\Component\DomCrawler\Crawler;
    $this->crawler->addContent($body);
  }
  function __call($name, $args) {
    return call_user_func_array([$this->crawler, $name], $args);
  }
}
