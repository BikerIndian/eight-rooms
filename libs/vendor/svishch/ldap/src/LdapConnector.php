<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 23.01.2022
 * Time: 10:19
 */

namespace net\svishch\php\ldap;
require_once("User.php");

class LdapConnector
{
    private $LC;
    private $CONFIG_LDAP_ATTRIBUTE;
    private $CONFIG_LDAP;
    private $CONFIG_APP;

    function __construct($Server, $User, $Password,$CONFIG,$Port = "389")
    {
        $this->LC = ldap_connect($Server);
        ldap_set_option($this->LC, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->LC, LDAP_OPT_REFERRALS, 0);

        $this->CONFIG_LDAP = $CONFIG['CONFIG_LDAP'];
        $this->CONFIG_LDAP_ATTRIBUTE = $CONFIG['CONFIG_LDAP_ATTRIBUTE'];
        $this->CONFIG_APP = $CONFIG['CONFIG_APP'];

        $this->SizePageDividerAttr      = $this->CONFIG_LDAP_ATTRIBUTE['LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD'];
        $this->SizeLimitCompatibility   = $this->CONFIG_LDAP['LDAP_SIZE_LIMIT_COMPATIBILITY'] ;

        $LB = ldap_bind($this->LC, $User, $Password);
    }

    function ldap_modify($DN, $WhatChange, $NotRecode = false)
    {
        if (is_array($WhatChange)) {

            $DN = iconv($this->CONFIG_APP['CHARSET_APP'], $this->CONFIG_APP['CHARSET_DATA'], $DN);

            foreach ($WhatChange as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key1 => $value1) {
                        if ($value1 == "") {
                            unset($WhatChange[$key][$key1]);
                            //$KeyForDel[]=$key;
                        } else {
                            if ($NotRecode)
                                $WhatChange[$key][$key1] = $value1;
                            else
                                $WhatChange[$key][$key1] = iconv($this->CONFIG_APP['CHARSET_APP'], $this->CONFIG_APP['CHARSET_DATA'], $value1);
                        }
                    }
                } else {
                    if ($WhatChange[$key] == "") {
                        unset($WhatChange[$key]);
                        $KeyForDel[] = $key;
                    } else {
                        if ($NotRecode)
                            $WhatChange[$key] = $value;
                        else
                            $WhatChange[$key] = iconv($this->CONFIG_APP['CHARSET_APP'], $this->CONFIG_APP['CHARSET_DATA'], $value);
                    }
                }

            }

            if (is_array(@$KeyForDel)) {
                $LS = ldap_search($this->LC, $DN, "name=*", $KeyForDel);
                $Entries = ldap_get_entries($this->LC, $LS);

                foreach ($KeyForDel as $key => $value) {
                    if (@$Entries[0][$value][0] != "")
                        $WhatDel[$value] = $Entries[0][$value][0];
                }
                if (is_array(@$WhatDel))
                    ldap_mod_del($this->LC, $DN, $WhatDel);
            }

