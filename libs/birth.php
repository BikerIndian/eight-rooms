<?php
use ru860e\rest\Application;
use ru860e\rest\LDAP;
use ru860e\rest\Staff;
use ru860e\rest\LDAPTable;


$CONFIG_LDAP_ATTRIBUTE  = $CONFIG['CONFIG_LDAP_ATTRIBUTE'];
$CONFIG_LDAP            = $CONFIG['CONFIG_LDAP'];
$BIRTHDAYS              = $CONFIG['BIRTHDAYS'];
$LDAP_USER              = $CONFIG['LDAP_USER'];


if($BIRTHDAYS['NEAR_BIRTHDAYS']) {

    $ldapListAttrs = array(
                    $CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD'],
                    $CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD'],
                    $CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']
                    );

    $filterBirth = getFilterBirth(
                $CONFIG_LDAP_ATTRIBUTE,
                $BIRTHDAYS,
                $CONFIG_LDAP['DIS_USERS_COND'],
                time()
                );

    $userList = getUserList($ldapConnector,$LDAP_USER['OU_USER_READ'] ,$filterBirth,$ldapListAttrs);

    printBirth($L,$CONFIG_LDAP_ATTRIBUTE,$BIRTHDAYS,$userList);

}

    function printBirth($localization,$CONFIG_LDAP_ATTRIBUTE,$BIRTHDAYS,$userList)
	{


        echo"<div class=\"heads\">
            <fieldset class=\"birthdays\">
            <legend>".$localization->l('nearest')." ".$BIRTHDAYS['NUM_ALARM_DAYES']." ".$localization->l('they_have_birthdays').":</legend>";
        echo "<table class='sqltable' cellpadding='4'>";

	    if (is_array($userList)) {
                $row = 0;
                //foreach ($userList[$CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD']] AS $key => $value) {
                foreach ($userList AS $key => $user) {
                    $row++;
                    //$name =  $userList[$CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD']][$key];
                    $name =  $user->DISPLAY_NAME_FIELD;
                //     echo print_r($user);
                    $ou =  $user->LDAP_DISTINGUISHEDNAME_FIELD;

                    $link = getName($name,$ou);
                    $birth =  $user->LDAP_BIRTH_FIELD;

                    $birth =  formatDate($BIRTHDAYS['BIRTH_DATE_FORMAT'],$birth,$localization);
                    echo "<tr class='" . getClassRow($row) . "'>";
                    echo "<td>$link</td><td>$birth</td></tr>";
                }
        }

        echo "</table> </div>";
	}

    function getName($name,$ou){
        $link = "<a class=\"lightview in_link\" href=\"newwin.php?menu_marker=si_employeeview&dn=".$ou."\">".$name."</a>";
        return $link;
    }
    function getClassRow($row){
        $cssClassRow = "";
        if ($row % 2) {
       $cssClassRow = 'even';
        } else {
        $cssClassRow = 'odd';
        }
        return $cssClassRow;
    }

     function getFilterBirth(
            $CONFIG_LDAP_ATTRIBUTE,
            $BIRTHDAYS,
            $DIS_USERS_COND,
            $time
            ){
             $dates = "";
             $filter;


	        switch($BIRTHDAYS['BIRTH_DATE_FORMAT']) //Определяем шаблоны для поиска ближайших дней рождения в зависимости от формат хранения даты
    		{
    		case 'yyyy-mm-dd':
    			$DateFormat="m-d";
    			$SortType="mm-dd";
    		break;
    		case 'dd.mm.yyyy':
    			$DateFormat="d.m";
    			$SortType="dd.mm";
    		break;
    		default:
    			$DateFormat="d.m";
    			$SortType="dd.mm";
    		}


             for($i=0; $i<$BIRTHDAYS['NUM_ALARM_DAYES']; $i++)
                 {
                 $dates.="(".$CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD']."=*".date($DateFormat, $time+$i*24*60*60)."*)";
             }

             //Добавляем в фильтр условия, что бы показывались сотрудники у которых соответствует компания
             if($dates)
             {
                 //$filter = "(&(company=".$LDAP_USER['LDAP_COMPANY_FIELD'].")(|".$dates.")".$DIS_USERS_COND.")";
                 $filter = "(&(|".$dates.")".$DIS_USERS_COND.")";
             }

             return $filter;
     }

    function getUserList($ldap,$OU,$filter,$ldapListAttrs)
	{
        //$ldapTable = $ldap->getArray($OU,$filter,$ldapListAttrs);
        //$ldapConnector->getArrayUsers($OU,$filter);
         $ldapTable = $ldap->getArrayUsers($OU,$filter);


        return $ldapTable;
	}

	function formatDate($format,$dateIn,$localization){
	    $dateOut="";
	    switch($format)
        {
        case 'yyyy-mm-dd':
          preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/u",  $dateIn,$dateArr);
          break;
        case 'dd.mm.yyyy':
          preg_match("/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/u",  $dateIn,$dateArr);
          break;
        default:
          preg_match("/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/u",  $dateIn,$dateArr);
        }

        // 	Вывод в формаете "17 Июня"
        return $dateArr[1]." ". $localization->l('months')[(int) $dateArr[2]];
	}
