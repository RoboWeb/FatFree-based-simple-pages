<?php
namespace models;

class User extends \DB\SQL\Mapper {
  public $logg;
  protected $sTable = 'users';
  protected $aFields = [
    'iID' => [
      'type' => 'INTEGER',
      'required' => TRUE,
      'unique' => TRUE,
      'autoincrement' => TRUE
    ],
    'sUserName' => [
      'type' => 'STRING',
      'require' => TRUE,
      'unique' => TRUE
    ],
    'sLoginMail' => [
      'type' => 'EMAIL',
      'require' => TRUE,
      'unique' => TRUE
    ],
    'sIdent' => [
      'type' => 'TEXT',
      'required' => TRUE,
      'unique' => TRUE
    ],
    'iStatus' => [
      'type' => 'INTEGER',
      'required' => TRUE,
      'default' => 1
    ],
    'iGroupId' => [
      'type' => 'INTEGER',
      'required' => TRUE,
      'foreign' => 'user_groups.iID'
    ]
  ];

  public function __construct() {
    parent::__construct(\Base::instance()->get('DB'), $this->sTable);
  }

}
