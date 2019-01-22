<?php

namespace models;

class Page extends \DB\SQL\Mapper {
  public $logg;
  protected $aFields = [
    'iID' => [
      'type' => 'INTEGER',
      'require' => TRUE,
      'unique' => TRUE,
      'autoincrement' => TRUE
    ],
    'sSlug' => [
      'type' => 'TEXT',
      'require' => TRUE
    ],
    'sTitle' => [
      'type' => 'TEXT',
      'require' => TRUE
    ],
    'sDefaultMainTemplate' => [
      'type' => 'TEXT'
    ],
    'aContents' => [
      'type' => 'JSON',
      'require' => TRUE
    ],
    'iStatus' => [
      'type' => 'OPTION',
      'select' => [
        'DRAFT' => 0, //\Base::instance()->status['status.DRAFT'],
        'PENDING' => 1, //\Base::instance()->get('status.PENDING'),
        'PUBLISH' => 2, //\Base::instance()->get('status.PUBLISH'),
        'ARCHIVED' => 3, //\Base::instance()->get('status.ARCHIVED'),
        'TRASHED' => 4, //\Base::instance()->get('status.TRASHED')
      ],
      'default' => 0,
      'require' => TRUE
    ]
  ];

  public function __construct() {
    parent::__construct(\Base::instance()->get('DB'), 'pages');
  }

}
