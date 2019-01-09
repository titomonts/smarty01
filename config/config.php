<?php
/*
============================================================
Site Configuration File
Monch 2007,2008,2009,2010,2011,2012,2013,2014,2015,2016,2017
2017 - Merged with settings/settings.php
============================================================
*/

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
error_reporting(E_ERROR);

setlocale(LC_MONETARY, 'en_US');
session_set_cookie_params(3600,"/");
session_start();

//define('CONF_ABSOLUTE_PATH', str_replace('\\', '/', realpath(dirname('_FILE_').'/../').'/'));
define('CONF_ABSOLUTE_PATH', getcwd());
define('MAIN_FOLDER', CONF_ABSOLUTE_PATH.'/');
define('SMARTY_PATH', MAIN_FOLDER.'smarty/libs/');
define('SMARTY_PLUGINS', MAIN_FOLDER.'smarty/plugins/');
define('CLASSES_PATH', MAIN_FOLDER.'classes/');
define('VILLA_XML_PATH', MAIN_FOLDER.'villa-xml/');
define('SITEMAP_PATH', MAIN_FOLDER.'sitemaps/');
define('ERROR_TEMPLATE', MAIN_FOLDER.'templates/404/');

/* Include required classes */
require CLASSES_PATH.'stringv.php';
require CLASSES_PATH.'mobile.php';
require CLASSES_PATH.'villaClasses.php';
require CLASSES_PATH.'formClass.php';
require CLASSES_PATH.'ratesClass.php';
require SMARTY_PATH.'Smarty.class.php';

$smarty = new Smarty; /* Instance of Template Engine Class */
$stringv = new stringv(); /* Instance of stringv class. See classes/stringv.php */
$mobile = new Mobile_Detect(); /* Instance for detecting mobile environment */
$website = new villa(); /* Instance of villa class */
$forms = new forms(); /* Instance of form class */
$rates = new villaRates(); /* Instance of rates class */
$_SESSION['ondevice'] = FALSE;

/* Check if the domain is already added to the database */
global $mysqli;
$mysqli = $website->connect();
$data['site'] = $_SERVER['HTTP_HOST'];
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$data['home_uri'] = $protocol.$data['site'];
$data['theme'] = '';
$data['environment'] = 'www';

