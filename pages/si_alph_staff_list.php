<form class="heads" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
    <?php
    use ru860e\rest\Application;
    use ru860e\rest\LDAP;
    use ru860e\rest\Staff;
    use ru860e\rest\Alphabet;

    //Получаем переменные для сортировки
    @$_GET['sortcolumn'] = ($_GET['sortcolumn']) ? $_GET['sortcolumn'] : "ФИО";
    @$_GET['sorttype'] = ($_GET['sorttype']) ? $_GET['sorttype'] : "ASC";

    $CONFIG_XMPP = $CONFIG['CONFIG_XMPP'];

    ?>
    <div class="heads">
        <?php
        if ($BLOCK_VIS[$menu_marker]['birthdays'])
            include("./libs/birth.php");
        if ($BLOCK_VIS[$menu_marker]['search'])
            include("./libs/search.php");
        if ($BLOCK_VIS[$menu_marker]['profile'])
            include("./libs/profile.php");
        if ($BLOCK_VIS[$menu_marker]['fast_move']) {
            echo "<br/>";
            $alphabet = new Alphabet();
            $alphabet->printGeneralLetters($localization,$CONFIG_APP); //Печатаем буквы алфавита, для быстрого перехода на сотрудников
        }
        ?>
    </div>
</form>
<?php
//$ldap = new LDAP($LDAPServer, $LDAPUser, $LDAPPassword); //Соединяемся с сервером
// Делаем фильтр для выборки сотрудников нужных компаний
//-------------------------------------------------------------------------------------------------------------
$CompanyNameLdapFilter = $application->getCompanyNameLdapFilter();
//-------------------------------------------------------------------------------------------------------------
// Определяем какой атрибут будем использовать в качестве формирования ФИО сотрудника
//-------------------------------------------------------------------------------------------------------------
if ($USE_DISPLAY_NAME)
    $DisplayName = $DISPLAY_NAME_FIELD;
else
    $DisplayName = $LDAP_NAME_FIELD;
//-------------------------------------------------------------------------------------------------------------
$LdapListAttrs = array($CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD'], $DisplayName,
    $LDAP_MAIL_FIELD,
    $LDAP_INTERNAL_PHONE_FIELD,
    $LDAP_CITY_PHONE_FIELD,
    $LDAP_ST_DATE_VACATION_FIELD,
    $LDAP_END_DATE_VACATION_FIELD,
    $LDAP_TITLE_FIELD,
    $LDAP_DEPARTMENT_FIELD,
    $LDAP_CELL_PHONE_FIELD,
    $LDAP_MANAGER_FIELD,
    $LDAP_COMPUTER_FIELD,
    $LDAP_DEPUTY_FIELD,
    $LDAP_GUID_FIELD,
    $LDAP_USERPRINCIPALNAME_FIELD,
    $LDAP_ROOM_NUMBER_FIELD);
//Получаем правильно отсортированных сотрудников с необходимыми атрибутами LDAP

$Staff = $ldap->getArray(
    $LDAP_USER['OU_USER_READ'],
    "(&(objectCategory=person)(objectClass=user)$DIS_USERS_COND)",
    $LdapListAttrs,
    array($DisplayName, array('ad_def_full_name')));


if (is_array($Staff)) {
    // Шапка таблицы
    //-------------------------------------------------------------------------------------------------------------
    echo "
		<table class=\"sqltable\" cellpadding=\"4\">
		<th><div>" . $localization->get('full_name') . "</div></th>
		<th><div>" . $localization->get('position') . "</div></th>
		<th><div>" . $localization->get('email') . "</div></th>";
    if (!$HIDE_ROOM_NUMBER)
        echo "<th><div>" . $localization->get('room_number') . "</div></th>";
    echo "
		<th><div>" . $localization->get('intrenal_phone') . "</div></th>
		";
    if (!$HIDE_CITY_PHONE_FIELD)
        echo "<th><div>" . $localization->get('city_phone') . "</div></th>";
    if (!$HIDE_CELL_PHONE_FIELD)
        echo "<th><div>" . $localization->get('cell_phone') . "</div></th>";
    if ($staff->showComputerName($Login)) //Если сотрудник является администратором справочника
        echo "<th><div>Компьютер</div></th>";
    if ($GLOBALS['XMPP_ENABLE'] && $GLOBALS['XMPP_MESSAGE_LISTS_ENABLE'] && !empty($_COOKIE['dn']))
        echo "<th><div></div></th>";
    if ($FAVOURITE_CONTACTS && isset($_COOKIE['dn']))
        echo "<th><div></div></th>";
    if (empty($_COOKIE['dn']) && $ENABLE_DANGEROUS_AUTH)
        echo $application->getCollTitle();
    //-------------------------------------------------------------------------------------------------------------
    $FavouriteDNs = array();
    if (isset($_COOKIE['dn'])) {
        $FavouriteDNs = $ldap->getAttrValue($_COOKIE['dn'], $LDAP_FAVOURITE_USER_FIELD);
    }
    //Выводим пользователей, которые есть в избраном
    if ($GLOBALS['FAVOURITE_CONTACTS'] && is_array($FavouriteDNs)) {
        $Filter = "(&(" . $LDAP_CN_FIELD . "=*)" . $DIS_USERS_COND . "(|(" . $CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD'] . "=" . implode(")(" . $CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD'] . "=", $ldap->escapeFilterValue($FavouriteDNs)) . ")))";

        $Favourites = $ldap->getArray($OU, $Filter, $LdapListAttrs);
        if (is_array($Favourites)) {
            $row = 0;
            foreach ($Favourites[$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']] AS $key => $value) {
                $Vars['row_css'] = ($row % 2) ? "even favourite" : "odd favourite";
                $Vars['current_login'] = $Login;
                $Vars['display_name'] = $DisplayName;
                $Vars['ldap_conection'] = $ldap;
                $Vars['favourite_dns'] = $FavouriteDNs;
                $Vars['data_parent_id'] = true;
                $Vars['id'] = false;
                $staff->printUserTableRow($Favourites, $key, $Vars);
                $row++;
            }
        }
    }
    $row = 0;    // переменная, используемая для нумерации строк таблицы
    //Перебираем всех выбраных пользователей
    foreach ($Staff[$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']] AS $key => $value) {
        $Surname = $staff->getSurname($Staff[$DisplayName][$key]);
        if (mb_substr($Surname, 0, 1, 'UTF-8') != @$FiLe) //Если ФИО сотрудника начинается с другой буквы чем у предыдущего
        {
            $FiLe = mb_substr($Surname, 0, 1, 'UTF-8'); //Сохраняем первую букву ФИО для дальнейше проверки
            echo "
			<tr>
				<td colspan=\"" . $staff->getNumStaffTableColls() . "\">
					<a href=\"#move_to_letter\" class=\"in_link uarr\" >&uarr;</a>
					<span class=\"sep_letter\" id=\"s_l_" . $row . "\">" . $FiLe . "</span>
				</td>
			</tr>
			"; // и печатаем разделитель
        }

        $Vars['row_css'] = ($row % 2) ? "even" : "odd";
        $Vars['current_login'] = $Login;
        $Vars['display_name'] = $DisplayName;
        $Vars['ldap_conection'] = $ldap;
        $Vars['favourite_dns'] = $FavouriteDNs;
        $Vars['data_parent_id'] = false;
        $Vars['id'] = true;
        // Вывод остальных сотрудников
        $staff->printUserTableRow($Staff, $key, $Vars);
        $row++;
    }
    echo "</table>";
}
?>