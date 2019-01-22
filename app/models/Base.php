<?php 

namespace models;

class Base extends \Base\DB\Cursor {
  
  protected $sTable, $oDB, $aFieldConf;
  
  /*
   * init the model
   */ 
  public function _construct(){
    $f3 = \Base::instance();
    $this->sTable = $f3->get('DB_TABLE_PREFIX').$this->sTable;
    // $this->oDB = 'DB';
    parent::__construct();
    // validation & error handler
    $class = get_called_class(); // PHP 5.3 bug
    $saveHandler = function(\Base\DB\Cursor $self) use($class) {
      $valid = true;
      foreach($self->getFieldConfiguration() as $kField => $vConf) {
        if (isset($vConf['type'])) {
          $val = $self->get($kField);
          $model = strtolower(str_replace('\\','.',$class));
          // check required fields
          if (isset($vConf['required']))
            $valid = \Validation::instance()->required($val,$kField,'error.'.$model.'.'.$kField);
          // check unique
          if (isset($conf['unique']))
            $valid = \Validation::instance()->unique($self,$val,$field,'error.'.$model.'.'.$field);
          if (!$valid)
            break;
        }
      }
      return $valid;
    };
    $this->beforesave($saveHandler);
  }
  
  /**
	 * returns model field conf array
	 * @return array|null
	 */
	public function getFieldConfiguration(){
		return $this->aFieldConf;
	}
}
