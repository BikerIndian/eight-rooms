<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use ru860e\controllers;
use ru860e\rest\LdapAssistant;
use ru860e\rest\Localization;
use ru860e\rest\Application;
use ru860e\rest\ConfigHandler;
use ru860e\rest\LDAP;
use ru860e\rest\Staff;
use ru860e\rest\Phones;
//use ru860e\rest\LdapAssistant;

//require_once("./config.php");
require_once("./libs/forms.php");
require_once("./libs/staff.php");
//require_once("./libs/phones.php");
require_once("./libs/time.php");
require_once("./libs/localization.php");
require_once("./libs/spyc.php");
require_once("./vendor/controllers/SiteController.php");


$configHandler = new ConfigHandler();
$CONFIG = $configHandler->getConfig();

$CONFIG_XMPP = $CONFIG['CONFIG_XMPP'];
$CONFIG_APP = $CONFIG['CONFIG_APP'];
$LDAP_USER = $CONFIG['LDAP_USER'];
$ADMINS = $LDAP_USER['ADMINS'];

$BOOKMARK_NAMES = $CONFIG['BOOKMARK']['BOOKMARK_NAMES'];
$PAGE_LINKS = $CONFIG['PAGE_LINKS'];
$CONFIG_PDF = $CONFIG['CONFIG_PDF'];
$CONFIG_EXEL = $CONFIG['CONFIG_EXEL'] ;
$CONFIG_PHONE = $CONFIG['CONFIG_PHONE'];
$BLOCK_VIS = $CONFIG['BLOCK_VIS'];
$BIRTHDAYS = $CONFIG['BIRTHDAYS'];
$CONFIG_LDAP_ATTRIBUTE = $CONFIG['LDAP_ATTRIBUTE'];

$CURRENT_SKIN = $CONFIG_APP['CURRENT_SKIN'];




$Controller = new controllers\SiteController();
//$localization->get('by_alphabet')

$localization = new Localization("./config/locales/" . $CONFIG_APP['LOCALIZATION'] . ".yml");




//Database
//----------------------------------------
$ldap = new LDAP($LDAP_USER['SERVER_LDAP'], $LDAP_USER['USER_READ'], $LDAP_USER['PASSWORD_USER_READ'],$CONFIG); //Соединяемся с сервером
$ldapAssistant = new LdapAssistant($ldap,$CONFIG);
//----------------------------------------	
$application = new Application($ldap,$CONFIG['BOOKMARK']);
$application->makeLdapConfigAttrLowercase(); //Преобразуем все атрибуты LDAP в нижний регистр.
$phones = new Phones($configHandler->getConfigPhone(),$CONFIG);

$staff = new Staff($CONFIG,$application,$phones);


setlocale(LC_CTYPE, "ru_RU." . $CONFIG_APP['CHARSET_APP']);

// Определяем вывод страницы
$menu_marker = isset($_POST['menu_marker']) ? $_POST['menu_marker'] :
               isset($_GET['menu_marker']) ? $_GET['menu_marker'] :
               $CONFIG_APP['DEFAULT_PAGE'];



// выводим по отделам
//$menu_marker="si_dep_staff_list";
$only_bookmark = isset($_POST['only_bookmark']) ? $_POST['only_bookmark'] :
                 isset($_GET['only_bookmark']);

$BOOKMARK_NAME = isset($_POST['bookmark_name']) ? $_POST['bookmark_name'] :
                 isset($_GET['bookmark_name']) ? $_GET['bookmark_name'] :
                 current(array_keys($BOOKMARK_NAMES[current(array_keys($BOOKMARK_NAMES))]));


if (isset($_POST['form_sent']) && !isset($GLOBALS['only_bookmark'])) //Если отправлена форма поиска и флажок "только во вкладке не был установлен"

