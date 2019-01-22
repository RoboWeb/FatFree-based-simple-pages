<?php

// Kickstart the framework
$f3=require('lib/base.php');

$f3->set('DEBUG',1);
if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

// Load configuration
$f3->config('app/config.ini', TRUE);
$f3->set('ONERROR',
		function($f3) {
	// recursively clear existing output buffers:
				while (ob_get_level())
						ob_end_clean();
				// your fresh page here:
				echo "[" . $f3->get('ERROR.status') . "] " . $f3->get('ERROR.code') . ": " . $f3->get('ERROR.text') . "<br/>";
				echo $f3->get('ERROR.trace');
		}
);

$f3->set('AUTOLOAD','app/');

$f3->config('app/routes.ini', TRUE);
$f3->set('DATETIME', [
'YEAR' => date('Y')
]);
$f3->set("MADEBY", "<div class=\"agency\"><i class=\"rcom-logo icon-reprezentuj\"></i><p>stronę przygotowała<br/>agencja reklamowa <a href=\"http://reprezentuj.com\" target=\"_blank\">Reprezentuj.com</a><p></div></div>");

//$f3->route('GET /', 'controller\Page->index');

$f3->run();
