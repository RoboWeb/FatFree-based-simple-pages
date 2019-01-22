<?php
namespace controllers;

class Site {

  protected $oBase;
  protected $sMainTpl = "layout.html";
  protected $mPage;
  protected $aJS = [];

  public $oNavigations;
  private $defaultMime = 'text/html';
  private $logg;

  public function __construct() {
    $this->logg = new \Log('site.log');
    $this->oBase = \Base::instance();
    $this->aJS = [
      \Base::instance()->get('TEMPLATE') . 'assets/js/skel.min.js',
      \Base::instance()->get('TEMPLATE') . 'assets/js/vue.min.js',
      \Base::instance()->get('TEMPLATE') . 'assets/js/flyform.vue.js'
    ];
    $this->oBase->set('DBF', new \DB\Jig ( 'app/data/', \DB\Jig::FORMAT_JSON));
    //$oBase->set('DB', new \DB\SQL('sqlite:app/data/database.sqlite'));
  }

  public function getHomePage($f3, $params) {
    $params['id'] = $this->oBase->get('site.HOME_PAGE');
    $this->sMainTpl = "home-layout.html";
    $this->getPage($f3, $params);
    $this->mPage['aVariables']['aRandomRooms'] = $this->_getRandomRooms(3);
    $this->mPage['aVariables']['aSlides'] = $this->_getSlides();
    $this->_registerScript([
      "https://maps.googleapis.com/maps/api/js?key=AIzaSyAgUTVi1cX8IcUxcGOobSJ93FQVR59-KSQ&language=pl",
      \Base::instance()->get('TEMPLATE') . 'assets/js/gmap-styled.js',
      \Base::instance()->get('TEMPLATE') . 'assets/js/slider.vue.js'
    ]);
  }

  public function getAllRooms($f3, $params) {
    $params['id'] = 3;
    $this->getPage($f3, $params);
    $this->mPage['aVariables']['aRooms'] = $this->_getRooms();
  }

  public function getRoom($f3, $params) {
    $params['id'] = 4;
    $this->getPage($f3, $params);
    $this->_registerScript([\Base::instance()->get('TEMPLATE') . 'assets/js/gallery.vue.js']);
    $this->mPage['aVariables'] = array_merge($this->mPage['aVariables'], $this->_getItemBySlug($this->_getRooms(), $params['slug']));
    $this->mPage['sTitle'] = $this->mPage['aVariables']['sName'];
    //$this->mPage['aVariables']['sBannerBg'] = $this->mPage['aVariables']['sMainImage'];
    $this->mPage['aVariables']['sGallery'] = $this->_getImages($this->mPage['aVariables']['sGalleryDir']);
  }

  public function getRestaurant($f3, $params) {
    $params['id'] = 5;
    $this->getPage($f3, $params);
    $this->mPage['aVariables']['aDishes'] = $this->_getDishes();
  }

  public function getDishesCategory($f3, $params) {
    $params['id'] = 6;
    $this->getPage($f3, $params);
    $params['mPage'] = $this->mPage;

    $this->mPage['aVariables'] = $this->_getItemBySlug($this->_getDishes(), $params['dish']);
    $this->mPage['sTitle'] = $this->mPage['aVariables']['sCategory'];
    //$this->mPage['aVariables']['sBannerBg'] = $this->mPage['aVariables']['sCategoryBg'];
  }

  public function getGarden($f3, $params) {
    $params['id'] = 7;
    $this->getPage($f3, $params);
    $this->mPage['aVariables']['sGallery'] = $this->_getImages($this->mPage['aVariables']['sGalleryDir']);
  }

  public function getGalleries($f3, $params) {
    $params['id'] = 8;
    $this->getPage($f3, $params);
    $this->mPage['aVariables']['aGalleries'] = $this->_getGalleries();
  }

  public function getGallery($f, $params) {
    $params['id'] = 9;
    $this->getPage($f, $params);
    $this->_registerScript([\Base::instance()->get('TEMPLATE') . 'assets/js/gallery.vue.js']);
    $this->mPage['aVariables'] = array_merge($this->mPage['aVariables'], $this->_getItemBySlug($this->_getGalleries(), $params['slug']));
    $this->mPage['aVariables']['aGalleries'] = $this->_getGalleries();
    $this->mPage['sTitle'] = "Galeria: " . $this->mPage['aVariables']['sCategory'];
    //$this->mPage['aVariables']['sBannerBg'] = $this->mPage['aVariables']['sMainImage'];
    $this->mPage['aVariables']['sGallery'] = $this->_getImages($this->mPage['aVariables']['sGalleryDir']);
  }

  public function getKontakt($f3, $params) {
    $params['id'] = 10;
    $this->getPage($f3, $params);
    $this->_registerScript([
      "https://maps.googleapis.com/maps/api/js?key=AIzaSyAgUTVi1cX8IcUxcGOobSJ93FQVR59-KSQ&language=pl",
      \Base::instance()->get('TEMPLATE') . 'assets/js/gmap-styled.js'
    ]);
  }

