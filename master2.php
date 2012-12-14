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

/**
 * Let's work on some of the settings that will make this work for you
 */

/**
 * These links are "relative" to the directory this file is in.
 * Standard setup means: http://example.com/AS/master.php
 * 
 * so 'booked.html' actually points to 'http://example.com/AS/booked.html'
 * 
 * if you need 'http://example.com/my-folder/my-page.html'
 * 		you will use '../my-folder/mypage.html'
 * 		'../' takes you backwards one directory
 */
$domain = array(
			1	=>	'booked.html',
			2	=>	'contact_form.html',
			3	=>	'contract.html',
			4	=>	'follow_up.html',
			5	=>	'invoice.html',
			6	=>	'mission-accomplished.html',
			7	=>	'quote.html',
			8	=>	'receipt.html',
			9	=>	'reminder.html',
			10	=>	'thank_you.html',
			11	=>	'../index.php'		// last item in the list SHOULD NOT have a comma
		);
$mergecode = array(
	//		'this-is in the url'	=>	'this is the merge code in the html';
			'Name'		=>	'%name%',			// name should be clients full name. This PHP will create %firstname% and %lastname% for you.
			'Email1'	=>	'%email%',
			'Company'	=>	'%company%',
			'Homephone'	=>	'%homephone%',
			'Workphone'	=>	'%workphone%',
			'Address1'	=>	'%address1%',
			'Address2'	=>	'%address2%',
			'City'		=>	'%city%',
			'State'		=>	'%state%',
			'Zip'		=>	'%zip%',
			'Country'	=>	'%country%',
			'Fax'		=>	'%fax%',
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
			'field11'	=>	'%field11%',
			'field12'	=>	'%field12%',
			'field13'	=>	'%field13%',
			'field14'	=>	'%field14%',
			'field15'	=>	'%field15%',
			'field16'	=>	'%field16%',
			'field17'	=>	'%field17%',
			'field18'	=>	'%field18%',
			'field19'	=>	'%field19%',
			'field20'	=>	'%field20%',
			'field21'	=>	'%field21%',
			'field22'	=>	'%field22%',
			'field23'	=>	'%field23%',
			'field24'	=>	'%field24%',
			'field25'	=>	'%field25%'		// last item in the list SHOULD NOT have a comma
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
define('DATE_FORMAT', 'm/d/Y'); // if you want a different date format, change this. see [http://us.php.net/manual/en/function.date.php] for more details


// ** That's all the settings we need. ** //

/**
 * Don't edit below here unless you know what you're doing.
 *
 * Feel free to read and learn though
 */

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
	echo 'File ' . $l . ' is not set.';			// show an error if there is no matching domain for 'l'
	die();
};

if ( !file_exists( $l ) ) {
	echo "That file doesn't exist. <a href='$l'>Check it out</a> for yourself.";
	die();
}


$showpage = file_get_contents($l);

if ( false === $showpage ) {
	echo "Unable to retrieve file contents.";
	die();
}


function merge_in_today($contents) {
	$today = date( DATE_FORMAT );
	$contents = str_replace('%today%', $today, $contents);
	preg_match_all('/%today\+([0-9]+)%/', $contents, $matches, PREG_SET_ORDER);
	foreach ($matches as $val) {
		$nextdate  = date( DATE_FORMAT, mktime(0, 0, 0, date("m")  , date("d")+ $val[1], date("Y")));
		$contents=str_replace($val[0], $nextdate, $contents);
	};
	return $contents;
}

if ( SET_TODAY === true ) { // will substitute %today% and %today+x% into template. These are dynamic dates.
	$showpage = merge_in_today($showpage);
};

if ( SPLIT_NAME === true ) {	// splitting the 'name' into first and last based on first [space] character in full name
	if ( isset ( $_REQUEST['Name'] ) ) {
		$splitname = explode(" ", $_REQUEST['Name'], 2);
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
	if ( isset($_REQUEST[$k]) ) {
		$showpage=str_replace($v, $_REQUEST[$k], $showpage);	// use "htmlspecialchars()" to add a layer of security to your site. down with evildoers
	};
};


echo $showpage;	//show the final content
exit(); // shut 'er down

/* EOF */
