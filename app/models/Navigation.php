<?php
namespace models;

class Navigation extends Content {

  protected $sTableName = 'navigation.json';
  private $aNavigationsFields = [
    [
      'iID' => [
        'type' => 'INTEGER',
        'require' => TRUE,
        'unique' => TRUE,
        'autoincrement' => TRUE
      ],
      'sName' => [
        'type' => 'TEXT',
        'minlength' => 5,
        'maxlength' => 120,
        'unique' => TRUE,
        'require' => TRUE
      ],
      'sLabel' => [
        'type' => 'TEXT',
        'maxlength' => 250
      ],
      'aPositions' => [
        'type' => 'LIST',
        'format' => 'FORMAT_JSON',
        'fields' => [
          'sTagId' => [
            'type' => 'TEXT'
          ],
          'sUrl' => [
            'type' => 'TEXT',
            'require' => TRUE
          ],
          'sText' => [
            'type' => 'TEXT',
            'require' => TRUE
          ],
          'sTagClass' => [
            'type' => 'TEXT'
          ],
          'aTagAttr' => [
            'type' => 'TEXT'
          ]
        ]
      ]
    ]
  ];

  public function __construct(){
    parent::__construct();
    $this->aContentFields = $this->aNavigationsFields;
    $this->aContentData = \Base::instance()->DBF->read($this->getTable());
  }

  public function getNavi($sName = ''){
    return $sName == '' ? $this->aContentData : $this->getPart($sName);
  }

  private function getPart($sName) {
    foreach ($this->aContentData as $itm) {
      if($itm->sName == $sName) return $itm;
    }
    return null;
  }

}
