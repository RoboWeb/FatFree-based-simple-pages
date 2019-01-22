<?php
namespace inc;

class Utils extends \Base\Prefab  {

  public function minify(\Base $f3, $params) {
    $path = $f3->GET('UI') . 'assets/' . $params['type'].'/';
    $files = str_replace('../','',$_GET['files']);
    echo \Web::instance()->minify($files, null, true, $path);
  }

  /**
	*	Decode HTML entities to equivalent characters
	*	@return string
	*	@param $arg mixed
	**/
	public function html($arg) {
		$fw=\Base::instance();
		return $fw->recursive($arg,	function($val) use($fw) {
				return is_string($val) ? $fw->decode($val) : $val;
			}
		);
	}

  /**
	*	Decode time to value
	*	@return string
	*	@param $arg mixed
	**/
	public function year($arg) {
		$fw=\Base::instance();
		return date('Y', strtotime($arg));
	}
}
