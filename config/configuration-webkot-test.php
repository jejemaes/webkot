<?php



// DB config for "webkot"
define('DB_LOGIN','webkot4test');
define('DB_PASS','yra8uhuqe');
define('DB_HOST','localhost');
define('DB_NAME','zadmin_webkot4-test');

// Date constant : a academic year start on the 15 July
define('BEGINYEAR_MONTH', '07');
define('BEGINYEAR_DAY','15');

// Constant for the Captcha
define('CAPTCHA_PUBLIC_KEY','6LeAJeoSAAAAAPudDwVdxlzguyNg4--zSa2t-0WB');//'6LfMM9kSAAAAAOPzslrU6R4II02TU3L3SnHwLX_s');
define('CAPTCHA_PRIVATE_KEY','6LeAJeoSAAAAANWB34Imfz56AfmhkV_bOs6BOXp-');//'6LfMM9kSAAAAAJVGJfV5Thxt9sKUzSO0x0fycNuQ');

define('URL', 'http://dev.webkot.be/webkot4/');

define('SENDMAIL_ACTIVE', false);

define('LOGGING_FILE',false);
define('LOGGING_LIVE',false);

define('NBR_DEFAULT',10);

define('RSS_FILE','rss.xml');

define('CHMOD', 777);


// Active APC with the first constant. If active, you have to define a prefix to avoid collision with other website on the same server
define('APC_ACTIVE', true);
define('APC_PREFIX', 'w4test_');

// The Facebook variables of the Webkot4 application
define('FACEBOOK_APPID','516806348339496');
define('FACEBOOK_SECRET','1163e8dcd6af2e9ed3632b5202bb6d2f');