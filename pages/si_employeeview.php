<?php

use ru860e\rest\LDAP;
use ru860e\rest\Staff;
use ru860e\rest\LDAPTable;


$fio = isset($_POST['fio']) ? $_POST['fio'] :
       isset($_GET['fio']) ? $_GET['fio'] :
       "";
$dn = isset($_POST['dn']) ? $_POST['dn'] :
       isset($_GET['dn']) ? $_GET['dn'] :
       "";

$sortcolumn = isset($_POST['sortcolumn']) ? $_POST['sortcolumn'] :
       isset($_GET['sortcolumn']) ? $_GET['sortcolumn'] :
       "ФИО";

$sorttype = isset($_POST['sorttype']) ? $_POST['sorttype'] :
       isset($_GET['sorttype']) ? $_GET['sorttype'] :
       "ASC";

$CONFIG_PHOTO = $CONFIG['CONFIG_PHOTO'];
$CONFIG_LDAP_ATTRIBUTE = $CONFIG['CONFIG_LDAP_ATTRIBUTE'];
$CONFIG_PHONE = $CONFIG['CONFIG_PHONE'];

//$OU = $LDAP_USER['OU_USER_READ'];
$dn = $LDAP_USER['DN_USERS_PHONEBOOK'];

if($fio){
  $user=$ldap->getUser($dn,$fio);
  //$dn=$ldap->getValue($OU, $CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD'], "cn=".$fio);
}




if($CONFIG_PHOTO['DIRECT_PHOTO']) {
    $Image = $ldap->getImage($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_PHOTO_FIELD']);
    }
    else
	{
	$Image=$CONFIG_PHOTO['PHOTO_DIR']."/".md5($dn).".jpg";
	$Image=$ldap->getImage($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_PHOTO_FIELD'], $Image);
	}

function renderTemplate($template, $param = false) {
    ob_start();
    if ($param) {
        extract($param);
    }
    include($template);
}

$page_content = renderTemplate('vi_employeeview.php', array("title"=>"hello"));

if($Image)
	echo"<div class=\"photo\"><img src=\"".$Image."\" height=\"".$CONFIG_PHOTO['PHOTO_MAX_HEIGHT']."\" width=\"".$CONFIG_PHOTO['PHOTO_MAX_WIDTH']."\"></div>";
else
	echo"<div class=\"photo\"><img src=\"./skins/".$CONFIG_APP['CURRENT_SKIN']."/images/ldap/user.png\"></div>";
echo"</td>";
echo"<td>";

if($CONFIG_APP['USE_DISPLAY_NAME'])
	$Name=$ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['DISPLAY_NAME_FIELD']);
else
	$Name=$ldap->getValue($dn, "name");


$control=$ldap->getValue($dn, "useraccountcontrol");
$LockedCssClass= ((($control & 2)==2)||(($control & 2) == 16))?"locked":"";

$FIO=preg_replace("/^([ёA-zА-я-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)$/u", "<div class=\"surname_head ".$LockedCssClass."\">$1</div><div class=\"name ".$LockedCssClass."\">$2</div>", $Name);
$FIO=preg_replace("/^([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]{1}.)[\s]{1}([ёA-zА-я-]+)$/u", "<div class=\"surname_head ".$LockedCssClass."\">$2</div><div class=\"name ".$LockedCssClass."\">$1</div>", $FIO);

echo $FIO;

if($CONFIG_APP['SHOW_EVALUATION_PERIOD_MESSAGE'] && $CONFIG_LDAP_ATTRIBUTE['LDAP_CREATED_DATE_FIELD'])
	{
	$Created=$ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_CREATED_DATE_FIELD']);
	$CreatedUnixTime=Time::getTimeOfDMYHI($Created,$CONFIG_APP['LDAP_CREATED_DATE_FORMAT']);
	$NumWorkDays=round((Time::getOnlyDatePartFromTime(time())-Time::getOnlyDatePartFromTime($CreatedUnixTime))/(24*60*60));
	//if($NumWorkDays<=$EVALUATION_PERIOD)
	if(false)
		echo "<h6 class=\"alarm\">Новый сотрудник</h6> &mdash; <small>работает в компании <big>".$L->ending($NumWorkDays, 'день', 'дня', 'дней')."</big></small>";
	}

$Department=$ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']   );
$Title= $ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD'] );

if($Department)
	echo "<div class=\"position\"><nobr class=\"department\">".$staff->makeDepartment($Department)."</nobr> <br/><span class=\"position\">".$staff->makeTitle($Title)."</span></div>";

if($CONFIG_APP['VACATION'])
	{
	$e[0]=$ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_ST_DATE_VACATION_FIELD'] ); $e[1]=$ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD']);

	if($e[0]&&$e[1])
		{
		$VacationState=$staff->getVacationState($e[0], $e[1]);
		if($VacationState == 0)
			$tag="del";
		else if($VacationState < 0)
			{ $tag="span"; }
		else
			$tag="span";
		}
	else
		$tag="span";
	}
else
	{
	$tag="span";
	}

