<?php
// Define the core paths
// Define them as absolute paths to make sure that require_once work as expected

// DIRECTORY_SEPARATOR is a php pre-defined constant
// (\ for windows, / for Unix)

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// windows
// defined('SITE_ROOT') ? null : define('SITE_ROOT', DS.'xampp'.DS.'htdocs'.DS.'trinity');

// ubuntu
 defined('SITE_ROOT') ? null : define('SITE_ROOT', DS.'var'.DS.'www'.DS.'html'.DS.'stefanos');

// mac
// defined('SITE_ROOT') ? null : define('SITE_ROOT', DS.'Users'.DS.'lepipas'.DS.'Sites'.DS.'stefanos');

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'includes');

defined('WS_PATH') ? null : define('WS_PATH', SITE_ROOT.DS.'vendor');

// load config file first
require_once(LIB_PATH.DS."config.php");

// load basic functions
require_once(LIB_PATH.DS."functions.php");

// load core objects
require_once(LIB_PATH.DS."session.php");
require_once(LIB_PATH.DS."database.php");
require_once(LIB_PATH.DS."geo_processor.php");
require_once(LIB_PATH.DS."database_object.php");


// load database related classes
require_once(LIB_PATH.DS."role.php");
require_once(LIB_PATH.DS."gym.php");
require_once(LIB_PATH.DS."loginuser.php");
require_once(LIB_PATH.DS."navigation.php");
require_once(LIB_PATH.DS."form.php");

require_once(LIB_PATH.DS."customer.php");
require_once(LIB_PATH.DS."provider.php");
require_once(LIB_PATH.DS."shift.php");
require_once(LIB_PATH.DS."attraction_income.php");
require_once(LIB_PATH.DS."payment_method.php");
require_once(LIB_PATH.DS."income.php");
require_once(LIB_PATH.DS."income_report.php");
require_once(LIB_PATH.DS."outcome.php");
require_once(LIB_PATH.DS."outcome_report.php");;
