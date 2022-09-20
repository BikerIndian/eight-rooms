<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 23.01.2018
 * Time: 10:08
 */

namespace ru860e\rest;

abstract class Application
{

    public static function getHiddenFieldForForm()
    {
        $HiddenFields='';
        foreach($GLOBALS['CurrentVars'] AS $key=>$value)
        {
            if(! empty($value))
                $HiddenFields.="<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }
        return $HiddenFields;
    }

    public static function getSearchFilter($SearchStr, $LdapAttr)
    {
        $SearchStr=LDAP::escapeFilterValue($SearchStr);
        $Filter='(|';
        foreach($LdapAttr AS $value)
        {
            if($value!=""){
            $Filter.="(".$value."=*".$SearchStr."*)";
            }
        }
        $Filter.=")";
        return str_replace("***", "*", $Filter);
    }

    public static function getCollTitle($Title='', $Attr=array())
    {
        $th_css_class='';
        $th_content=$Title;


        if(is_array(@$Attr['sort'])) //Если по полю дожна позволятся сортировка
        {
            $th_css_class.=" sort";

            $urls['menu_marker']=$GLOBALS['menu_marker'];
            $urls['sort_field']=$Attr['sort']['field'];

            if($Attr['sort']['field'] == $Attr['sort']['sorted_field'])
            {
                if(empty($Attr['sort']['order']))
                {
                    $urls['sort_order']='desc';
                }
                else
                {
                    if($Attr['sort']['order'] == 'asc')
                        $urls['sort_order']='desc';
                    if($Attr['sort']['order'] == 'desc')
                        $urls['sort_order']='asc';
                }
            }
            else
            {
                $urls['sort_order']='asc';
            }

            if(is_array($Attr['sort']['url_vars']))
                $urls=array_merge($urls, $Attr['sort']['url_vars']);

            $th_content="<a href=\"".$_SERVER['PHP_SELF']."?".http_build_query($urls)."\" class=\"no_line\"><span>".$Title."</span></a>";
            if((! empty($Attr['sort']['order'])) && $Attr['sort']['field'] == $Attr['sort']['sorted_field'])
                $th_css_class.=" ".$Attr['sort']['order'];
        }

        $th="<th class=\"".$th_css_class."\">";
        $th.=$th_content;
        $th.="</th>";

        return $th;
    }

    /**
     * Возвращает фильтр для выборки сотрудников нужных компаний
     * @return string
     *
     */
    public static function getCompanyNameLdapFilter()
    {
        $bookmark_name=LDAP::escapeFilterValue($GLOBALS['BOOKMARK_NAME']);
        $bookmark_attr=$GLOBALS['bookmark_attr'];

        if(($bookmark_name=="*") || ( (@$_POST['form_sent']) && (@!$GLOBALS['only_bookmark']) ) )
        {

            foreach($GLOBALS['BOOKMARK_NAMES'] AS $key=>$value)
            {
                $bookmark_names=LDAP::escapeFilterValue(array_keys($value));

                if($GLOBALS['BOOKMARK_NAME_EXACT_FIT'][$bookmark_attr])
                    $filters[]="|(".$key."=".implode(")(".$key."=", $bookmark_names).")";
                else
                {
                    $filter="|(".$key."=*".implode("*)(".$key."=*", $bookmark_names)."*)";
                    $filters[]=str_replace("***", "*", $filter);
                }
            }
            $filter="(&(".implode(")(", $filters)."))";
        }
        else
        {
            if($GLOBALS['BOOKMARK_NAME_EXACT_FIT'][$bookmark_attr])
                $filter="(".$bookmark_attr."=".$bookmark_name.")";
            else
                $filter="(".$bookmark_attr."=*".$bookmark_name."*)";
        }

        //echo $filter;
        return $filter;
    }

    /**
     *  Преобразовать все атрибуты LDAP в нижний регистр
     */
    public static function makeLdapConfigAttrLowercase()
    {
        foreach($GLOBALS AS $key => $value)
        {
            if(preg_match("/^LDAP_[A-Z_]{1,}_FIELD$/", $key)){
            mb_convert_case($value , MB_CASE_LOWER);
            }
        }
    }

    public static function makeWindow($Links, $NumPosInCol=3)
    {
        $Window="<div class=\"tab\"><a href=\"\" class=\"in_link window\"></a></div>";
        $Window.="<div class=\"window hidden\">";
        $i=0;
        foreach($Links AS $key => $value)
        {
            if( !($i%$NumPosInCol) && $i!=0)
                $Window.="</ul><ul>";
            if( !($i%$NumPosInCol) && $i==0)
                $Window.="<ul>";
            $Window.="<li>".$value."</li>";
            $i++;
        }
        $Window.="</ul></div>";
        return $Window;
    }


    /**
     * @param $bookmark_attr
     * @param string $class
     * @return array
     *
     * Возвращает массив
     * первый элемент 'bookmark' - массив со ссылками вкладок, которые должны показаться в данной ситуации
     * второй элемент 'window'- массив ссылок для скрытого всплывающего окна
     */

    public static function getBookMarkLinks($bookmark_attr, $class='')
    {
        if ( array_key_exists($bookmark_attr, $GLOBALS['BOOKMARK_MAX_NUM_ITEMS']) )
            $max_items=$GLOBALS['BOOKMARK_MAX_NUM_ITEMS'][$bookmark_attr]; //Сколько вкладок максимум показывать по данному атрибуту
        else
            $max_items=0;
        $bookmark_names=$GLOBALS['BOOKMARK_NAMES']; // Массив всех вкладок

        $keys=array_keys($bookmark_names[$bookmark_attr]); //Все значения для поиска для данного атрибута
        $sizeof=sizeof($keys);
        $NumBookmaks=sizeof($bookmark_names[$bookmark_attr]);

        $select_index=array_search($GLOBALS['bookmark_name'], $keys); //Порядковый номер выбраной сейчас вкладки

        if(! $max_items) //Если в конфиге не задано числа максимально показываемых вкладок, то показывать все!
        {
            $start=0; $end=$NumBookmaks-1;
        }
        else
        {
            $delta=$select_index-$max_items+1;
            if($select_index===false) //Ессли в данной группе нет выбраной вкладке
            {
                $start=0; $end=$max_items-1;
            }
            else
            {
                if($delta<0)
                {
                    $start=0; $end=$select_index+abs($delta);
                }
                else
                {
                    $start=$delta; $end=$select_index;
                }
            }
            $end = ($end>$NumBookmaks-1) ? $NumBookmaks-1 : $end;
        }
        $BookMarksLinks=array();
        for($i=$start; $i<=$end; $i++)
        {
            if($keys[$i]==$GLOBALS['bookmark_name'])
                $BookMarksLinks[]="<div class=\"sel tab ".$class."\">".$bookmark_names[$bookmark_attr][$keys[$i]]."</div>";
            else
                $BookMarksLinks[]="<div class=\"tab ".$class."\"><a href=\"".$_SERVER['PHP_SELF']."?bookmark_name=".$keys[$i]."&bookmark_attr=".$bookmark_attr."&menu_marker=".$GLOBALS['menu_marker']."\">".$bookmark_names[$bookmark_attr][$keys[$i]]."</a></div>";
            $class='';
        }
        $i=0;
        $WindowsLinks='';
        foreach($bookmark_names[$bookmark_attr] AS $key=>$value)
        {
            if($i<$start || $i>$end)
                $WindowsLinks[]="<a href=\"".$_SERVER['PHP_SELF']."?bookmark_name=".$key."&bookmark_attr=".$bookmark_attr."&menu_marker=".$GLOBALS['menu_marker']."\">".$value."</a>";
            $i++;
        }

        return array('bookmark' => $BookMarksLinks, 'window' => $WindowsLinks);
    }


}