<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once("./libs/forms.php");
require_once("./libs/staff.php");
require_once("./libs/time.php");
require_once("./libs/localization.php");
require_once("./libs/spyc.php");

use ru860e\rest\Application;
use ru860e\rest\LDAP;
use ru860e\rest\LdapAssistant;
use ru860e\rest\Localization;
use ru860e\rest\ConfigHandler;
use ru860e\rest\Auth;
use ru860e\rest\Staff;
use ru860e\rest\Phones;

$configHandler = new ConfigHandler();
$CONFIG = $configHandler->getConfig();
$CONFIG_PHONE = $configHandler->getConfigPhone();
$LDAP_USER = $CONFIG['LDAP_USER'];
$CONFIG_APP = $CONFIG['CONFIG_APP'];

//Database
//----------------------------------------
$ldap = new LDAP($LDAP_USER['SERVER_LDAP'], $LDAP_USER['USER_READ'], $LDAP_USER['PASSWORD_USER_READ'],$CONFIG); //Соединяемся с сервером
$ldapAssistant = new LdapAssistant($ldap,$CONFIG);
//----------------------------------------

$application = new Application($ldap,$CONFIG['BOOKMARK']);
$application->makeLdapConfigAttrLowercase(); //Преобразуем все атрибуты LDAP в нижний регистр.

$localization = new Localization("./config/locales/" . $CONFIG_APP['LOCALIZATION'] . ".yml");
$phones = new Phones($CONFIG_PHONE,$CONFIG);
$staff = new Staff($CONFIG,$application,$phones);

$auth = new Auth($ldap, $CONFIG);

setlocale(LC_CTYPE, "ru_RU." .
          $CONFIG_APP['CHARSET_APP']);

$menu_marker= isset($_POST['menu_marker']) ? $_POST['menu_marker'] :
              $_GET['menu_marker'];

//Basic Auth
//----------------------------------------	
//include_once("auth.php");
//----------------------------------------	
?>
<html>

	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/newwin.css" type="text/css" />
		<link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/staff.css" type="text/css" />
		<link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/si_print_vacation_claim.css" type="text/css" />
		<link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/general.css" type="text/css" />
		<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
		<script type="text/javascript" src="./js/prototype.js"></script>
		<script type="text/javascript" src="./js/smartform.js"></script>
		<script type="text/javascript" src="./js/staff.js"></script>
		<script type="text/javascript" src="./js/si_print_vacation_claim.js"></script>		
	</head>

	<body>
		<?php	
        // Вывод справочника
        if (is_file($CONFIG_APP['PHPPath']. "/" . $menu_marker . ".php")) {
            include($CONFIG_APP['PHPPath']. "/" . $menu_marker . ".php");
        }
		?>
	</body>

</html>