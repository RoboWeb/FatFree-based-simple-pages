<?php

namespace inc;


class Auth {

  protected $response;
  protected $logg;

  /**
  * init the View
  */
  // public function beforeroute() {
  //     $this->response = new \View\Backend();
  // }

  /**
  * check login state
  * @return bool
  */
  static public function isLoggedIn() {
    /** @var Base $f3 */
    $f3 = \Base::instance();
    if ($f3->exists('SESSION.uid')) {
      $user = new \models\User();
      $user->load(['iID = :id', ':id' => $f3->get('SESSION.uid')]);
      if(!$user->dry()) {
          $f3->set('BACKEND_USER',$user);
          return true;
      }
    }
    return false;
  }

  /**
  * login procedure
  */
  static public function login() {

    $f3 = \Base::instance();
    if ($f3->exists('POST.login') && $f3->exists('POST.pass')) {
      sleep(1); // login should take a while to kick-ass brute force attacks
      $ident = self::mixIdent($f3->get('POST.login'), $f3->get('POST.pass'));
      $user = new \models\User();
      $user->load(['sLoginMail = :login',':login' => $f3->get('POST.login')]);

      if (!$user->dry()) {
        // // check hash engine
        $hash_engine = $f3->get('PASSWORD_HASH_ENGINE');
        $valid = false;
        if($hash_engine == 'bcrypt') {
          $valid = \Bcrypt::instance()->verify($ident,$user->sIdent);
        } elseif($hash_engine == 'md5') {
          $valid = (md5($ident.$f3->get('PASSWORD_MD5_SALT')) == $user->sIdent);
        }
        if($valid) {
          @$f3->clear('SESSION'); //recreate session id
          $f3->set('SESSION.uid',$user->iID);
          if($f3->get('CONFIG.SSL'))
            $f3->reroute('https://'.$f3->get('HOST').$f3->get('BASE').'/'.$f3->get('backend.ENTER'));
          else $f3->reroute('/'.$f3->get('backend.ENTER'));
        }
        return $valid;
      }
      ///////\Flash::instance()->addMessage('Wrong Username/Password', 'danger');

      //$f3->reroute('/admin/login');
      return FALSE;
    }
    return FALSE;
    //$f3->reroute('/admin/login');
  }

  public function logout($f3,$params) {
    $f3->clear('SESSION');
    $f3->reroute('http://'.$f3->get('HOST').$f3->get('BASE').'/');
  }

  static public function mixIdent($uname, $pass) {
    // $f3 = \Base::instance();
    $unameLen = strlen($uname);
    $passLen = strlen($pass);
    $longer = $unameLen >= $passLen ? $unameLen : $passLen;
    $mix; $a = 0; $un =  0; $pa = 0;
    while($a < $longer) {
      $mix .= $uname[$un] . $pass[$pa];
      $un++;
      $pa++;
      if($un > $unameLen) $un = 0;
      if($pa > $passLen) $pa = 0;
      $a++;
    }
    return $mix; //self::setIdent($mix);
  }
  /**
  * crypt password
  * @param $val
  * @return string
  */
  static public function setIdent($val) {
    $f3 = \Base::instance();
    $hash_engine = $f3->get('PASSWORD_HASH_ENGINE');
    switch($hash_engine) {
      case 'bcrypt':
      $crypt = \Bcrypt::instance();
      $val = $crypt->hash($val);
      break;
      case 'md5':
      // fall-through
      default:
      $val = md5($val.$f3->get('PASSWORD_MD5_SALT'));
      break;
    }
    return $val;
  }

}