if(!$CONFIG_PHONE['HIDE_CITY_PHONE_FIELD'])
	echo "<div class=\"phone\"><h6>"
	.$localization->get('city_phone')
	.":</h6> <".$tag.">"
	.$staff->makeCityPhone($ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_CITY_PHONE_FIELD']))
	."</".$tag."></div>";

echo "<div class=\"otherphone\"><h6>".$localization->get('intrenal_phone').":</h6> <".$tag.">".$staff->makeInternalPhone($ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_INTERNAL_PHONE_FIELD']))."</".$tag."></div>";

if(!$CONFIG_PHONE['HIDE_CELL_PHONE_FIELD'])
	echo "<div class=\"otherphone\"><h6>".$localization->get('cell_phone').":</h6> ".$staff->makeCellPhone($ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_CELL_PHONE_FIELD']))."</div>";

if($HomePhone=$ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_HOMEPHONE_FIELD']))
	echo "<div class=\"otherphone\"><h6>".$localization->get('home_phone').":</h6> ".$staff->makeHomePhone($HomePhone)."</div>";

if(!$CONFIG_APP['HIDE_ROOM_NUMBER'])
	echo "<div class=\"otherphone\"><h6>".$localization->get('room_number').":</h6> ".$staff->makePlainText($ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD']))."</div>";

echo "<div class=\"email\"><h6>E-mail:</h6> ".$staff->makeMailUrl($ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD']))."</div>";


$StDate=$ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE["LDAP_ST_DATE_VACATION_FIELD"]);
$EndDate=$ldap->getValue($dn, $CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD'] );
$staff->printVacOnCurrentPage($StDate, $EndDate);

$DeputyDN=$ldap->getValue($dn, $LDAP_DEPUTY_FIELD);	
if($DeputyDN && $SHOW_DEPUTY && ($staff->checkInVacation($StDate, $EndDate) && $BIND_DEPUTY_AND_VACATION) || !$BIND_DEPUTY_AND_VACATION)
	{
	echo "<div class=\"employee birthday\">
		<h6>".$localization->get('deputy_for_vacation_period').":</h6><br/>";

	echo $staff->makeDeputy($DeputyDN, $ldap->getValue($DeputyDN, $DISPLAY_NAME_FIELD));
	echo "</div>";
	}

$Birth=$ldap->getValue($dn, $LDAP_BIRTH_FIELD);

//День рождения
//-----------------------------------------------------------------------------
if($Birth)
{
	switch($BIRTHDAYS['BIRTH_DATE_FORMAT'])
	{
		case 'yyyy-mm-dd':
		{
			$Date=explode("-", $Birth);
			$temp=$Date[0]; $Date[0]=$Date[2]; $Date[2]=$temp;
		} break;
		case 'dd.mm.yyyy':
		{
			$Date=explode(".", $Birth);
		} break;
		default: $Date=explode(".", $Birth);
	}

	$Jubilee="";
	if($BIRTHDAYS['SHOW_JUBILEE_INFO'])
		{	
		if(!((date("Y")-$Date[2])%5)) $Jubilee="<div>".$localization->get('round_date')."</div>";
		if(!((date("Y")-$Date[2])%10)) $Jubilee="<div>".$localization->get('jubilee')."</div>";
		}
	echo"<div class=\"birthday\"><h6>".$localization->get('birthday').":</h6> ".(int) $Date[0]." ".$MONTHS[(int) $Date[1]].". ".@$Jubilee."</div>";
}
//-----------------------------------------------------------------------------

$ManDN=$ldap->getValue($dn, $LDAP_MANAGER_FIELD);	
if($ManDN)
{
echo "<div class=\"employee\"><h6>".$localization->get('immediate_supervisor').":</h6><br>";
	if($USE_DISPLAY_NAME)
	{
		echo $staff->getClickUrlOnName($ManDN, $ldap->getValue($ManDN, $DISPLAY_NAME_FIELD));
	}
	else
		echo $staff->getClickUrlOnName($ManDN);
echo "</div>";
}

if (isset($Manager))
	echo $Manager;



echo"</td>";
echo"</tr>";

echo"<tr>";
echo"<td colspan='2'>";
echo"<div class=\"staff\" id=\"people\"><h6>Подчиненные:</h6></div>";
$table=new LDAPTable($LDAPServer, $LDAPUser, $LDAPPassword, false, false);

if($USE_DISPLAY_NAME)
	$table->addColumn($DISPLAY_NAME_FIELD.", distinguishedname", "ФИО", true, 0, false, "ad_def_full_name");
else	
	$table->addColumn("distinguishedname", "ФИО", true, 0, false, "ad_def_full_name");
$table->addColumn($LDAP_INTERNAL_PHONE_FIELD, $localization->get('intrenal_phone'), true);
$table->addColumn("title", "Должность");

$table->addPregReplace("/^(.*)$/eu", "$staff->getClickUrlOnName('\\1')", "ФИО");

$table->addPregReplace("/^\.\./u", "", "Должность");
$table->addPregReplace("/^\./u", "", "Должность");
$table->addPregReplace("/^(.*)$/eu", "$staff->makeInternalPhone('\\1')", $localization->get('intrenal_phone'));

echo"<div id=\"people_table\">";

$table->printTable($OU, "(&(company=*)(manager=".$ldap->escapeFilterValue($dn).")".$DIS_USERS_COND.")");
echo"</div>";
echo"</td>";
echo"</tr>";
echo"</table>";
?>