$chk = $mysqli->query("CALL check_domain('".$_SERVER['HTTP_HOST']."')") or die($mysqli->error);
$row = $chk->fetch_assoc();
$mysqli->close();
if( $row['num_domain'] >=1 ):
	$data['site_id'] = $row['site_id'];
	$data['complex'] = $row['site_is_complex'];
	/* Get Complex Calendars */
	if( $data['complex'] == 'yes' ):
		$mysqli = $website->connect();
		$cqry = $mysqli->query("SELECT * FROM smarty_calendar_iframes WHERE site_id = ".(int)$data['site_id']."") or die($mysqli->error.' Calendar Iframe Error');
		$cc = 0;
		while($crow = $cqry->fetch_assoc()):
			$data['complex_calendars'][$cc]['site_id'] = $crow['site_id'];
			$data['complex_calendars'][$cc]['site_villa_id'] = $crow['site_villa_id'];
			$data['complex_calendars'][$cc]['site_villa_name'] = $crow['site_villa_name'];
			$data['complex_calendars'][$cc]['site_iframe_calendar'] = base64_decode($crow['site_iframe_calendar']);
			$data['complex_calendars'][$cc]['site_estate_id'] = $crow['site_estate_id'];
			$cc++;
		endwhile;
		$mysqli->close();
	endif;
	/* End get complex calendars */

	 /* Check google grouping */
        $gc_json = file_get_contents( 'villa-xml/captcha.json' ) or die('JSON google captcha file failed to load');
        $gc = json_decode(trim($gc_json), TRUE);
        $sgc = count($gc);
        $site = str_replace($data['environment'].'.', '', $data['site']);
        for( $t = 0; $t<$sgc; $t++ ):
                if( $site >= $gc[$t]['range_start'] && $site <= $gc[$t]['range_end']):
                        $data['google_site_key'] = $gc[$t]['site_key'];
                        $data['google_secret_key'] = $gc[$t]['secret_key'];
                        break;
                endif;
        endfor;
        /* End check google grouping */

	$data['villa_id'] = $row['site_villa_id'];

	/* GTM and GA Data */
	$wsVillaData = $website->ws_villa_info($data['villa_id']);
	list($data['location'],$data['sublocation']) = explode(',',str_replace(" ","",$wsVillaData['gaData']['dataPageTrack']['ehgLocation']));
	$data['bedrooms'] = $wsVillaData['gaData']['dataPageTrack']['ehgBedrooms'];
	$data['ehgLocation'] = $wsVillaData['gaData']['dataPageTrack']['ehgLocation'];
	$data['ehgPageType'] = $wsVillaData['gaData']['dataPageTrack']['ehgPageType'];
	$data['ehgBookingSource'] = $wsVillaData['gaData']['dataPageTrack']['ehgBookingSource'];
	$data['hrental_id'] = $wsVillaData['gaData']['dataPageTrack']['hrental_id'];
	/* End GTM and GA Data */

	/* Promo Villas */
	$data['estate_id'] = $row['site_estate_id'];
	$data['estate_villa_ids'] = str_replace(' ','',$row['site_estate_vids']);
	if( $data['estate_villa_ids'] != '' ):
		$pvids = explode(',',$data['estate_villa_ids']);
		$data['promovillas'] = array();
		$spvids = count($pvids);
		for( $r=0; $r<$spvids; $r++ ):
			$pdata = $website->ws_villa_info($pvids[$r]);
			$data['promovillas'][$pvids[$r]] = $pdata['gaData']['property']['name'];
		endfor;
	endif;
	/* End Promo Villas */

	$data['cdn'] = $protocol.$row['site_cdn_domain_name'];
	$data['api_db'] = $row['site_api_db'];
	$data['site_title'] = $row['site_main_title'];
	$data['google_analytics'] = $row['site_google_analytics'];
	$data['theme'] = $row['site_theme'];
	$data['responsive'] = $row['site_is_responsive'];
	$data['site_configuration'] = $row['site_configuration'];
	$data['site_structure'] = $row['site_menu_structure'];
	$data['gallery_image_per_page'] = (int)$row['site_image_per_page'];
	$data['site_seo_description'] = $row['site_seo_description'];
	$data['site_seo_og_locale'] = $row['site_seo_og_locale'];
	$data['site_seo_og_type'] = $row['site_seo_og_type'];
	$data['site_seo_og_title'] = $row['site_seo_og_title'];
	$data['site_seo_og_description'] = $row['site_seo_og_description'];
	$data['site_seo_og_url'] = $row['site_seo_og_url'];
	$data['site_seo_og_site_name'] = $row['site_seo_og_site_name'];
	$data['site_seo_article_publisher'] = $row['site_seo_article_publisher'];
	$data['site_seo_og_image'] = $row['site_seo_og_image'];
	$data['site_seo_twitter_card'] = $row['site_seo_twitter_card'];
	$data['site_seo_twitter_description'] = $row['site_seo_twitter_description'];
	$data['site_seo_twitter_title'] = $row['site_seo_twitter_title'];
	$data['site_seo_twitter_site'] = $row['site_seo_twitter_site'];
	$data['site_seo_twitter_image'] = $row['site_seo_twitter_image'];
	$data['site_seo_twitter_creator'] = $row['site_seo_twitter_creator'];
	$data['site_email_template'] = $row['site_email_template'];
	$data['site_calendar_iframe'] = base64_decode($row['site_calendar_iframe']);
	$data['site_tag'] = $row['site_tag'];
else:
	die('This domain is not yet configured. Please log in to admin and configure this domain.');
endif;
/* End check domain */

/* ADDER BY: ARDIAN
   FOR BOOKING ENGINE SUPPORT
*/
$smarty->configLoad('templates/' . $data['theme'] . '/' . $data['theme'] . '.conf');
$booking_engine = $smarty->getConfigVars('BOOKING_ENGINE');
$booking_urls = $smarty->getConfigVars('BOOKING_ENGINE_URL');