// !!!
$BOOKMARK_NAME = "";
$bookmark_name = $BOOKMARK_NAME;
$bookmark_attr = isset($_POST['bookmark_attr']) ? $_POST['bookmark_attr'] :
                 isset($_GET['bookmark_attr']) ? $_GET['bookmark_attr'] :
                 current(array_keys($BOOKMARK_NAMES));


//Записываем переменные в массив. Массив используется для формирование скрытых полей форм и url-ов.
//-------------------------------------------------------------------------------------------------
$CurrentVars['menu_marker'] = $menu_marker;
$CurrentVars['bookmark_name'] = $bookmark_name;
$CurrentVars['bookmark_attr'] = $bookmark_attr;
$CurrentVars['only_bookmark'] = $only_bookmark;
//-------------------------------------------------------------------------------------------------

if (@$_POST['form_sent'] && (!$only_bookmark))
    $BOOKMARK_NAME = "*";


//Аутентификация для Staff
//-------------------------------------------------------------------------------------------------

@$dn = ($_GET['dn']) ? $_GET['dn'] : $_POST['dn'];

if (@$_GET['iamnot']) { //Если нажата кнопка выход, то уничтожаем куку
    setcookie('dn');
    $_COOKIE['dn'] = "";
}


if (@$_SERVER['REMOTE_USER']) { //Если есть прозрачно аутентифицированный пользователь. И в серверной переменной хранится его логин

    if ($DistinguishedName = $ldap->getValue($OU, $CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD'], $LDAP_USERPRINCIPALNAME_FIELD . "=" . $_SERVER['REMOTE_USER'] . "*")) { //Находим его distinguishedname
        //Сохраняем куку с distinguishedname, что бы в дальнейшем аутентифицировать пользователя по куке.
        setcookie('dn', $DistinguishedName, time() + 5000 * 24 * 60 * 60, "/");
        $_COOKIE['dn'] = $DistinguishedName;
    }
} else {
    if (@$_POST['password']) { //Если пользователь ввел пароль в ручную
        $LC = ldap_connect($LDAPServer); //Соединяемся с сервером LDAP
        if (@ldap_bind($LC, $ldap->getValue($dn, $LDAP_USERPRINCIPALNAME_FIELD), $_POST['password'])) { //Проверяем что пользователь может соединится с сервером LDAP используя введенный пароль.
            setcookie('dn', $dn, time() + 5000 * 24 * 60 * 60, "/"); //Сохраняем куку с distinguishedname, что бы в дальнейшем аутентифицировать пользователя по куке.
            $_COOKIE['dn'] = $dn;
        }
        /*
          else
          $Error['password']=true; */
    }
}

//-------------------------------------------------------------------------------------------------	
//Аутентификация пользователя
//----------------------------------------	
include_once("auth.php");
//----------------------------------------	
//Если есть кука с dn, то ищется имя залогиненого пользователя
if (isset($_COOKIE['dn'])) {
    if ($USE_DISPLAY_NAME)
        $WhoAreYou = $ldap->getValue($_COOKIE['dn'], $DISPLAY_NAME_FIELD);
    else
        $WhoAreYou = $ldap->getValue($_COOKIE['dn'], $LDAP_NAME_FIELD);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>

<head>
    <meta name="author" content="Vladimir Pitin"/>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title><?php echo $TITLE; ?></title>

    <link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/main.css" type="text/css"/>
    <link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/staff.css" type="text/css"/>
    <link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/calendar/calendar.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/lightview/lightview.css"/>
    <link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/general.css" type="text/css"/>
    <link rel="shortcut icon" href="./skins/<?php echo $CURRENT_SKIN; ?>/favicon.ico" type="image/x-icon">
    <script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>


    <script type="text/javascript" src="./js/prototype.js"></script>


    <script type="text/javascript" src="./js/staff.js"></script>
    <script type="text/javascript" src="./js/calendar/calendar.js"></script>
    <script type="text/javascript" src="./js/calendar/calendar-ru.js"></script>
    <script type="text/javascript" src="./js/calendar/calendar-setup.js"></script>
    <script type="text/javascript" src="./js/smartform.js"></script>
    <script type="text/javascript" src="./js/spinners/spinners.min.js"></script>
    <script type="text/javascript" src="./js/lightview/lightview.js"></script>
</head>

<body onLoad="scroll();">

<?php
if ($CONFIG_XMPP['XMPP_ENABLE'])
    echo "<div id=\"send_xmpp_message\" class=\"lightview\">" . $localization->get('send_message') . "</div>";
?>


<?php
if ($CONFIG['ALARM_MESSAGE']) {
    echo "<div class=\"alarm\" id=\"alarm_mess\">" . $ALARM_MESSAGE . "</div>";
}
?>


<table class="main" align="center" cellpadding="5px" cellspacing="0px">

    <tr>
        <td class="companies">

            <div class="sep_tabs">
                <?php
                //Вывод закладок компаний
                if (sizeof($BOOKMARK_NAMES) > 1) {
                    $i = 0;
                    foreach ($BOOKMARK_NAMES AS $key => $value) {
                        if ($i != 0)
                            $class = "border";
                        else
                            $class = "";

                        $BookMarkLinks = $application->getBookMarkLinks($key, $class);
                        echo implode(current($BookMarkLinks));

                        if (is_array($BookMarkLinks['window']))
                            echo $application->makeWindow($BookMarkLinks['window']);
                        $i++;
                    }
                }
                ?>
            </div>

            <div class="sep_tabs">
                <?php
                //Вывод закладок на различные способы отображения справочника
                if (sizeof($PAGE_LINKS) > 1) {
                    foreach ($PAGE_LINKS AS $key => $value) {
                        if ($menu_marker == $key) {
                            echo "<div class=\"sel views tab\">" . $value . "</div>";
                        } else {
                            echo "<div class=\"tab views\"><a href=\"" . $_SERVER['PHP_SELF'] . "?bookmark_name=" . $BOOKMARK_NAME . "&bookmark_attr=" . $bookmark_attr . "&menu_marker=" . $key . "\">" . $value . "</a></div>";
                        }
                    }
                }
                ?>
            </div>

            <div class="sep_tabs">
                <?php //
                //Вывод PDF

                if ($CONFIG_PDF['ENABLE_PDF_EXPORT']) {
                    ?>
                    <div class="tab export">
                     <a id="exp_pdf_sep_dep"
                       href="./pages/si_export_pdf_department.php?bookmark_name=<?php echo $BOOKMARK_NAME; ?>&bookmark_attr=<?php echo $bookmark_attr; ?>"
                       target="_blank" class="in_link"><?php echo $localization->get('by_department'); ?>
                      </a>
                    </div>

                    <div class="tab export">
                      <a id="exp_pdf_sep_alph"
                       href="./pages/si_export_pdf_alphabet.php?bookmark_name=<?php echo $BOOKMARK_NAME; ?>&bookmark_attr=<?php echo $bookmark_attr; ?>"
                       target="_blank" class="in_link"><?php echo $localization->get('by_alphabet'); ?>
                      </a>
                    </div>
                <?php } ?>


                <?php if ($CONFIG_EXEL['ENABLE_EXEL_EXPORT']) {
                    ?>
                    <div class="tab export">
                    <a id="exp_exl_sep_dep" href="./pages/si_export_xlsx_department.php"
                                               target="_blank" class="in_link"><?php echo $localization->get('by_department'); ?></a>
                    </div>

                <?php } ?>


            </div>

        </td>
    </tr>

    <tr>
        <td>
            <?php
            // Вывод справочника
            if (is_file($CONFIG_APP['PHPPath']. "/" . $menu_marker . ".php")) {
                include($CONFIG_APP['PHPPath']. "/" . $menu_marker . ".php");
            }
            ?>
        </td>
    </tr>

    <tr class="copyright" align="center">
        <td><?php
            $Controller->actionFoter();
            ?></td>
    </tr>

</table>
</body>
</html>