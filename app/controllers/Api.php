<?php
namespace controllers;

class Api {
  private $defaultMime = 'application/json';
  public function __construct(){
    $f3 = \Base::instance();
    $f3->set('DBF', new \DB\Jig ( 'app/data/files/', \DB\Jig::FORMAT_JSON));
    $f3->set('DB', new \DB\SQL('sqlite:app/data/database.sqlite'));
    if(! \inc\Auth::isLoggedIn()) $f3->reroute('/'.$f3->get('backend.LOGIN'));
    header('Content-Type: ' . $this->defaultMime);
  }
  public function settings(\Base $f3, $params) {
    $configfile = parse_ini_file('app/config.ini', TRUE);
    $configChange = false;
    if(null !== $f3->get('POST.iDevMode')) if($configfile['globals']['DEV'] !== $f3->get('POST.iDevMode')) {
        $configfile['globals']['DEV'] = $f3->get('POST.iDevMode') == 'true' ? 1 : 0;
        $configChange = true;
    }
    if(null !== $f3->get('POST.iDebug')) if($configfile['globals']['DEBUG'] !== $f3->get('POST.iDebug')){
      $configfile['globals']['DEBUG'] = intval($f3->get('POST.iDebug'));
      $configChange = true;
    }
    if(null !== $f3->get('POST.iCache')) if($configfile['globals']['CACHE'] !== $f3->get('POST.iCache')){
      $configfile['globals']['CACHE'] = $f3->get('POST.iCache') == 'true' ? 1 : 0;
      $configChange = true;
    }
    if(null !== $f3->get('POST.sTimeZone')) if($configfile['globals']['TZ'] !== $f3->get('POST.sTimeZone')){
      $configfile['globals']['CACHE'] = $f3->get('POST.sTimeZone');
      $configChange = true;
    }
    if(null !== $f3->get('POST.sTitle')) if($configfile['site']['TITLE'] !== $f3->get('POST.sTitle')) {
        $configfile['site']['TITLE'] = $f3->get('POST.sTitle');
        $configChange = true;
    }
    if(null !== $f3->get('POST.sKeywords')) if($configfile['site']['KEYWORDS'] !== $f3->get('POST.sKeywords')) {
        $configfile['site']['KEYWORDS'] = $f3->get('POST.sKeywords');
        $configChange = true;
    }
    if(null !== $f3->get('POST.sDescription')) if($configfile['site']['DESCRIPTION'] !== $f3->get('POST.sDescription')) {
        $configfile['site']['DESCRIPTION'] = $f3->get('POST.sDescription');
        $configChange = true;
    }
    if(null !== $f3->get('POST.iHomePage')) if($configfile['site']['HOME_PAGE'] !== $f3->get('POST.iHomePage')) {
        $configfile['site']['HOME_PAGE'] = intval($f3->get('POST.iHomePage'));
        $configChange = true;
    }
    if($configChange) echo $f3->write('app/config.ini', $this->setIniString($configfile));
    else echo '0: no change';
  }

  // set string from array whit data of ini file type
  // return string ready to save as .ini file
  private function setIniString($arr){
    $str;
    foreach($arr as $section => $sett){
      $str .= "[" . $section . "]\r\n";
      foreach($sett as $key => $val) {
        $str .= "$key = $val\r\n";
      }
      $str .= "\r\n";
    }
    return $str;
  }
}