if ($booking_engine) {
    $params['booking'] = $booking_engine;
    $params['urls'] = $booking_urls;
    $params['page'] = substr(str_replace('.html', '', $_SERVER['REQUEST_URI']), 1);
    $params['return'] = true;
    if (isset($_POST)) {
        $params['ci'] = isset($_POST['txtPreArriveDate']) ? $_POST['txtPreArriveDate'] : null;
        $params['co'] = isset($_POST['txtPreDepartDate']) ? $_POST['txtPreDepartDate'] : null;
    }    
    include_once(SMARTY_PATH . 'plugins/function.booking_menu.php');
    $redirect = smarty_function_booking_menu($params);
    if ($redirect) {
        header("Location:".trim($redirect), true, 302);
        /*exit;*/
    }
}
/* END ADDED */

define('ASSET_COMPILE_OUTPUT_DIR', CONF_ABSOLUTE_PATH.'/cache');
define('ASSET_COMPILE_URL_ROOT', $data['cdn'].'/cache');

/* Check villa folder if a configuration file exists */
if( !is_dir( VILLA_XML_PATH.$data['theme'].'/' ) ): /* No configuration file for website */
	$wsVillaData = $website->ws_villa_info($data['villa_id']);
	list($loc,$sloc) = explode(',',$wsVillaData['gaData']['dataPageTrack']['ehgLocation']);
	mkdir(VILLA_XML_PATH.$data['theme'].'/', 0777);
	chmod(VILLA_XML_PATH.$data['theme'].'/', 0777);
	$villa_config_file = ($data['theme'] == 'ehteaser' || $data['theme'] == 'ehteaser_v1')?VILLA_XML_PATH.$data['theme'].'/config-'.$data['villa_id'].'.xml':VILLA_XML_PATH.$data['theme'].'/config.xml';
	if( !file_exists($villa_config_file) ):
	$fh = fopen($villa_config_file, 'wb') or die("can't open file");
        $confString = '<?xml version="1.0" encoding="utf-8"?>
		<Configuration>
		<Source>
		<villa_id>'.$data['villa_id'].'</villa_id>
		<estate_id '.(isset($data['estate_villa_ids'])?'vids="'.$data['estate_villa_ids'].'"':'').'>'.$data['estate_id'].'</estate_id>
		<villa_name>'.htmlentities($wsVillaData['gaData']['property']['name']).'</villa_name>
		<google_analytics>'.$data['google_analytics'].'</google_analytics>
		<location>'.$loc.'</location>
		<sublocation>'.$sloc.'</sublocation>
		<bedrooms>'.$wsVillaData['gaData']['dataPageTrack']['ehgBedrooms'].'</bedrooms>
		<hrental_id>'.$wsVillaData['gaData']['dataPageTrack']['hrental_id'].'</hrental_id>
		<ehgLocation>'.$wsVillaData['gaData']['dataPageTrack']['ehgLocation'].'</ehgLocation>
		<ehgBookingSource>'.$wsVillaData['gaData']['dataPageTrack']['ehgBookingSource'].'</ehgBookingSource>
		<ehgPageType>'.$wsVillaData['gaData']['dataPageTrack']['ehgPageType'].'</ehgPageType>
		</Source>
		</Configuration>';
        fwrite($fh, $confString);
        fclose($fh);
	endif;
	$data['villa_name'] = $wsVillaData['gaData']['property']['name'];
	$data['google_analytics'] = $data['google_analytics'];
	list($data['location'],$data['sublocation']) = explode(',',str_replace(" ","",$wsVillaData['gaData']['dataPageTrack']['ehgLocation']));
	$data['bedrooms'] = $wsVillaData['gaData']['dataPageTrack']['ehgBedrooms'];
	$data['ehgLocation'] = $wsVillaData['gaData']['dataPageTrack']['ehgLocation'];
	$data['ehgPageType'] = $wsVillaData['gaData']['dataPageTrack']['ehgPageType'];
	$data['ehgBookingSource'] = $wsVillaData['gaData']['dataPageTrack']['ehgBookingSource'];
	$data['hrental_id'] = $wsVillaData['gaData']['dataPageTrack']['hrental_id'];	
