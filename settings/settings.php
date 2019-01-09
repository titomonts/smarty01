<?php
ini_set('max_execution_time', '90');
ini_set('session.gc_maxlifetime',3600);
require 'config/config.php';

/* for CLASSES_PATH, SMARTY_PATH, SMARTY_PATH declaration, see config/config.php */
require CLASSES_PATH.'stringv.php';
require CLASSES_PATH.'mobile.php';
require SMARTY_PATH.'Smarty.class.php';

/* Start DB Connection 
$data['host'] = 'localhost';
$data['username'] = 'root';
$data['password'] = 'hcnom2031055';
$data['db'] = 'ehs_unified';
$image_CDN_URL = 'http://uat-admin.elitehavenssales.com';
$data['image_CDN'] = $image_CDN_URL;

$mysqli = new mysqli($data['host'],$data['username'],$data['password'],$data['db']);
  
if ($mysqli->connect_errno):
  echo "Failed to connect to MySQL: ".$mysqli->connect_error;
  exit();
endif;
 End DB Connection */

session_set_cookie_params(3600,"/");
session_start();
$smarty = new Smarty; /* Instance of Template Engine Class */
$stringv = new stringv(); /* Instance of stringv class. See classes/stringv.php */
$mobile = new Mobile_Detect(); /* Instance for detecting mobile environment */

$seo_urls = 'Enable'; /* Enable SEO Friendly URLs */
$smarty->assign('seo_urls', $seo_urls);

$environment = ($mobile->isMobile() ? ($mobile->isTablet() ? 'tablet' : 'mobile') : 'desktop/laptop');
$_SESSION['environment'] = $environment;

$smarty->addTemplateDir(WEBSITE);
$smarty->setTemplateDir(WEBSITE);

$data['home_uri'] = 'http://'.$_SERVER['HTTP_HOST'];
$data['page_js'] = '';
$data['header_js'] = '';
$data['copyright_date'] = date('Y');

$select_js = '<script type="text/javascript" src="'.$data['home_uri'].'/media/select/jquery.selectBox.js"></script>
<link type="text/css" rel="stylesheet" href="'.$data['home_uri'].'/css/jquery.selectBox.css"/>';

$slider_img ='<link rel="stylesheet" href="'.$data['home_uri'].'/media/slider/flexslider.css" type="text/css" media="screen" />
<script defer src="'.$data['home_uri'].'/media/slider/jquery.flexslider.js"></script>';

$slider_form ='<script src="'.$data['home_uri'].'/media/sliderange/jquery.nouislider.js"></script>
<link href="'.$data['home_uri'].'/media/sliderange/jquery.nouislider.css" rel="stylesheet">
<script defer src="'.$data['home_uri'].'/media/sliderange/slide.js"></script>';

$dropdown_location = '<script src="'.$data['home_uri'].'/js/jquery.chosen.js"></script>
<link href="'.$data['home_uri'].'/css/the_chosen_one.css" rel="stylesheet">';

$map_js ='<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3"></script>';
$form_js ='<script defer src="'.$data['home_uri'].'/page/js/form.js"></script>';

$galleria_js = '<script type="text/javascript" src="/js/galleria-1.2.7.min.js"></script>';

/* end of file settings.php */
