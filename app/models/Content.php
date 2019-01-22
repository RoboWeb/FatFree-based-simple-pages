<?php
namespace models;

class Content {

  const STATUS_DRAFT    = 0;
  const STATUS_PENDING  = 1;
  const STATUS_PUBLISH  = 2;
  const STATUS_ARCHIVED = 3;
  const STATUS_TRASHED  = 4;

  const ATTACHEMENT_TYPE_FILE = 0;
  const ATTACHEMENT_TYPE_BLOCK = 1;
  const ATTACHEMENT_TYPE_ARTICLE = 2;

  protected $sTableName;
  public $aContentData;
  public $aContentFields;
  protected $aContentPattern;

  /* content patterns example
  public $patterns = [
    [
      'iIndex' => 0,
      'sName' => 'article',
      'sLabel' => "Artykuł",
      'sDefaultTemplate' => 'article.html',
      'aFields' => [
        'iID' => ['type' => 'INTEGER', 'require' => TRUE, 'unique' => TRUE, 'autoincrement' => TRUE],
        'sTitle' => ['type' => 'TEXT', 'require' => TRUE, 'maxlength' => 250, 'minlength' => 3],
        'sSlug' => ['type' => 'TEXT', 'require' => TRUE, 'maxlength' => 250, 'minlength' => 3, 'unique' => TRUE],
        'sPermalink' => ['type' => 'TEXT', 'require' => TRUE, 'maxlength' => 500, 'minlength' => 5, 'unique' => TRUE],
        'sTeaser' => ['type' => 'TEXT', 'maxlength' => 1024],
        'hContent' => ['type' => 'HTML'],
        'iAuthorId' => ['type' => 'INTEGER', 'require' => TRUE],
        'sAuthorSignature' => ['type' => 'TEXT', 'maxlength' => 250],
        'tCreateDate' => ['type' => 'DATETIME', 'require' => TRUE],
        'tLastModifiedDate' => ['type' => 'DATETIME', 'require' => TRUE],
        'tPublishingDate' => ['type' => 'DATETIME'],
        'tFinishPublishingDate' => ['type' => 'DATETIME'],
        'iStatus' => ['type' => 'SELECT', 'options' => ['Draft', 'Pending', 'Publish', 'Archived', 'Trashed']],
        'sPermissions' => ['type' => 'TEXT', 'require' => TRUE, 'default' => '15F'],
        'aAttachements' => ['type' => 'ARRAY'],
        'aImages' => ['type' => 'ARRAY'],
        'aAttributes' => ['type' => 'ARRAY'],
        'iHits' => ['type' => 'INTEGER', 'require' => TRUE, 'default' => 0]
      ]
    ],
    [
      'iIndex' => 1,
      'sName' => 'banner',
      'sLabel' => "Baner",
      'sDefaultTemplate' => 'banner.html',
      'aFields' => [
        'iID' => ['type' => 'INTEGER', 'require' => TRUE, 'unique' => TRUE, 'autoincrement' => TRUE],
        'sTitle' => ['type' => 'TEXT', 'require' => TRUE, 'maxlength' => 250, 'minlength' => 3],
        'sDescription' => ['type' => 'TEXT', 'maxlength' => 1024],
        'sImage' => ['type' => 'URL', 'require' => TRUE],
        'sTargetUrl' => ['type' => 'URL'],
        'iAuthorId' => ['type' => 'INTEGER', 'require' => TRUE],
        'tCreateDate' => ['type' => 'DATETIME', 'require' => TRUE],
        'tLastModifiedDate' => ['type' => 'DATETIME', 'require' => TRUE],
        'tPublishingDate' => ['type' => 'DATETIME'],
        'tFinishPublishingDate' => ['type' => 'DATETIME'],
        'iStatus' => ['type' => 'SELECT', 'options' => ['Draft', 'Pending', 'Publish', 'Archived', 'Trashed']],
        'sPermissions' => ['type' => 'TEXT', 'require' => TRUE, 'default' => '15F'],
        'iHits' => ['type' => 'INTEGER', 'require' => TRUE, 'default' => 0],
        'iMaxHits' => ['type' => 'INTEGER']
      ]
    ],
    [
      'iIndex' => 2,
      'sName' => 'form',
      'sLabel' => "formularz kontaktowy",
      'sDefaultTemplate' => 'contact.html',
      'aFields' => [
        'iID' => ['type' => 'INTEGER', 'require' => TRUE, 'unique' => TRUE, 'autoincrement' => TRUE],
        'sTitle' => ['type' => 'TEXT', 'require' => TRUE, 'maxlength' => 250, 'minlength' => 3],
        'sSlug' => ['type' => 'TEXT', 'require' => TRUE, 'maxlength' => 250, 'minlength' => 3, 'unique' => TRUE],
        'sPermalink' => ['type' => 'TEXT', 'require' => TRUE, 'maxlength' => 500, 'minlength' => 5, 'unique' => TRUE],
        'sTeaser' => ['type' => 'TEXT', 'maxlength' => 1024],
        'aFormSettings' => [
          'action' => ['type' => 'TEXT', 'require' => TRUE, 'default' => 'form/contact'],
          'method' => ['type' => 'OPTION', 'require' => TRUE, 'options' => ['POST', 'GET']]
        ],
        'aFormFields' => [
          ['sField' => 'INPUT', 'sType' => 'TEXT', 'sName' => 'name', 'sPlaceholder' => 'Imię i Nazwisko', 'bRequire' => TRUE, 'iMinLength' => 5, 'iMaxLength' => 250],
          ['sField' => 'INPUT', 'sType' => 'EMAIL', 'sName' => 'email', 'sPlaceholder' => "E-mail", 'bRequire' => TRUE, 'iMaxLength' => 250],
          ['sField' => 'INPUT', 'sType' => 'TEXT', 'sName' => 'subject', 'sPlaceholder' => 'Tytuł', 'bRequire' => TRUE, 'iMinLength' => 3, 'iMaxLength' => 250],
          ['sField' => 'TEXTAREA', 'sName' => 'message', 'sPlaceholder' => 'Wiadomość', 'bRequire' => TRUE, 'iMaxLength' => 1024],
          ['sField' => 'INPUT', 'sType' => 'SUBMIT', 'sValue' => 'Wyślij wiadomość'],
          ['sField' => 'INPUT', 'sType' => 'RESET', 'sValue' => 'Wyczyść']
        ],
        'hContent' => ['type' => 'HTML'],
        'iAuthorId' => ['type' => 'INTEGER', 'require' => TRUE],
        'sAuthorSignature' => ['type' => 'TEXT', 'maxlength' => 250],
        'tCreateDate' => ['type' => 'DATETIME', 'require' => TRUE],
        'tLastModifiedDate' => ['type' => 'DATETIME', 'require' => TRUE],
        'tPublishingDate' => ['type' => 'DATETIME'],
        'tFinishPublishingDate' => ['type' => 'DATETIME'],
        'iStatus' => ['type' => 'SELECT', 'options' => ['Draft', 'Pending', 'Publish', 'Archived', 'Trashed']],
        'sPermissions' => ['type' => 'TEXT', 'require' => TRUE, 'default' => '15F'],
        'aAttachements' => [],
        'aImages' => ['type' => 'ARRAY'],
        'aAttributes' => ['type' => 'ARRAY'],
        'iHits' => ['type' => 'INTEGER', 'require' => TRUE, 'default' => 0]
      ]
    ]
  ];
  */

  public function __construct(){}

  public function getTable(){
    return $this->sTableName;
  }

  public function loadContent($id=NULL){
    $f3 = \Base::instance();
    if(is_null($id)) $f3->error(404);
    $this->aContentData = $f3->DB->exec('SELECT * FROM `'.$this->sTableName.'` WHERE `iID`=?', $id)[0];
  }

}
