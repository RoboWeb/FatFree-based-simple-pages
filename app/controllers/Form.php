<?php
namespace controllers;

class Form {

  protected $oBase;
  private $defaultMime = 'application/json';
  private $flashMessage;
  private $statements;
  private $accepted_urls = ['localhost', 'roboweb.eu', 'hotelretro.eu', 'www.hotelretro.eu'];
  private $logg;

  public function __construct() {
    $this->logg = new \Log('site.log');
    $this->oBase = \Base::instance();
    $this->oBase->set('DBF', new \DB\Jig ( 'app/data/', \DB\Jig::FORMAT_JSON));
    //$oBase->set('DB', new \DB\SQL('sqlite:app/data/database.sqlite'));

  }

  public function formSubmit($f3, $params){
    $audit = \Audit::instance();
    $data = $this->oBase->DBF->read('forms.json');
    $cff = $data[0]['contactForm']['fields'];

    if(empty($f3->POST)) $this->flashMessage = ['id' => 300, 'cont' => 'Nic nie zostało wysłane.'];
    elseif (!in_array($_SERVER['HTTP_HOST'], $this->accepted_urls ))
      $this->flashMessage = ['id' => 320, 'cont' =>  'Błędne dane! ' . $_SERVER['HTTP_HOST']]; //'Wiadomość próbuje zostać wysłana ze strony innej niż firma.dalejrazem.pl. Możliwy atak! - '.$_SERVER['HTTP_HOST'];
    else {
      foreach($cff as $key => $val) {
        if(!isset($f3->POST[$key]))
          $this->setFieldError($key, 'empty');
        elseif(!$this->isValid($f3->POST[$key], $val))
          $this->setFieldError($key, 'invalid', $f3->POST[$key]);
      }
      if(is_array($this->statements)) $this->flashMessage = ['id' => 330, 'stats'=>$this->statements];
      else $this->sendMail($f3, $f3->POST);
    }
    // if(!is_array($this->flashMessage))
    //   $this->flashMessage = 100;

  }

  public function getFields() {
    $data = $this->oBase->DBF->read('forms.json');
    $this->flashMessage = $data[0]['contactForm'];
  }
  /*
  * afterRoute method
  *
  */
  public function afterRoute($params) {
    header('Content-Type: ' . $this->defaultMime);
    echo json_encode($this->flashMessage, JSON_FORCE_OBJECT);
  }

  /* private helper methods */
  protected function sendMail($f3, $data) {

    $address_ip = $f3->SERVER['REMOTE_ADDR'];
    $user_data = $f3->SERVER['HTTP_USER_AGENT'];
    extract($f3->formsend);
    $smtp = new \SMTP($HOST, $PORT, $SCHEME, $USER, $PASS);

    $smtp->set('Subject', "Mail z formularza: " . date("Y-m-d H:i:s"));
    $smtp->set('To', $RECIPIENT);
    $smtp->set('Bcc', 'ykee.tomasz.wolny@gmail.com');
    $smtp->set('From', 'Obsługa formularzy <'.$USER.'>');
    $smtp->set('Content-type', "text/html; charset=UTF-8");
    $smtp->set('MIME-Version', "1.0");
    $smtp->set('Reply-To', $data['name']."<".$data['email'].">");
    $smtp->set('X-Mailer', 'PHP/'.phpversion());
    $message = ""
      ."<p>Adresat: <strong>".$data['name']."</strong> </p>"
      ."<p>E-mail: <strong>".$data['email']."</strong></p>"
      ."<p>Telefon: <strong>".$data['phone']."</strong></p>"
      ."<p>Tresc:<br/>".$data['message']."</p>"
      .$message .= "<br><br>"
      ."<p>Zgoda na przetwarzanie moich danych osobowych dla potrzeb niezbędnych do komunikacji poprzez formularz kontaktowy (zgodnie z Ustawą z dnia 29.08.1997 roku o Ochronie Danych Osobowych; tekst jednolity: Dz. U. z 2002r. Nr 101, poz. 926 ze zm.): "
      ."<strong>".$data['agreement']."</strong></p>"
      ."<p>Akceptacja polityki prywatności: <strong>".$data['accept']."</strong></p>"
      ."<br/><br/>"
      ."<p>Adres IP: <strong>".$address_ip."</strong></p>"
      ."<p>USER_AGENT: <strong>".$user_data."</strong></p>";
    if($smtp->send($message)) $this->flashMessage = 100;
    else $this->flashMessage = ['id' => 340, 'cont' => "Nie udało się wysłać maila. <br/>Mailer Error: " . $this->arr2str($smtp->log()) ." <- ".$HOST." ".$USER];
  }

  private function setFieldError($k, $type, $v = '') {
    $this->statements[] = $type === 'empty' ? ['name' => $k, 'type' => $type] : ['name' => $k, 'send' => $v, 'type' => $type];
  }

  private function isValid($nVal, $field) {
    $field['value'] = $nVal;
    if (['checkbox' => 1][$field['type']]) return true;
    return preg_match($this->_getPattern($field['type']), $nVal) &&
    (strlen($field['value']) >= (isset($field['charsNumber']['min']) ? $field['charsNumber']['min'] : 0)) &&
    (strlen($field['value']) <= (isset($field['charsNumber']['max']) ? $field['charsNumber']['max'] : 120));
    // return false;
  }
  private function _getPattern($type) {
    switch($type){
      case 'name':
        return "/^(([\w-ĄĆĘŁÓŚŹŻąćęłńóśźż]+)\s([\w-ĄĆĘŁÓŚŹŻąćęłńóśźż]+)){1,2}$/";
        break;
      case 'phone':
        return "/^(\+\d{2}\ {0,1}){0,1}(\({0,1}\d{2,3}\){0,1}\ {0,1}){0,1}(\d{2,3}(\ |\-){0,1}){3}$/";
        break;
      case 'email':
        return "/^(\w+\.{0,}\-{0,}){1,}@{1}(\w+\.{0,}\-{0,}){1,}(\.{1}\w{2,3}){1}$/";
        break;
      case 'text':
      case 'textarea':
        return "/^([a-zA-Z\-ĄĆĘŁÓŚŹŻąćęłńóśźż\s\d_\/\'\"\&\(\)\,\.\:]+)$/";
        break;
    }
  }
  private function arr2str($arr) {
    $str = "";
    foreach($arr as $key => $val) {
      $str .= $key." => ".(is_array($val) ? "[".$this->arr2str($val)."], " : $val.", ");
    }
    return $str;
  }
}
