<?php
use ru860e\rest\LDAPTable;
use ru860e\rest\LDAP;
use ru860e\rest\Application;

//Выввод ближайших дней рождений

if($BIRTHDAYS['$NEAR_BIRTHDAYS']) {

    $ldapListAttrs = array(
                    $CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD'],
                    $CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD'],
                    $CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']
                    );

    $filterBirth = getFilterBirth(
                $CONFIG_LDAP_ATTRIBUTE,
                $BIRTHDAYS,
                $DIS_USERS_COND,
                time()
                );

    $userList = getUserList($ldap,$OU,$filterBirth,$ldapListAttrs);

    printBirth($L,$CONFIG_LDAP_ATTRIBUTE,$BIRTHDAYS,$userList);

}

    function printBirth($L,$CONFIG_LDAP_ATTRIBUTE,$BIRTHDAYS,$userList)
	{

    //echo print_r($userList);
        echo"<div class=\"heads\">
            <fieldset class=\"birthdays\">
            <legend>".$L->l('nearest')." ".$BIRTHDAYS['NUM_ALARM_DAYES']." ".$L->l('they_have_birthdays').":</legend>";
        echo "<table class='sqltable' cellpadding='4'>";

	    if (is_array($userList)) {
                $row = 0;
                foreach ($userList[$CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD']] AS $key => $value) {
                    $row++;
                    $name =  $userList[$CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD']][$key];
                    $ou = $userList[$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']][$key];

                    $link = getName($name,$ou);
                    $birth =  $userList[$CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD']][$key];
                    $birth =  formatDate($BIRTHDAYS['BIRTH_DATE_FORMAT'],$birth);
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
                 $filter = "(&(company=".$CONFIG_LDAP_ATTRIBUTE['LDAP_COMPANY_FIELD'].")(|".$dates.")".$DIS_USERS_COND.")";
             }
             return $filter;
     }

    function getUserList($ldap,$OU,$filter,$ldapListAttrs)
	{
        $ldapTable = $ldap->getArray($OU,$filter,$ldapListAttrs);
        return $ldapTable;
	}

	function formatDate($format,$dateIn){
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
        return $dateArr[1]." ".$GLOBALS['MONTHS'][(int) $dateArr[2]];
	}
