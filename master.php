<?php
/**
 * Master PHP file
 *
 * The settings section starts at line # 21
 *
 */


// ** Is your server's PHP good enough? ** //
 if (version_compare(PHP_VERSION, '4.3') < 0) {
    echo 'You need at least PHP version 4.3 on your server to use this file. You have: ' . PHP_VERSION . "\n";
	echo 'Contact your web hosting company to see about upgrading your PHP.';
	die();	// If not, we stop here
}
/* Your server's PHP is good, lets continue on... */
// Define a referance to this file
define('MASTERBASEDIR', dirname(__FILE__) . '/');

/**
 * Let's work on some of the settings that will make this work for you
 */

$domain = array(
			1	=>	'http://example.com/',	// put your primary website domain here between the ' marks. Keep the / at the end
			2	=>	'http://www.guilefulmagic.com/',	// your secondary website, same rules as primary, delete this line if you don't need it
			3	=>	MASTERBASEDIR, // you can try this if you get 'http:// wrapper is disabled in the server configuration by allow_url_fopen=0' errors
			4	=>	'',	// you can keep going as long as you need, the next would be 4 => '',
		);
$mergecode = array(
	//		'this-is in the url'	=>	'this is the merge code in the html';
			'name'		=>	'%name%',			// name should be clients full name. This PHP will create %firstname% and %lastname% for you.
			'email'		=>	'%email%',
			'company'	=>	'%company%',
			'homephone'	=>	'%homephone%',
			'workphone'	=>	'%workphone%',
			'address1'	=>	'%address1%',
			'address2'	=>	'%address2%',
			'city'		=>	'%city%',
			'state'		=>	'%state%',
			'zip'		=>	'%zip%',
			'country'	=>	'%country%',
			'fax'		=>	'%fax%',
			'field1'	=>	'%field1%',
			'field2'	=>	'%field2%',
			'field3'	=>	'%field3%',
			'field4'	=>	'%field4%',
			'field5'	=>	'%field5%',
			'field6'	=>	'%field6%',
			'field7'	=>	'%field7%',
			'field8'	=>	'%field8%',
			'field9'	=>	'%field9%',
			'field10'	=>	'%field10%',
		);
/* if you don't want us to set up %firstname%
 * and %lastname% fields, change this to 'false'
 */
define('SPLIT_NAME', true);

/* if you don't want us to set up %today%
 * and %today+x% fields, change this to 'false'
 * These are dynamic, if you need dates to remain constant, save them in a custom field and load like the others
 * Output format is MM/DD/YYYY
 */
define('SET_TODAY', true);

// ** That's all the settings we need. ** //

/**
 * Don't edit below here unless you know what you're doing.
 *
 * Feel free to read and learn though
 */

// Try to set url fopen value to true. It it was off, it's worth a shot.
ini_set('allow_url_fopen', 1);

if (isset($_GET['l']) and is_numeric($_GET['l'])) {		// 'l' is required make sure it's there
	$l = intval( $_GET['l'] );
	unset($_GET['l']);
} else {
	echo 'l needs to exist and be a number';		// show an error if it isn't present or not a number
	die();
};

$l = intval($l);
if (isset($domain["$l"])) {							// 'l' needs to match a domain in the settings
	$l = $domain["$l"];
} else {
	echo 'Domain ' . $l . ' is not set.';			// show an error if there is no matching domain for 'l'
	die();
};

if (isset($_GET['f'])) {								// 'f' is required, it's the template file and path
	$l = $l . $_GET['f'];
	unset($_GET['f']);
} else {
	echo 'You did not select a file';				// show an error if 'f' is not set
	die();
};

/* Check if cURL is available, use to check if html template exists. Else, hope for the best. */
if (function_exists('curl_init')) {
	function http_response($url, $status = null, $wait = 3) 
	{ 
			$time = microtime(true); 
			$expire = $time + $wait; 
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_HEADER, TRUE); 
			curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
			$head = curl_exec($ch); 
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			curl_close($ch); 
			
			if(!$head) 
			{ 
				return FALSE; 
			} 
			
			if($status === null) 
			{ 
				if($httpCode < 400) 
				{ 
					return TRUE; 
				} 
				else 
				{ 
					return FALSE; 
				} 
			} 
			elseif($status == $httpCode) 
			{ 
				return TRUE; 
			} 
			
			return FALSE; 
		}  
		 
};
if (function_exists('http_response')) {
	if (http_response($l, '200')) {			// if specified template exists on specified, http reaponse will be 200
		$showpage=file_get_contents($l);
	} else {
		echo 'Template file not available: ' . $l;
		die();
	};
} else {
	$showpage=file_get_contents($l); // We don't know if the template is there, but we'll hope for the best.
};


function merge_in_today($contents) {
	$today = date("m/d/Y");			// if you want a different date format, change this. see [http://us.php.net/manual/en/function.date.php] for more details
	$contents = str_replace('%today%', $today, $contents);
	preg_match_all('/%today\+([0-9]+)%/', $contents, $matches, PREG_SET_ORDER);
	foreach ($matches as $val) {
		$nextdate  = date("m/d/Y", mktime(0, 0, 0, date("m")  , date("d")+ $val[1], date("Y")));	// you would also need to change the date format here
		$contents=str_replace($val[0], $nextdate, $contents);
	};
	return $contents;
}

if ( SET_TODAY === true ) { // will substitute %today% and %today+x% into template. These are dynamic dates.
	$showpage = merge_in_today($showpage);
};

if ( SPLIT_NAME === true ) {	// splitting the 'name' into first and last based on first [space] character in full name
	if ( isset ( $_GET['name'] ) ) {
		$splitname = explode(" ", $_GET['name'], 2);
		$showpage=str_replace('%firstname%',$splitname[0],$showpage);
		$showpage=str_replace('%lastname%',$splitname[1],$showpage);
	};
};

if ( isset($_GET['auto']) && ( $_GET['auto'] === 'yes' ) ) {	// &auto=yes at the end of the URL will allow forms to submit themselves
	if ( isset($_GET['fm']) ) {
		$form_name = $_GET['fm'];	// use &fm=something if "form1" in <form name="form1"> is different in your template
	} else {
		$form_name = 'form1';
	};
	$autojs = '<script type="text/javascript">document.getElementById("' . $form_name . '").submit();</script>';
	$bodyReplace = $autojs . '</body>';
	$showpage=str_replace('</body>',$bodyReplace,$showpage); // we put the javascript at the very bottom, just before the closing body tag
};

foreach ( $mergecode as $k => $v ) {	// loop through all the merge codes and switch things out
	if ( isset($_GET[$k]) ) {
		$showpage=str_replace($v,htmlspecialchars($_GET[$k]),$showpage);	// use "htmlspecialchars()" to add a layer of security to your site. down with evildoers
	};
};


echo $showpage;	//show the final content
exit(); // shut 'er down

/* EOF (When the entire file is all php, we obmit the closing ?> tag to prevent potential php errors. "EOF" means "End Of File".) */