else: /* A configuration file is already present */
	$villa_config_file = ($data['theme'] == 'ehteaser' || $data['theme'] == 'ehteaser_v1')?VILLA_XML_PATH.$data['theme'].'/config-'.$data['villa_id'].'.xml':VILLA_XML_PATH.$data['theme'].'/config.xml';
	$wsVillaData = $website->ws_villa_info($data['villa_id']);
	list($loc,$sloc) = explode(',',$wsVillaData['gaData']['dataPageTrack']['ehgLocation']);
	if( !file_exists($villa_config_file) ):
		$fh = fopen($villa_config_file, 'wb') or die("can't open file");
        $confString = '<?xml version="1.0" encoding="utf-8"?>
		<Configuration>
		<Source>
		<villa_id>'.$data['villa_id'].'</villa_id>
		<estate_id '.(isset($data['estate_villa_ids'])?'vids="'.$data['estate_villa_ids'].'"':'').'>'.$data['estate_id'].'</estate_id>
		<villa_name>'.htmlentities($wsVillaData['gaData']['property']['name']).'</villa_name>
		<google_analytics>'.$data['google_analytics'].'</google_analytics>
		<location>'.$loc.'</location>
		<sublocation>'.$sloc.'</sublocation>
		<bedrooms>'.$wsVillaData['gaData']['dataPageTrack']['ehgBedrooms'].'</bedrooms>
		<hrental_id>'.$wsVillaData['gaData']['dataPageTrack']['hrental_id'].'</hrental_id>
		<ehgLocation>'.$wsVillaData['gaData']['dataPageTrack']['ehgLocation'].'</ehgLocation>
		<ehgBookingSource>'.$wsVillaData['gaData']['dataPageTrack']['ehgBookingSource'].'</ehgBookingSource>
		<ehgPageType>'.$wsVillaData['gaData']['dataPageTrack']['ehgPageType'].'</ehgPageType>
		</Source>
		</Configuration>';
        fwrite($fh, $confString);
        fclose($fh);
	endif;
	$conf = new DOMDocument();
	$conf->load($villa_config_file);	
	$vconf = $conf->getElementsByTagName('Source');
	foreach( $vconf as $vc ):
		$data['villa_name'] = $vc->getElementsByTagName( 'villa_name' )->item(0)->nodeValue;
		$data['location'] = $vc->getElementsByTagName( 'location' )->item(0)->nodeValue;
		$data['sublocation'] = $vc->getElementsByTagName( 'sublocation' )->item(0)->nodeValue;
		$data['bedrooms'] = $vc->getElementsByTagName( 'bedrooms' )->item(0)->nodeValue;
		$data['hrental_id'] = $vc->getElementsByTagName( 'hrental_id' )->item(0)->nodeValue;
		$data['ehgLocation'] = $vc->getElementsByTagName( 'ehgLocation' )->item(0)->nodeValue;
		$data['ehgBookingSource'] = $vc->getElementsByTagName( 'ehgBookingSource' )->item(0)->nodeValue;
		$data['ehgPageType'] = $vc->getElementsByTagName( 'ehgPageType' )->item(0)->nodeValue;
	endforeach;
endif;
/* End check configuration file */

/* Load menu/page structure file */
if( $data['theme'] == 'ehteaser' || $data['theme'] == 'ehteaser_v1'):
        $json = file_get_contents( 'villa-xml/ehteaser/ehteaser.json' ) or die('JSON page structure file failed to load');
else:
        $json = file_get_contents( 'villa-xml/'.$data['theme'].'/'.$data['site_structure'] ) or die('JSON page structure file failed to load');
endif;
$page_structure = json_decode(trim($json), true); /* This will generate the menu */

/* Check if sitemap folder exists */
if( !is_dir(SITEMAP_PATH.$data['site'].'/') ):
	mkdir(SITEMAP_PATH.$data['site'].'/',0777);
endif;
$sitemap_file = SITEMAP_PATH.$data['site'].'/sitemap.xml';
set_time_limit(3600);
if( !file_exists($sitemap_file) ):
	$sf = fopen($sitemap_file, 'wb') or die('Failed to open/write sitemap.xml');
	$sp = sizeof($page_structure);
	$xstring = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9  http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<url>
    <loc>http://' . $data['site'] . '/</loc>
    <changefreq>weekly</changefreq>
    <priority>1.00</priority>
