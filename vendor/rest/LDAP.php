<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 23.01.2018
 * Time: 10:19
 */

namespace ru860e\rest;

class LDAP
{
    private $LC;

    function __construct($Server, $User, $Password, $Port = "389")
    {
        $this->LC = ldap_connect($Server);
        ldap_set_option($this->LC, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->LC, LDAP_OPT_REFERRALS, 0);

        $this->alphabet = $GLOBALS['Alphabet'];
        $this->SizePageDividerAttr = $GLOBALS['LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD'];
        $this->SizeLimitCompatibility = $GLOBALS['LDAP_SIZE_LIMIT_COMPATIBILITY'];

        $LB = ldap_bind($this->LC, $User, $Password);
    }

    function ldap_modify($DN, $WhatChange, $NotRecode = false)
    {
        if (is_array($WhatChange)) {

            $DN = iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $DN);

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
                                $WhatChange[$key][$key1] = iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $value1);
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
                            $WhatChange[$key] = iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $value);
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

    function getValue($DN, $Attribute, $Filter = false, $NotRecode = false) //Устарела. Использовать getAttrValue()
    {
        $Attributes = array($Attribute);
        $Attributes = $this->arrtolower($Attributes);

        $DN = iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $DN);
        $Filter = iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $Filter);

        if (!$Filter) {
            $Filter = $GLOBALS['LDAP_CN_FIELD'] . "=*";
        }

        if (@$LS = ldap_search($this->LC, $DN, $Filter, $Attributes)) {
            //for ($Entries=ldap_first_entry($this->LC, $LS); $Entries!=false; $Entries=ldap_next_entry($this->LC,$Entries))
            if ($Entries = ldap_get_entries($this->LC, $LS)) {

                if (!$NotRecode)
                    return @iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[0][$Attribute][0]);
                else
                    return $Entries[0][$Attribute][0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getImage($DN, $Attribute, $File = false)    //$File=false
    {
        $Attributes = array($Attribute);
        $Attributes = $this->arrtolower($Attributes);
        $DN = iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $DN);
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
        $Entries = [];
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
        $LastVal = [];
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
                                            //$d.=" ";
                                            //$LastVal[$key-1]=str_replace($key1, $d, $LastVal[$key-1]); //!!!! Возможно деяние должно быть другим


                                            $LastVal[$key - 1] = " " . str_replace($key1, "", $LastVal[$key - 1]);


                                            break;
                                        /*default:
                                            $LastVal[$key-1]=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $LastVal[$key-1]);*/
                                    }
                                };
                            }
                        } else

                            if (isset($Entries[$i][$val][0])) {
                                $LastVal[$key] = iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$i][$val][0]);
                            }

                        if ($key > 0)
                            @$ArrSorted[$i] .= " " . $LastVal[$key - 1];
                    }
                    if (!is_array($val))
                        @$ArrSorted[$i] .= " " . $LastVal[$key];
                    //echo $ArrSorted[$i]."<br>";
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
                        @$Value = iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$AS[$i]][$ADAttributes[$j]][0]);
                        //echo $Value."<br>";
                    } else
                        $Value = iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$i][$ADAttributes[$j]][0]);

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

    static function escapeFilterValue($Value)
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
}