<?php



// DB config for "webkot"
define('DB_LOGIN','webkot4');
define('DB_PASS','Webkot1314FTW');
define('DB_HOST','localhost');
define('DB_NAME','zadmin_webkot4');

// Date constant : a academic year start on the 15 July
define('BEGINYEAR_MONTH', '07');
define('BEGINYEAR_DAY','15');

// Constant for the Captcha
define('CAPTCHA_PUBLIC_KEY','6LeAJeoSAAAAAPudDwVdxlzguyNg4--zSa2t-0WB');
define('CAPTCHA_PRIVATE_KEY','6LeAJeoSAAAAANWB34Imfz56AfmhkV_bOs6BOXp-');

define('URL', 'http://www.webkot.be/');

define('SENDMAIL_ACTIVE', true);

define('LOGGING_FILE',false);
define('LOGGING_LIVE',false);

define('NBR_DEFAULT',10);

define('RSS_FILE','rss.xml');

define('CHMOD', 0777);


// Active APC with the first constant. If active, you have to define a prefix to avoid collision with other website on the same server
define('APC_ACTIVE', true);
define('APC_PREFIX', 'w4_');

// The Facebook variables of the Webkot4 application
define('FACEBOOK_APPID','516806348339496');
define('FACEBOOK_SECRET','1163e8dcd6af2e9ed3632b5202bb6d2f');