</url>';

	$p_structure = [];
	
	for ($i = 0; $i < $sp; $i++):
		if ($page_structure[$i]['menu_location'] == 'main' || $page_structure[$i]['menu_location'] == 'footer' || (isset($page_structure[$i]['on_sitemap']) && $page_structure[$i]['on_sitemap'] == 'yes')):
			if ($page_structure[$i]['page_identifier'] != 'index' && ((isset($page_structure[$i]['level']) && $page_structure[$i]['level'] != 'linkout') || !isset($page_structure[$i]['level']))):
				if (isset($page_structure[$i]['parent']) && $page_structure[$i]['parent'] != $page_structure[$i]['page_identifier']):
					
					if (isset($page_structure[$i]['what']) && $page_structure[$i]['what'] != $page_structure[$i]['page_identifier']):  
						
						if (!in_array($page_structure[$i]['parent'] . '/' . $page_structure[$i]['what'] . '/' . $page_structure[$i]['page_identifier'] . '.html', $p_structure, true)):
							array_push($p_structure, $page_structure[$i]['parent'] . '/' . $page_structure[$i]['what'] . '/' . $page_structure[$i]['page_identifier'] . '.html');
						endif;
	
					else:
						
						if (!in_array($page_structure[$i]['parent'] . '/' . $page_structure[$i]['page_identifier'] . '.html', $p_structure, true)):
							array_push($p_structure, $page_structure[$i]['parent'] . '/' . $page_structure[$i]['page_identifier'] . '.html');
						endif;
	
					endif;
	
				else:
						
					if (!in_array($page_structure[$i]['page_identifier'] . '.html', $p_structure, true)):                
						array_push($p_structure, $page_structure[$i]['page_identifier'] . '.html');
					endif;
	
				endif;
	
				if( array_key_exists('children',$page_structure[$i]) ):
					$sc = sizeof($page_structure[$i]['children']);
					for( $j = 0; $j < $sc; $j++ ):
						if( ((isset($page_structure[$i]['children'][$j]['level']) && $page_structure[$i]['children'][$j]['level'] != 'linkout') || !isset($page_structure[$i]['children'][$j]['level'])) ):
	
							if (isset($page_structure[$i]['children'][$j]['parent']) && $page_structure[$i]['children'][$j]['parent'] != $page_structure[$i]['children'][$j]['page_identifier']):
								
								if (isset($page_structure[$i]['parent'])):
						
									if (!in_array($page_structure[$i]['parent'] . '/' . $page_structure[$i]['children'][$j]['parent'] . '/' . $page_structure[$i]['children'][$j]['page_identifier'] . '.html', $p_structure, true)):
										array_push($p_structure, $page_structure[$i]['parent'] . '/' . $page_structure[$i]['children'][$j]['parent'] . '/' . $page_structure[$i]['children'][$j]['page_identifier'] . '.html');
									endif;
									
								else:
						
									if (!in_array($page_structure[$i]['children'][$j]['parent'] . '/' . $page_structure[$i]['children'][$j]['page_identifier'] . '.html', $p_structure, true)):  
										array_push($p_structure, $page_structure[$i]['children'][$j]['parent'] . '/' . $page_structure[$i]['children'][$j]['page_identifier'] . '.html');
									endif;
									
								endif;
	
							elseif (!isset($page_structure[$i]['children'][$j]['parent']) || (isset($page_structure[$i]['children'][$j]['parent']) && $page_structure[$i]['page_identifier'] != $page_structure[$i]['children'][$j]['page_identifier'])):
						
								if (!in_array($page_structure[$i]['children'][$j]['page_identifier'] . '.html', $p_structure, true)):
									array_push($p_structure, $page_structure[$i]['children'][$j]['page_identifier'] . '.html');
								endif;
	
							endif;
	
						endif;        
					endfor;        
				endif;        
			endif;        
		endif;
	endfor;
	
	for ($x = 0; $x < sizeof($p_structure); $x++):
		$xstring .= '<url>
			<loc>http://' . $data['site'] . '/' . $p_structure[$x] . '</loc>
			<changefreq>weekly</changefreq>
			<priority>0.9</priority>
		</url>'; 
	endfor;
	
	$xstring .= '</urlset>';
	fwrite($sf, $xstring);
    fclose($sf);
endif;
/* End check if sitemap folder exists */

