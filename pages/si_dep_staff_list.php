<form class="heads" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<?php
use ru860e\rest\Application;
use ru860e\rest\LDAP;
use ru860e\rest\Staff;

if (isset($_GET['sortcolumn'])){$_GET['sortcolumn']=($_GET['sortcolumn'])?$_GET['sortcolumn']:"ФИО";}
if (isset($_GET['sorttype'])){$_GET['sorttype']=($_GET['sorttype'])?$_GET['sorttype']:"ASC";}

?>
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
	<span id=\"ALPH_ITEM_IN_LINE\" class=\"h\">".$CONFIG_APP['DEP_ITEM_IN_COL']."</span>
	<fieldset id=\"move_to_dep\">
		<legend>".$localization->get('fast_move_to_department')."</legend>
	</fieldset>
	";
}
?>
</form>
<?php
//=================================================================================================================
// Делаем фильтр для выборки сотрудников нужных компаний
//-------------------------------------------------------------------------------------------------------------
$CompanyNameLdapFilter=$application->getCompanyNameLdapFilter();
//-------------------------------------------------------------------------------------------------------------
// Определяем какой атрибут будем использовать в качестве формирования ФИО сотрудника
//-------------------------------------------------------------------------------------------------------------
if($CONFIG_APP['USE_DISPLAY_NAME'])
	$DisplayName=$CONFIG_LDAP_ATTRIBUTE['DISPLAY_NAME_FIELD']  ;
else
	$DisplayName=$CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD'];
//-------------------------------------------------------------------------------------------------------------
$LdapListAttrs = array(
        $CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD'],
        $DisplayName,
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_INTERNAL_PHONE_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_CITY_PHONE_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_ST_DATE_VACATION_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_CELL_PHONE_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_MANAGER_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_COMPUTER_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPUTY_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_USERPRINCIPALNAME_FIELD'],
  		$CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD']);
//Получаем правильно отсортированных сотрудников с необходимыми атрибутами LDAP, учитывая настроки сортировки из конфига

$inquiry = "(&(objectCategory=person)".$CONFIG_LDAP['DIS_USERS_COND'].")";

/*
$staffUserList=$ldap->getArray(
    $LDAP_USER['OU_USER_READ'],
	$inquiry,
	$LdapListAttrs,
  	array(
  	    $CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD'],
  	    $CONFIG['DEP_SORT_ORDER'],
  	    $CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD'],
  	    $CONFIG['STAFF_SORT_ORDER'],
  	    $DisplayName));
*/
$dn = $CONFIG_APP['DN_USERS_PHONEBOOK'];
$staffUserList = $ldap->getArrayUsers($dn);

if(is_array($staffUserList))
{
	// Шапка таблицы
	//-------------------------------------------------------------------------------------------------------------
	echo "
		<table class=\"sqltable\" cellpadding=\"4\">
		<th><div>".$localization->get('full_name')."</div></th>
		<th><div>".$localization->get('position')."</div></th>
		<th><div>".$localization->get('email')."</div></th>";
	if(!$CONFIG_APP['HIDE_ROOM_NUMBER'])
		echo "<th><div>".$localization->get('room_number')."</div></th>";
	echo"
		<th><div>".$localization->get('intrenal_phone')."</div></th>
		";
	if(!$CONFIG_PHONE['HIDE_CITY_PHONE_FIELD'])
		echo "<th><div>".$localization->get('city_phone')."</div></th>";
	if(!$CONFIG_PHONE['HIDE_CELL_PHONE_FIELD'])
		echo "<th><div>".$localization->get('cell_phone')."</div></th>";
	if($staff->showComputerName($Login)) //Если сотрудник является администратором справочника
		echo "<th><div>".$localization->get('pc')."</div></th>";
	if($CONFIG_XMPP['XMPP_ENABLE'] && $CONFIG_XMPP['XMPP_MESSAGE_LISTS_ENABLE'] && !empty($_COOKIE['dn']))
		echo "<th><div></div></th>";	
	if($CONFIG_APP['FAVOURITE_CONTACTS'] && isset($_COOKIE['dn']))
		echo "<th><div></div></th>";
	if(empty($_COOKIE['dn']) && $CONFIG_APP['ENABLE_DANGEROUS_AUTH'])
		echo $application->getCollTitle();
	//-------------------------------------------------------------------------------------------------------------
    $FavouriteDNs=[];
    if(isset($_COOKIE['dn'])){
        $FavouriteDNs=$ldap->getAttrValue($_COOKIE['dn'], $LDAP_FAVOURITE_USER_FIELD);
    }


	//Выводим пользователей, которые есть в избраном
	if($CONFIG_APP['FAVOURITE_CONTACTS'] && is_array($FavouriteDNs))
		{
		$Filter="(&(".$CONFIG_LDAP_ATTRIBUTE['LDAP_CN_FIELD']."=*)".$CONFIG_LDAP['DIS_USERS_COND']."(|(".$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']."=".implode(")(".$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']."=", $ldap->escapeFilterValue($FavouriteDNs)).")))";
		//echo "$Filtersaasdas";
		$Favourites=$ldap->getArray($LDAP_USER['OU_USER_READ'], $Filter, $LdapListAttrs);
		if(is_array($Favourites))
			{
			$row=0;
			foreach($Favourites[$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']] AS $key=>$value)
				{	
				$Vars['row_css']=($row%2) ? "even favourite" : "odd favourite";
				$Vars['current_login']=$Login;
				$Vars['display_name']=$DisplayName;
				$Vars['ldap_conection']=$ldap;
				$Vars['favourite_dns']=$FavouriteDNs;
				$Vars['data_parent_id']=true;
				$Vars['id']=false;
				$staff->printUserTableRow($Favourites, $key, $Vars);
				$row++;
				}
			}
		}
	$row=0;	// переменная, используемая для нумерации строк таблицы

	foreach ($staffUserList as $user) {

        //echo $user->DISPLAY_NAME_FIELD."<br>";
    }


	foreach($staffUserList[$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']] AS $key=>$value)
	{
		if($staffUserList[$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']][$key]!=@$prevDEP) //Если отдел текущего сотрудника аналогичен отделу предыдущего
		{
			if(strpos($staffUserList[$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']][$key], @$inclusionDEP)===0) //Если предыдущий отдел начинается с нового отдела (т.е. новый подстрока предыдущего)
			{
				$depCSS="department";
			}
			else
			{
				$depCSS="division";
				$inclusionDEP=($staffUserList[$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']][$key])?$staffUserList[$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']][$key]:' ';
			}
			$prevDEP=$staffUserList[$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']][$key];
			echo "
			<tr>
				<td colspan=\"".$staff->getNumStaffTableColls()."\">
					<div class=\"department_title ".$depCSS."\">
						<a href=\"#move_to_dep\" class=\"in_link uarr\" >&uarr;</a>
						<span id=\"dep_".$row."\">".$staff->makeDepartment($staffUserList[$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']][$key], true)."</span>
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
		$staff->printUserTableRow($staffUserList, $key, $Vars);
		$row++;
	}
	echo"</table>";	
}
?>