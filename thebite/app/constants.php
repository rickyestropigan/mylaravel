<?php
ob_start();
$adminuser = DB::table('site_settings')->first();

/* * * define constants ** */
define('SITE_TITLE', $adminuser->title);
define('SITE_URL', $adminuser->url);
define('TAG_LINE', $adminuser->tagline);
define('MAIL_FROM', $adminuser->mail_from);



define('TITLE_FOR_PAGES', SITE_TITLE .' | '.TAG_LINE." - ");
if ($_SERVER['SERVER_NAME'] == '192.168.0.45') {
    
    define('HTTP_PATH', "http://" . $_SERVER['SERVER_NAME'] . "/comp201/bitebargain/site/");
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "/comp201/bitebargain/site/");
} else
if ($_SERVER['SERVER_NAME'] == 'demo.imagetowebpage.com') {
    define('HTTP_PATH', "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/bitebargain/");
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "/wordpress/bitebargain/");
} else {
    define('HTTP_PATH', "https://" . $_SERVER['SERVER_NAME'] . "/");
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "");
}

define('SITE_LOGO', $adminuser->logo);
define('SITE_FAVICON', $adminuser->favicon);
define('PAYPAL_EMAIL',$adminuser->paypal_email_address);
define('PAYPAL_URL', $adminuser->paypal_url);
define('CURR', '$');
define('PER', '%');

/* * ******************  users images ************************ */
define('UPLOAD_FULL_PROFILE_IMAGE_PATH', 'uploads/users');
define('DISPLAY_FULL_PROFILE_IMAGE_PATH', 'uploads/users/');
define('TEMP_PATH', 'uploads/temp');
define('UPLOAD_LOGO_IMAGE_PATH', 'uploads/logo/');
define('DISPLAY_LOGO_IMAGE_PATH', 'uploads/logo/');

define('UPLOAD_FULL_ITEM_IMAGE_PATH', 'uploads/item');
define('DISPLAY_FULL_ITEM_IMAGE_PATH', 'uploads/item/');

define('CAPTCHA_KEY', '6LfFdgkUAAAAAAuyZPrYswKbxxOBLVy_841PSSKj');
//define('API_KEY', 'AIzaSyBMUm-KXWAU0iQoirUzhdLQxszuH0s8eNE');//demo
define('API_KEY', 'AIzaSyDPQzrrGMa3trxfphZKquvTbgoh9Fcp-1E');//live


global $month_Array;

$month_Array = Array
    (
    "monday" => "Monday",
    "tuesday" => "Tuesday",
    "wednesday" => "Wednesday",
    "thursday" => "Thursday",
    "friday" => "Friday",
    "saturday" => "Saturday",
    "sunday" => "Sunday",

);

global $rest_Type;
$rest_Type = Array
    (
    "breakfast" => "Breakfast",
    "cafe" => "Cafe",
    "trendy" => "Trendy",
    "pub" => "Pub"

);

global $payment_option;
$payment_option = Array
    (
    "Cash" => "Cash",
    "Credit Card" => "Credit Card",
    "PayPal" => "PayPal"
);


global $adminStatus;
$adminStatus = array(
    'Confirm' => 'Confirm',
//    'Active' => 'Active',
//    'Scheduled' => 'Scheduled',
    'Prepared' => 'Prepared',
//    'Assign To Delivery' => 'Assign To Delivery',
    'On Delivery' => 'On Delivery',
    'Delivered' => 'Delivered',
    
);


