<?php



// DB config for "webkot"
define('DB_LOGIN','webkot4.1');
define('DB_PASS','coubertin');
define('DB_HOST','localhost');
define('DB_NAME','webkot4.1-dev');

// Environement
define('ENV_TEST', true);
define('ENV_LOCAL', true);

// Date constant : a academic year start on the 15 July
define('BEGINYEAR_MONTH', '07');
define('BEGINYEAR_DAY','15');

// Constant for the Captcha
define('CAPTCHA_PUBLIC_KEY','6LfMM9kSAAAAAOPzslrU6R4II02TU3L3SnHwLX_s');
define('CAPTCHA_PRIVATE_KEY','6LfMM9kSAAAAAJVGJfV5Thxt9sKUzSO0x0fycNuQ');

define('URL', 'http://localhost/Web Developpement/Workspace/webkot4/');

define('SENDMAIL_ACTIVE', false);

define('LOGGING_FILE',false);
define('LOGGING_LIVE',false);
// Indicate the PHP constant for the error log 0, E_ALL, E_ERROR || E_PARSE, ... (without quote)
define('DEBUG_MODE',E_ALL);
// Timezone
define('TIMEZONE','Europe/Paris');

define('NBR_DEFAULT',10);

define('RSS_FILE','rss.xml');

define('CHMOD', 0777);


// Active APC with the first constant. If active, you have to define a prefix to avoid collision with other website on the same server
define('APC_ACTIVE', true);
define('APC_PREFIX', 'w4_');

// The Facebook variables of the Webkot4 application
define('FACEBOOK_APPID','516806348339496');
define('FACEBOOK_SECRET','1163e8dcd6af2e9ed3632b5202bb6d2f');