  public function getPage($f3,$params) {

    if (isset($params['id']))
      $page = $this->_getPageByID($params['id']);
    elseif (isset($params['slug']))
      $page = $this->_getPageBySlug($params['slug']);
    else $this->oBase->error(404);

    if (!isset($page['iPageID']) || !$page) $this->oBase->error(404);

    $page['sMainTemplate'] = (!is_null($page['sMainTemplate']) && $page['sMainTemplate'] !== $this->sMainTpl) ? $page['sMainTemplate'] : $this->sMainTpl;

    $this->mPage = $page;
  }
  private function _getPageByID($id) {
    $pages = $this->oBase->DBF->read('pages.json');
    foreach ($pages as $i => $page) {
      if($page['iPageID'] == $id) return $page;
    }
    return false;
  }
  private function _getPageBySlug($slug) {
    $pages = $this->oBase->DBF->read('pages.json');
    foreach ($pages as $i => $page) {
      if($page['sSlug'] == $slug) return $page;
    }
    return false;
  }
  private function _getRooms(){
    return $this->oBase->DBF->read('rooms.json');
  }
  private function _getRandomRooms($n) {
    $rooms = $this->_getRooms();
    $rrooms = [];
    $indxs = [];
    $i = 0;
    $all = count($rooms) - 1;
    while($i != $n){
      $indx = mt_rand(0, $all);
      if(!in_array($indx, $indxs)) {
        $indxs[$i] = $indx;
        $rrooms[$i] = $rooms[$indx];
        $i++;
      }
    }
    return $rrooms;
  }

  private function _getDishes(){
    return $this->oBase->DBF->read('dishes.json');
  }

  private function _getSlides() {
    return $this->oBase->DBF->read('slides.json');
  }

  private function _getGalleries() {
    return $this->oBase->DBF->read('galleries.json');
  }

  private function _getImages($dir, $i = 0, $sort = true) {
    $gl = new \FAL\LocalFS($dir);
    $list = [];
    foreach($gl->listDir() as $key => $img) {
      if($img['type'] == 'dir') {
        $res = array_merge($list, $this->_getImages($img['path'], $i, false));
        $list = $res;
        $i = count($list);
      } else {
        $list[$i]['sFile'] = $key;
        $list[$i]['sPath'] = $img['path'];
        // $keys[$i] = $key;
        $i++;
      }
    }
    foreach($list as $i => $img){
      $keys[$i] = $img['sFile'];
    }
// $this->logg->write($this->arr2str($list));
    if($sort) array_multisort($keys, SORT_ASC, SORT_STRING, $list);
// $this->logg->write($this->arr2str($list));
    return $list;
  }

  private function _getItemBySlug($arr, $slug) {
    $all = count($arr);
    for($i = 0; $i < $all; $i++){
      if($arr[$i]['sSlug'] == $slug) {
        $arr[$i]['sPrevItemUrl'] = isset($arr[$i - 1]) ? $arr[$i - 1]['sPermalink'] : $arr[$all - 1]['sPermalink'];
        $arr[$i]['sNextItemUrl'] = isset($arr[$i + 1]) ? $arr[$i + 1]['sPermalink'] : $arr[0]['sPermalink'];
        return $arr[$i];
      }
    }
    return false;
  }
 /*
  * breadcrumbs method
  *
  */
  public function breadcrumbs($home = 'Home') {

    $path = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    $base = $this->oBase->get('BASE');
    $last = end($path);

    // Build the rest of the breadcrumbs
    foreach ($path AS $x => $crumb) {
      // Our "title" is the text that will be displayed (strip out .php and turn '_' into a space)
      $title = ucwords($crumb);

      // If we are not on the last index, then display an <a> tag
      if ($crumb !== $last){
        $breadcrumbs[$x]['sUrl'] = $base . ( $x == 1 ? "" : "/" . $crumb);
        $breadcrumbs[$x]['sTitle'] = $title;
        // Otherwise, just display the title (minus)
      } else $breadcrumbs[$x]['sTitle'] = $title;
    }

    // Build our temporary array (pieces of bread) into one big string :)
    return $breadcrumbs;
  }

  /*
  * afterRoute method
  *
  */
  public function afterRoute($params) {
    $this->oNavigations = new \models\Navigation();
    $this->oBase->set('navigation', $this->oNavigations->aContentData);
    //$this->oBase->set('breadcrumbs', $this->breadcrumbs());
    $this->_registerScript(\Base::instance()->get('TEMPLATE') . 'assets/js/main.js');
    $this->oBase->set('scripts', $this->aJS);
    $this->oBase->set('mPage', $this->mPage);

    echo \Template::instance()->render($this->sMainTpl);
  }

  private function _registerScript($script) {
    $res = array_merge($this->aJS, (is_array($script) ? $script : [$script]));
    $this->aJS = $res;
  }


  private function arr2str($arr) {
    $str = "";
    foreach($arr as $key => $val) {
      $str .= $key." => ".(is_array($val) ? "[".$this->arr2str($val)."], " : $val.", ");
    }
    return $str;
  }

}
