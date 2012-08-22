<?php

class feedReaderFilterIterator extends FilterIterator {
  
  /**
   * Handles the keyword we're looking for
   */
  
  private $selectedQuery;
  
  public function __construct(Iterator $it, $selectedQuery) {
    $this->selectedQuery = $selectedQuery;
    
    parent::__construct($it);
  }
  
  public function accept() {
    return preg_match('/' . strtolower($this->selectedQuery) .'/', strtolower($this->current()->description));
  }
  
}

class feedReader implements IteratorAggregate {
  
  /**
   * Handles the FilterIterator instance
   */
  
  private $itResult;
  
  public function __construct($selectedQuery, $feedUrl) {
    
    if(!filter_var($feedUrl, FILTER_VALIDATE_URL)) {
      throw new Exception('The URL is not valid');
    }
    
    $xml = new SimpleXMLIterator(file_get_contents($feedUrl));
    
    if(strlen($selectedQuery) < 3) {
      throw new Exception('The query has to be more than 3 chars length');
    }
    
    $this->itResult = new feedReaderFilterIterator(new ArrayIterator($xml->xpath('channel/item')), $selectedQuery);
  }
    
  public function getIterator() {
    return $this->itResult;
  }
  
}

try {
  $itFeedReader = new feedReader((isset($_GET['s']) ? htmlspecialchars($_GET['s']) : 'Symfony2'), 'http://xlab.pl/feed');
  
  /**
   * Run the template
   */
   
  include_once 'template.html';
  
}catch(Exception $e) {
  
  echo 'Error: ' . $e->getMessage();
  
}