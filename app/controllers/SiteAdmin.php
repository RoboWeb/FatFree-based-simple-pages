<?php
namespace controllers;

class SiteAdmin extends Site {

  protected $sMainTpl = "layout.php";
  protected $mPage;

  public $oNavigations;
  public $oCont;
  private $defaultMime = 'text/html';

  public function __construct(\Base $f3) {
    parent::__construct();
  }

  public function login() {
    $f3 = \Base::instance();
    $this->sMainTpl = 'login.php';
    //$f3->UI = $f3->ADMIN;
    $f3->set('POST.response', \inc\Auth::login());
    //echo $f3->stringify($f3->get('POST'));

  }

  public function afterRoute(\Base $f3, $params) {
    $f3->UI = $f3->ADMIN;
    echo \View::instance()->render($this->sMainTpl);
  }

  public function backEnd(\Base $f3, $params) {
    if(! \inc\Auth::isLoggedIn()) $f3->reroute('/'.$f3->get('backend.LOGIN'));
    $f3->UI = $f3->ADMIN;
    // set to hive CONFIG file, TODO: delete in future
    if($f3->DEBUG == 3) {
      $configfile = parse_ini_file('app/config.ini', TRUE);
      $f3->set('CONFIGFILE', $configfile);
    }
    // get all (max 10 offset 0) articles from database
    $articles = $f3->DB->exec('SELECT articles.*, users.sUserName, collections.sTitle AS sCollectionTitle FROM `articles` JOIN `users` ON articles.iAuthorId = users.iID JOIN `collections` ON articles.iCollectionId = collections.iID  ORDER BY articles.iID ASC LIMIT 10 OFFSET 0');

    $f3->set('articles', $articles);
    // get all pages from database
    $pages = $f3->DB->exec('SELECT * FROM `pages` ORDER BY `iID` ASC LIMIT 10 OFFSET 0');
    $f3->set('status_keys', array_keys($f3->get('status')));
    $f3->set('content_types_keys', array_keys($f3->get('content_type')));
    $f3->set('currentUser', ['iUserId' => $f3->BACKEND_USER['iID'], 'sUserName' => $f3->BACKEND_USER['sUserName']]);
    foreach($pages as $i => $page) {
      $test = json_decode($page['aContents'], TRUE);
      $pages[$i]['aContents'] = $test;
      $pages[$i]['aContentStatistics'] = $this->getStat($test);
    }
    $f3->set('pages', $pages);
  }


  // private functions
  // return nombers of types content in single page
  private function getStat($aContents) {
    $stat;
    // $f3 = \Base::instance();
    $contentTypes = array_keys(\Base::instance()->get('content_type'));
      foreach($aContents as $cont) {
        if(isset($stat[$contentTypes[$cont['iContentType']]])) $stat[$contentTypes[$cont['iContentType']]]++;
        else $stat[$contentTypes[$cont['iContentType']]] = 1;
        // $stat[0][] = $contentTypes[$cont['iContentType']];
      }
    return $stat;
  }



  private function getPatternById($iId) {
    if(!isset($iId) || !is_numeric($iId)) return null;
    if(is_null($this->aContentPatterns)) $this->loadPatterns();
    if(is_array($this->aContentPatterns)){
      foreach($this->aContentPatterns as $aPattern) {
        if($aPattern['iID'] == $iId) return $aPattern;
      }
    }
    return null;
  }

  private function getPatternByName($sName) {
    if(!isset($sName) || !is_string($sName)) return null;
    if(is_null($this->aContentPatterns)) $this->loadPatterns();
    if(is_array($this->aContentPatterns)){
      foreach($this->aContentPatterns as $aPattern) {
        if($aPattern['sName'] == $sName) return $aPattern;
      }
    }
    return null;
  }

  private function loadPatterns(){
    $this->aContentPatterns = $this->dbf->read('patterns');
  }
}
