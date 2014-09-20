<?php
class Chisel {
  
  function __construct($url, $body = null) {
    $this->url     = $url;
    $this->body    = $body ? $body : Guzzle::get($url)->getBody();
    $this->crawler = new Crawler($this->body);
  }
  
  /*
   * returns array of nodes
   */
  function nodes($selector) {
    return iterator_to_array($this->crawler->filter($selector));
  }
  
  /*
   * returns first match in array
   */
  function attempt($selectors) {
    $attempt = 0;
    do {
      $node = $this->crawler->filter($selectors[$attempt]);
    } while ($node->count() == 0 && ++$attempt < count($selectors));
    return $node->count() > 0 ? $node->first() : null;
  }
  
  /*
   * Return a group of fields?
   * TODO: figure out wtf i was thinking
   */
  function group($fields) {
    foreach ($fields as $name => $selector) {
      
    }
  }
  
  /*
   * Return a hash of og tags
   */
  function og_tags() {
    $tags = [];
    
    $this->crawler->filter('meta[property^="og:"]')->each(function ($tag) use (&$tags) {
      $tags[$tag->attr('property')] = $tag->attr('content');
    });
    
    return $tags;
  }
  
  /*
   * Return array of unique link urls
   */
  function links() {
    $links = [];
    
    $this->crawler->filter('a')->each(function ($a) use (&$links) {
      $links[] = $a->attr('href');
    });
    
    return array_unique($links);
  }
  
  /*
   * Returns Readability content after cleaning and caching
   */
  function readable($allowed_tags = '<p><a><h1><h2><h3>', $allowed_classes = []) {
    // If this function has run before on this instance then return the saved text
    if ($this->_readable) return $this->_readable;
    
    $html = $this->body;
    
    // Fucking BuzzFeed has the most ghetto ass template system and it
    // fucks up PHP DOMDocument. That is the only reason for this line.
    // It can and should be removed whenever BuzzFeed grows a clue.
    $html = str_replace("+ '</div>'", '', $html);
    
    
    // Step 1. Readability
    $r = new Readability($html);
    $r->init();
    $html = $r->getContent()->innerHTML;
    
    // Step 2. strip_tags
    $html = strip_tags($html, $allowed_tags);
    
    // Step 3. HTMLPurifier
    $config = HTMLPurifier_Config::createDefault();
    $config->set('Attr', 'AllowedClasses', $allowed_classes);
    $html = (new HTMLPurifier($config))->purify($html);
    
    // Step 4. HTML Tidy
    $html = tidy_repair_string($html, ['bare' => true, 'show-body-only' => true, 'wrap' => 0], 'UTF8');
    
    
    // Save the result to the instance before returning
    // I want $this->terms() to be able to use it without recomputing
    $this->_readable = $html;    
    return $this->_readable;
  }
  
  function terms() {
    return (new TermExtractor)->extract(strip_tags($this->readable()));
  }
  
}
