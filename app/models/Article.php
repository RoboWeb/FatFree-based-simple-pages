<?php
namespace models;

class Article extends Content {

  protected $sTableName = 'articles';

  public function __construct($iId){
    parent::__construct();
    $this->loadContent($iId);
  }

}