$mysqli = $website->connect();
$gqry = $mysqli->query("CALL get_gallery('".$data['villa_id']."')") or die($mysqli->error);
$gallery = array();
$g = 0;
while( $row = $gqry->fetch_assoc() ):
	$gallery[$g]['path'] = $data['complex']=='no'?'/gallery/'.$data['villa_id'].'/':'/gallery/'.$data['site_id'].'/'.$data['villa_id'].'/';
	$gallery[$g]['title'] = $row['gal_description'];
	$gallery[$g]['image_src'] = $row['gal_filename'];
	$gallery[$g]['thumbnail_src'] = $row['gal_thumbnail'];
	$gallery[$g]['alt'] = $row['gal_description'];
	$gallery[$g]['villaID'] = $row['gal_vid'];
	$gallery[$g]['gal_thumb_height'] = $row['gal_thumb_height'];
	$gallery[$g]['gal_thumb_width'] = $row['gal_thumb_width'];
	$g++;
endwhile;
$mysqli->close();		
$pgallery = $data['gallery_image_per_page']>0?array_chunk($gallery,$data['gallery_image_per_page']):array_chunk($gallery,sizeof($gallery));

$smarty->assign('pages',$page_structure);
/* End load menu/page structure file and gallery images */

/* Check if xml content already exists */
$data['cacheName'] = VILLA_XML_PATH.$data['theme'].'/'.$data['villa_id'].'.xml.cache';
$ageInSeconds = (3600 * 24) * 7; /* 7 days cache life */
if (!file_exists($data['cacheName']) || filemtime($data['cacheName']) > time() + $ageInSeconds):
	$contents = file_get_contents('http://ws.marketingvillas.com/vsapi.asmx/getContent?p_villaid='.$data['villa_id']);
	file_put_contents($data['cacheName'], $contents);
	if( $data['complex'] == 'yes' ):
		$vids = explode(',',$data['estate_villa_ids']);
		for( $vs=0; $vs<sizeof($vids); $vs++ ):
			$cacheName = VILLA_XML_PATH.$data['theme'].'/'.$vids[$vs].'.xml.cache';
			if (!file_exists($cacheName) || filemtime($cacheName) > time() + $ageInSeconds):
				$contents = file_get_contents('http://ws.marketingvillas.com/vsapi.asmx/getContent?p_villaid='.$vids[$vs]);
				file_put_contents($cacheName, $contents);
			endif;
		endfor;
	endif;
endif;

/* Check if site is teaser site, then get teaser data */
if( $data['theme'] == 'ehteaser' || $data['theme'] == 'ehteaser_v1'):
        $data['teaser_data'] = $data['complex'] == 'yes'?$website->teaser_data($data['estate_id']):$website->teaser_data($data['villa_id']);
        if( $data['complex'] == 'yes' ):
                $vids = explode(',', $data['estate_villa_ids']);
                $svids = sizeof($vids);
                $vb = 0;
                for( $v=0; $v<$svids; $v++ ):
                        $tdata = $website->teaser_data($vids[$v]);
                        $data['teaser_complex_data'][$vb]['villa_id'] = $vids[$v];
                        $data['teaser_complex_data'][$vb]['villa_name'] = $tdata['Villa']['Info']['Name'];
                        $vb++;
                endfor;
        endif;
endif;
/* End check if teaser site */


/* Generate individual rates cache for each villa id */
if( $data['complex'] == 'yes' ):
	$vids = explode(',',$data['estate_villa_ids']);
	for( $vs=0; $vs<sizeof($vids); $vs++ ):
		$arg['id'] = $vids[$vs];
		$arg['villa_theme'] = $data['theme'];
		$arg['vid'] = $vids[$vs];
		$rates->get_villa_rates($arg);
	endfor;
endif;
/* End generate individual rates */
/* End check xml content */

$data['copyright_date'] = date('Y');
$data['environment'] = ($mobile->isMobile() ? ($mobile->isTablet() ? 'tablet' : 'mobile') : 'desktop/laptop');
$subTemplate = ($data['environment'] == 'tablet' || $data['environment'] == 'mobile') ? ($data['responsive'] == 'no'?'device/':''):'';

define('WEBSITE', MAIN_FOLDER.'templates/'.$data['theme'].'/'.$subTemplate); /* Define the template to be used */
$smarty->addTemplateDir(WEBSITE); /* Register the theme folder as a template */
$smarty->setTemplateDir(WEBSITE); /* Use the registered theme folder as default template */

$seo_urls = 'Enable'; /* Enable SEO Friendly URLs */
$smarty->assign('seo_urls', $seo_urls);

/* Universal Configuration */
$mysqli = $website->connect();
$sqry = $mysqli->query( "SELECT * FROM universal_settings" ) or die($mysqli->error);