            ldap_modify($this->LC, $DN, $WhatChange);

        }

    }

    function getEmptyFilter()
    {
        return "name=*";
    }

    function getAttrValue($DN, $Attribute, $Filter = false)
    {
        $Attributes = array($Attribute);

        if (!$Filter)
            $Filter = self::getEmptyFilter();

        if (@$LS = ldap_search($this->LC, $DN, $Filter, $Attributes)) {
            if ($Entries = ldap_get_entries($this->LC, $LS)) {
                unset($Entries[0][$Attribute]['count']);
                return @$Entries[0][$Attribute];
            } else
                return false;
        } else
            return false;
    }

    function getUserByHexGui($dn,$uid)
    {
      $guid = hex2bin($uid);
      $filter = "(&(objectCategory=person)(objectClass=user)(objectGUID=".$guid."))";
      $ls = ldap_search($this->LC, $dn, $filter, $this->getUserAttributes());
      $entries = ldap_get_entries($this->LC, $ls);
      return $this->parseUser($entries[0]);
    }

    function getUser($dn,$userName)
    {
      $filter = "(&(objectCategory=person)(CN=".$userName."))";
      $ls = ldap_search($this->LC, $dn, $filter, $this->getUserAttributes());
      $entries = ldap_get_entries($this->LC, $ls);
      return $this->parseUser($entries[0]);
    }

    /** Получить пользователя по dn **/
    function getUserForDn($dn)
    {
      $filter = "(&(objectCategory=person)(CN=*))";
      $ls = ldap_search($this->LC, $dn, $filter, $this->getUserAttributes());
      $entries = ldap_get_entries($this->LC, $ls);
      return $this->parseUser($entries[0]);
    }


    /** Список подчененных сотрудников **/
    function getSubordinatesByAttribute($dn)
    {
    $dn = str_replace( "(", '\28',$dn);
    $dn = str_replace( ")", '\29',$dn);
     $user = $this->getUserForDn($dn);
     $filter = "(&(objectCategory=person)(objectClass=user)(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(manager=".$dn."))";
     $usersArr = $this->getArrayUsers($this->CONFIG_LDAP['OU'],  $filter);
     return $usersArr;
    }

    /** Список пользователей **/
    function getArrayUsers($dn,$filter = false)
    {
        if(!$filter){
          $filter = "(&(objectCategory=person))";
        }

        $ls = ldap_search($this->LC, $dn, $filter, $this->getUserAttributes());
        $entries = ldap_get_entries($this->LC, $ls);
        $length = count($entries);
        $arrayUsers = null;
        for ($i = 0; $i < $length-1; $i++) {
            $arrayUsers[$i]=$this->parseUser($entries[$i]);
        }

        return $arrayUsers;
    }

    function isAccessUser($login, $password, $dn, $filter)
    {

       $bind = @ldap_bind($this->LC, $_POST['login'], $_POST['password']);

       if ($bind) {
       $arrayUsers = $this->getArrayUsers($dn,$filter);

          $length = count($arrayUsers);

             for ($i = 0; $i < $length-1; $i++) {
               $loginAD = $arrayUsers[$i]->LDAP_USERPRINCIPALNAME_FIELD;
                   if($login == $loginAD){
                   return $arrayUsers[$i];
                   }
               }
       }

        return null;
    }

    private function getUserAttributes(){
              $LDAP_GUID_FIELD = "objectguid";
              $attributes = array(
                      $LDAP_GUID_FIELD,
                      $this->CONFIG_LDAP_ATTRIBUTE['DISPLAY_NAME_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_INTERNAL_PHONE_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_CITY_PHONE_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_ST_DATE_VACATION_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_CELL_PHONE_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_MANAGER_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_COMPUTER_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_DEPUTY_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_USERPRINCIPALNAME_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD'],
                      $this->CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD'],
                      );
     return $attributes;
    }

    private function parseUser($entries){

         // Если пустой то возврат
         if (!$entries) {
            return new User();
         }

         $LDAP_GUID_FIELD = "objectguid";
         $user = new User();
         $user->LDAP_GUID_FIELD                 = bin2hex($entries[$LDAP_GUID_FIELD][0]);
         $user->DISPLAY_NAME_FIELD              = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['DISPLAY_NAME_FIELD'],$entries);
         $user->LDAP_DISTINGUISHEDNAME_FIELD    = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD'],$entries);
         $user->LDAP_NAME_FIELD                 = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD'],$entries);
         $user->LDAP_MAIL_FIELD                 = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD'],$entries);
         $user->LDAP_INTERNAL_PHONE_FIELD       = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_INTERNAL_PHONE_FIELD'],$entries);
         $user->LDAP_CITY_PHONE_FIELD           = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_CITY_PHONE_FIELD'],$entries);
         $user->LDAP_ST_DATE_VACATION_FIELD     = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_ST_DATE_VACATION_FIELD'],$entries);
         $user->LDAP_END_DATE_VACATION_FIELD    = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD'],$entries);
         $user->LDAP_TITLE_FIELD                = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD'],$entries);
         $user->LDAP_DEPARTMENT_FIELD           = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD'],$entries);
         $user->LDAP_CELL_PHONE_FIELD           = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_CELL_PHONE_FIELD'],$entries);
         $user->LDAP_MANAGER_FIELD              = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_MANAGER_FIELD'],$entries);
         $user->LDAP_COMPUTER_FIELD             = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_COMPUTER_FIELD'],$entries);
         $user->LDAP_DEPUTY_FIELD               = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_DEPUTY_FIELD'],$entries);
         $user->LDAP_USERPRINCIPALNAME_FIELD    = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_USERPRINCIPALNAME_FIELD'],$entries);
         $user->LDAP_ROOM_NUMBER_FIELD          = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD'],$entries);
         $user->LDAP_BIRTH_FIELD                = $this->setStr($this->CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD'],$entries);

         return $user;
    }

    function getImage($DN, $Attribute, $File = false)    //$File=false
    {
        $Attributes = array($Attribute);
        $Attributes = $this->arrtolower($Attributes);
        $DN = iconv($this->CONFIG_APP['CHARSET_APP'], $this->CONFIG_APP['CHARSET_DATA'], $DN);
        $LS = ldap_search($this->LC, $DN, "name=*", $Attributes);

        if ($Entries = ldap_get_entries($this->LC, $LS)) {

            if (@$Entries[0][$Attribute][0]) {
                if ($File) {
                    //if (file_exists($strFile)) unlink($strFile);
                    $handle = @fopen($File, 'wb');
                    @fwrite($handle, $Entries[0][$Attribute][0]);
                    @fclose($handle);
                    return $File;
                } else
                    return "data:image/jpeg;base64," . base64_encode($Entries[0][$Attribute][0]);
            } else
                return false;
        } else {
            return false;
        }
    }

    public function ldap_search($BaseDN, $Filter, $Attributes)
    {
        $LS = ldap_search($this->LC, $BaseDN, $Filter, $Attributes);
        $Entries = ldap_get_entries($this->LC, $LS);
        return $Entries;
    }

    public function getEntriesWithoutSizeLimit($BaseDN, $Filter, $Attributes, $WithoutSizeLimit = false)
    {
        $Entries = array();
        $count = "";

        if ($this->SizeLimitCompatibility && !$WithoutSizeLimit) {
            $Attributes[] = 'displayname';
            foreach ($this->alphabet AS $key => $value) {
                $MofifiedFilter = substr_replace($Filter, "(&(" . $this->SizePageDividerAttr . "=" . $value . "*)", 0, 2);
                $LS = ldap_search($this->LC, $BaseDN, $MofifiedFilter, $Attributes);

                if (is_array($Entries)) {
                    $Entries = array_merge($Entries, ldap_get_entries($this->LC, $LS));
                    $count += $Entries['count'];
                } else {
                    $Entries = ldap_get_entries($this->LC, $LS);
                    $count = $Entries['count'];
                }
            }

            $Entries['count'] = $count;
        } else {
            $LS = ldap_search($this->LC, $BaseDN, $Filter, $Attributes);
            $Entries = ldap_get_entries($this->LC, $LS);

        }

        /*for($i=0; $i<$Entries[count]; $i++)
            {
            $sss = explode(" ", $Entries[$i]['displayname'][0]);
            $Entries[$i]['displayname'][0] = $sss[1]." ".$str = mb_substr($sss[2], 0, 1, 'UTF-8').". ".$sss[0];
            }*/

        return $Entries;
    }

    function getArray($BaseDN, $Filter = false, $ADAttributes, $Sort = array('name'), $SortType = "ASC", $WithoutSizeLimit = false)
    {
        $LastVal = array();
        if (!$Filter)
            $Filter = self::getEmptyFilter();

        $ADAttributes = $this->arrtolower($ADAttributes);
        $SizeOf = sizeof($ADAttributes);

        if ($Entries = self::getEntriesWithoutSizeLimit($BaseDN, $Filter, $ADAttributes, $WithoutSizeLimit)) {
            //Сортировка
            //-----------------------------------------------------------------------------
            if (is_array($Sort)) {
                for ($i = 0, $d = 0; $i < $Entries['count']; $i++) {
                    foreach ($Sort AS $key => $val) {
                        if (is_array($val)) {
                            $d = "";
                            foreach ($val AS $key1 => $val1) {
                                if (is_array($val1)) {
                                    foreach ($val1 AS $key2 => $val2) {

                                        switch ($val2) {
                                            case 'order_replace':
                                                $d .= $key2;
                                                if (isset($LastVal[$key - 1])) {
                                                    $LastVal[$key - 1] = str_replace($key1, $d, $LastVal[$key - 1]); //!!!! Возможно деяние должно быть другим
                                                }
                                                break;
                                        }
                                    }
                                }

                                if (isset($LastVal[$key - 1])) {
                                    switch ($val1) {
                                        case 'ad_def_full_name':
                                            $LastVal[$key - 1] = preg_replace("/([ёA-zА-я-]+[\s]{1}[ёA-zА-я]{1}.)[\s]{1}([ёA-zА-я-]+)/u", "\\2 \\1", $LastVal[$key - 1]);
                                            break;
                                        case 'order_replace':
                                            $LastVal[$key - 1] = " " . str_replace($key1, "", $LastVal[$key - 1]);
                                            break;
                                    }
                                };
                            }
                        } else

                            if (isset($Entries[$i][$val][0])) {
                                $LastVal[$key] = iconv($this->CONFIG_APP['CHARSET_DATA'], $this->CONFIG_APP['CHARSET_APP'], $Entries[$i][$val][0]);
                            }

                        if ($key > 0)
                            @$ArrSorted[$i] .= " " . $LastVal[$key - 1];
                    }
                    if (!is_array($val))
                        @$ArrSorted[$i] .= " " . $LastVal[$key];
                }


                if (is_array(@$ArrSorted)) {
                    asort($ArrSorted);
                    $AS = array_keys($ArrSorted);
                    if (strtolower($SortType) == "desc") {
                        $AS = array_reverse($AS);
                    }
                }
                /*foreach($ArrSorted AS $key=>$value)
                    {
                    echo "".$value."<br/>";
                    }*/
            }
            //-----------------------------------------------------------------------------


            for ($i = 0; $i < @$Entries[count]; $i++) {
                for ($j = 0; $j < $SizeOf; $j++) {
                    if (is_array($Sort)) {
                        @$Value = iconv($this->CONFIG_APP['CHARSET_DATA'], $this->CONFIG_APP['CHARSET_APP'], $Entries[$AS[$i]][$ADAttributes[$j]][0]);
                        //echo $Value."<br>";
                    } else
                        $Value = iconv($this->CONFIG_APP['CHARSET_DATA'], $this->CONFIG_APP['CHARSET_APP'], $Entries[$i][$ADAttributes[$j]][0]);

                    $RA[$ADAttributes[$j]][$i] = $Value;
                }
                unset($Value);
                //echo "<br>";

            }
        }

        if (@is_array($RA)) {
            return $RA;
        } else {
            return false;
        }
    }


    function removeValues($dn, $Attributes)
    {
        ldap_mod_del($this->LC, $dn, $Attributes);

    }

    function addValuesToEnd($dn, $Attributes)
    {
        @ldap_mod_add($this->LC, $dn, $Attributes);
        //$LS=ldap_search($this->LC, $dn, "name=*", array_unique(array_keys($Attributes)));
        //$Entries=ldap_get_entries($this->LC, $LS);
    }

    function escapeFilterValue($Value)
    {
        if (is_array($Value)) {
            foreach ($Value AS $key => $val) {
                $Value[$key] = str_replace(array('\\', '(', ')'), array('\5c', '\28', '\29'), $val);
            }
        } else {
            $Value = str_replace(array('\\', '(', ')'), array('\5c', '\28', '\29'), $Value);
        }

        return $Value;
    }

    function arrtolower($arr)
    {
        if (!is_array($arr)) {
            $arr = strtolower($arr);
        } else {
            foreach ($arr as $key => $val) $arr[$key] = $this->arrtolower($val);
        }
        return $arr;
    }

    function setStr($key,$arr){

    if(array_key_exists($key, $arr))
    {
    $str = $arr[$key][0];
    }else{
    $str = " ";
    }

    return $str;
    }
}


