<?php
/*******************************************************************************
* RPDF                                                                         *
*                                                                              *
* Version: 1.0                                                                *
* Date:    2016-05-30                                                         *
* Author:  Tomasz WOLNY                                                     *
*******************************************************************************/
namespace inc\fpdf;

class RPDF extends fPDF {
  public
  $subject = "brief",
  $title,
  $author = "Reprezentuj.com",
  $creator = "rcom site";
  private $currX, $charset = "UTF-8";

  public function __construct($docTitle, $orientation='P', $unit='mm', $size='A4') {
    Parent::__construct($orientation, $unit, $size);

    $this->addRalewayFonts();

    $this->AliasNbPages();
    $this->setAuthor($this->author);
    $this->setCreator($this->creator);
    $this->setTitle($docTitle);
    $this->SetDisplayMode('real');
    $this->SetMargins(20,15,20);
    $this->SetAutoPageBreak(true, 25);
    $this->title = $docTitle;


  }

  public function addSection($secTitle) {
    $this->SetFont('RalewayL', '', 14);
    $this->SetTextColor(0,0,0);
    $this->Cell(0,15,iconv('UTF-8', 'ISO-8859-2', $secTitle),0,1,'L');
  }

  public function addRow($label, $value) {

    $this->SetFont('Raleway', 'B', 10);
    $this->SetTextColor(0,0,0);
    $this->Write(5, iconv('UTF-8', 'ISO-8859-2', $label) . '  ');
    $this->SetFont('Raleway', '', 10);
    $this->Write(5, iconv('UTF-8', 'ISO-8859-2', $value));

    $this->Ln();
  }
  // Page header
  function Header()
  {
    $f3 = \Base::instance();
    // Logo
    // $this->Image($f3->UI.'/images/rcom_1920x500_pdf_header.jpg',0,0,210,50);
    $this->setFillColor(0);
    $this->Rect(0,0,210,50,'F');
    // Arial bold 15
    $this->SetFont('RalewayEB','',35);
    $this->SetTextColor(255);
    $this->SetY(20);
    // Title
    $this->Cell(0,15,"R.BRIEF",0,1,'C');
    // Line break
    $this->Ln(20);
  }

  // Page footer
  function Footer() {
    $f3 = \Base::instance();
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Raleway','I',8);
    $this->SetTextColor(255);
    $this->setFillColor(0);
    $this->Rect(0,275,210,25,'F');
    // $this->Image($f3->UI.'/images/rcom_1920x300_pdf_footer.jpg',0,280,210);
    // Page number
    $this->Cell(0,10,'(C) Copyright ' . Date('Y') . ' ' . $f3->site['TITLE'], 0,0,'C');
    $this->SetY(-15);
    $this->Cell(0,10,'Strona '.$this->PageNo().'/{nb}',0,0,'R');
  }

  private function addRalewayFonts() {
    $this->AddFont('Raleway', '', 'raleway.php'); //Regular
    $this->AddFont('Raleway', 'I', 'ralewayi.php'); //Regular italic
    $this->AddFont('Raleway', 'B', 'ralewayb.php'); //Regular Bold
    $this->AddFont('RalewayEB', '', 'ralewayeb.php'); //Extra Bold
    $this->AddFont('RalewayL', '', 'ralewayl.php');//Light

  }
}
