<?php
use ru860e\rest\Application;
use ru860e\rest\LDAP;
use ru860e\rest\Staff;

if (isset($_GET['sortcolumn'])){$_GET['sortcolumn']=($_GET['sortcolumn'])?$_GET['sortcolumn']:"ФИО";}
if (isset($_GET['sorttype'])){$_GET['sorttype']=($_GET['sorttype'])?$_GET['sorttype']:"ASC";}

?>
<form class="heads" method="POST" action="<?php echo $_SERVER['PHP_SELF']?>?menu_marker=si_stafflist">
<div class="heads">
<?php
if($BLOCK_VIS[$menu_marker]['birthdays'])
	include("./libs/birth.php");
if($BLOCK_VIS[$menu_marker]['search'])	
	include("./libs/search.php");
if($BLOCK_VIS[$menu_marker]['profile'])
	include("./libs/profile.php");
?>
</div>
<?php
//Печатаем контейнер в который JS будет класть ссылки для быстрого перехода на отделы
if($BLOCK_VIS[$menu_marker]['fast_move'])
{
	echo "<br/>
	<span id=\"ALPH_ITEM_IN_LINE\" class=\"h\">".$DEP_ITEM_IN_COL."</span>
	<fieldset id=\"move_to_dep\">
		<legend>".$L->l('fast_move_to_department')."</legend>
	</fieldset>
	";
}
?>
</form>
<?php
//=================================================================================================================
$ldap=new LDAP($LDAPServer, $LDAPUser, $LDAPPassword); //Соединяемся с сервером
// Делаем фильтр для выборки сотрудников нужных компаний
//-------------------------------------------------------------------------------------------------------------
$CompanyNameLdapFilter=Application::getCompanyNameLdapFilter();
//-------------------------------------------------------------------------------------------------------------
// Определяем какой атрибут будем использовать в качестве формирования ФИО сотрудника
//-------------------------------------------------------------------------------------------------------------
if($USE_DISPLAY_NAME)
	$DisplayName=$DISPLAY_NAME_FIELD;
else
	$DisplayName=$LDAP_NAME_FIELD;
//-------------------------------------------------------------------------------------------------------------
$LdapListAttrs = array($LDAP_DISTINGUISHEDNAME_FIELD, $DisplayName,
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
//Получаем правильно отсортированных сотрудников с необходимыми атрибутами LDAP, учитывая настроки сортировки из конфига

$inquiry = "(&(objectCategory=person)$DIS_USERS_COND)";

$Staff=$ldap->getArray($OU,
	$inquiry,
	$LdapListAttrs,
  	array($LDAP_DEPARTMENT_FIELD, $DEP_SORT_ORDER, $LDAP_TITLE_FIELD, $STAFF_SORT_ORDER, $DisplayName));


if(is_array($Staff))
{
	// Шапка таблицы
	//-------------------------------------------------------------------------------------------------------------
	echo "
		<table class=\"sqltable\" cellpadding=\"4\">
		<th><div>".$L->l('full_name')."</div></th>
		<th><div>".$L->l('position')."</div></th>
		<th><div>".$L->l('email')."</div></th>";
	if(!$HIDE_ROOM_NUMBER)
		echo "<th><div>".$L->l('room_number')."</div></th>";
	echo"
		<th><div>".$L->l('intrenal_phone')."</div></th>
		";
	if(!$HIDE_CITY_PHONE_FIELD)
		echo "<th><div>".$L->l('city_phone')."</div></th>";	
	if(!$HIDE_CELL_PHONE_FIELD)
		echo "<th><div>".$L->l('cell_phone')."</div></th>";
	if(Staff::showComputerName($Login)) //Если сотрудник является администратором справочника
		echo "<th><div>".$L->l('pc')."</div></th>";
	if($GLOBALS['XMPP_ENABLE'] && $GLOBALS['XMPP_MESSAGE_LISTS_ENABLE'] && !empty($_COOKIE['dn']))	
		echo "<th><div></div></th>";	
	if($FAVOURITE_CONTACTS && isset($_COOKIE['dn']))
		echo "<th><div></div></th>";
	if(empty($_COOKIE['dn']) && $ENABLE_DANGEROUS_AUTH)
		echo Application::getCollTitle();
	//-------------------------------------------------------------------------------------------------------------
    $FavouriteDNs=[];
    if(isset($_COOKIE['dn'])){
        $FavouriteDNs=$ldap->getAttrValue($_COOKIE['dn'], $LDAP_FAVOURITE_USER_FIELD);
    }


	//Выводим пользователей, которые есть в избраном
	if($GLOBALS['FAVOURITE_CONTACTS'] && is_array($FavouriteDNs))
		{
		$Filter="(&(".$LDAP_CN_FIELD."=*)".$DIS_USERS_COND."(|(".$LDAP_DISTINGUISHEDNAME_FIELD."=".implode(")(".$LDAP_DISTINGUISHEDNAME_FIELD."=", LDAP::escapeFilterValue($FavouriteDNs)).")))";
		//echo "$Filtersaasdas";
		$Favourites=$ldap->getArray($OU, $Filter, $LdapListAttrs);
		if(is_array($Favourites))
			{
			$row=0;
			foreach($Favourites[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
				{	
				$Vars['row_css']=($row%2) ? "even favourite" : "odd favourite";
				$Vars['current_login']=$Login;
				$Vars['display_name']=$DisplayName;
				$Vars['ldap_conection']=$ldap;
				$Vars['favourite_dns']=$FavouriteDNs;
				$Vars['data_parent_id']=true;
				$Vars['id']=false;
				Staff::printUserTableRow($Favourites, $key, $Vars);
				$row++;
				}
			}
		}
	$row=0;	// переменная, используемая для нумерации строк таблицы
	foreach($Staff[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
	{
		if($Staff[$LDAP_DEPARTMENT_FIELD][$key]!=@$prevDEP) //Если отдел текущего сотрудника аналогичен отделу предыдущего
		{
			if(strpos($Staff[$LDAP_DEPARTMENT_FIELD][$key], @$inclusionDEP)===0) //Если предыдущий отдел начинается с нового отдела (т.е. новый подстрока предыдущего)
			{
				$depCSS="department";
			}
			else
			{
				$depCSS="division";
				$inclusionDEP=($Staff[$LDAP_DEPARTMENT_FIELD][$key])?$Staff[$LDAP_DEPARTMENT_FIELD][$key]:' ';
			}
			$prevDEP=$Staff[$LDAP_DEPARTMENT_FIELD][$key];
			echo "
			<tr>
				<td colspan=\"".Staff::getNumStaffTableColls()."\">
					<div class=\"department_title ".$depCSS."\">
						<a href=\"#move_to_dep\" class=\"in_link uarr\" >&uarr;</a>
						<span id=\"dep_".$row."\">".Staff::makeDepartment($Staff[$LDAP_DEPARTMENT_FIELD][$key], true)."</span>
					</div>
				</td>
			</tr>
			";
		}
		
		$Vars['row_css']=($row%2) ? "even" : "odd";
		$Vars['current_login']=$Login;
		$Vars['display_name']=$DisplayName;
		$Vars['ldap_conection']=$ldap;
		$Vars['favourite_dns']=$FavouriteDNs;
		$Vars['data_parent_id']=false;
		$Vars['id']=true;
		Staff::printUserTableRow($Staff, $key, $Vars);
		$row++;
	}
	echo"</table>";	
}
?>