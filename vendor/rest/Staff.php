<?php
namespace ru860e\rest;

abstract class Staff
{

	public static function showComputerName($Login)
		{
		if(in_array($Login, $GLOBALS['ADMIN_LOGINS']) && $GLOBALS['SHOW_COMPUTER_FIELD'])
		{return true;}
		else {return false;}
		}

	public static function getSurname($value)
		{
		if($GLOBALS['USE_DISPLAY_NAME'])
			{
			$fio = explode(" ", $value);
			if(preg_match("/([ёA-zА-я-]+[\s]{1}[ёA-zА-я]{1}.)[\s]{1}([ёA-zА-я-]+)/u", $value))
				return $fio[2];
			else
				return $fio[0];
			}
		else
			{
			return $value; //Не правильно
			}
		}

	public static function makeNameUrlFromDn($DN, $Title="")
		{
		if($GLOBALS['USE_DISPLAY_NAME'])
			{
			$DN=preg_replace("/([ёA-zА-я-\.-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\3\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'>\\1</span> \\2</a>", $Title.$DN);
			$DN=preg_replace("/([ёA-zА-я-\.-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zА-я-]+)(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\3\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'>\\2</span> \\1</a>", $DN);
			$DN=preg_replace("/([ёA-zA-я0-9№\s\.-]{1,})(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\2\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'> \\1</span></a>", $DN);
			$DN=preg_replace("/^CN=([ёA-zA-я0-9\s\.-]{1,})(.*)$/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\0\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'> \\1</span></a>", $DN);
			}
		else
			{
			$DN=preg_replace("/^[A-Za-z]+=*([ёА-яA-z0-9\s-.]+),[\S\s]+$/eu", "'<a href=\"newwin.php?menu_marker=si_employeeview&dn='.'\\0'.'\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\">___\\1</a>'", $DN);
			$DN=preg_replace("/___([ёA-zА-я-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)/u", "<span class='surname'>\\1</span> \\2", $DN);
			//Для формата Имя О. Фамилия
			$DN=preg_replace("/___([ёA-zА-я-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zА-я-]+)/u", "<span class='surname'>\\2</span> \\1", $DN);	
			}	
		return $DN;
		}
		
	public static function makeMailUrl($Mail)
		{
		if($Mail)
			return preg_replace("/([A-z0-9_\.\-]{1,20}@[A-z0-9\.\-]{1,20}\.[A-z]{2,4})/u", "<a href='mailto:\\1' class='in_link'>\\1</a>", $Mail);
		else
			return "x";
		}

	public static function makePlainText($Var)
		{
		if($Var)
			return $Var;
		else
			return "x";
		}
		

	public static function makeDeputy($DN, $Title='')
		{
		if($GLOBALS['USE_DISPLAY_NAME'])
			return self::makeNameUrlFromDn($DN, $Title);
		else
			return self::makeNameUrlFromDn($DN);
		}

	public static function printDeputyInList($DN, $Title='')
		{
		if($GLOBALS['SHOW_DEPUTY'] && $DN && $GLOBALS['SHOW_DEPUTY_IN_LISTS'])
			echo "<span class=\"unimportant\"> ".$GLOBALS['L']->l("deputy")." </span><span class=\"deputy\">".Staff::makeNameUrlFromDn($DN, $Title)."</span>";
		}

	// Функции форматирования телефонных номеров
	// ===============================================================================================================
	public static function makeInternalPhone($Val, $Link=true)
	{
		$phone_attr=get_phone_attr($Val);
		if (empty($Val)) return 'x';
		if($Link)
		{
			$call_via_ip = ($GLOBALS['ENABLE_CALL_VIA_IP'] && isset($_COOKIE['dn']))?"call_via_ip":"";
			if(@$GLOBALS['FORMAT_INTERNAL_PHONE'])
			{		
				$Val="<a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$phone_attr['clear_phone']."\" data-phone-for-ip-call=\"".$phone_attr['clear_phone']."\" class=\"in_link int_phone ".$call_via_ip."\">".$phone_attr['format_phone']."</a>";
			}	
			else
				$Val="<a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$Val."\" data-phone-for-ip-call=\"".$Val."\"  class=\"in_link int_phone ".$call_via_ip."\">".$Val."</a>";
		}
		else
			{
			if(@$GLOBALS['FORMAT_INTERNAL_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}
	// ---------------------------------------------------------------------------------------------------------------		
	public static function makeCityPhone($Val, $Link=true)
	{
		$phone_attr=get_phone_attr($Val);
		if (empty($Val)) return 'x';
		if($Link)
		{
			if($GLOBALS['FORMAT_CITY_PHONE'])
			{		
				if($GLOBALS['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$phone_title="title=\"".$phone_attr['provider_desc']."\"";
				else
					$phone_title="title=\"\"";
				$Val="<a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$phone_attr['clear_phone']."\" class=\"in_link cityphone\" ".$phone_title.">".$phone_attr['format_phone']."</a>";
			}
			else
				$Val="<a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$Val."\" class=\"in_link cityphone\">".$Val."</a>";
		}
		else
			{
			if(@$GLOBALS['FORMAT_CITY_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		return $Val;
	}	
	// ---------------------------------------------------------------------------------------------------------------	
	public static function makeCellPhone($Val, $Link=true)
	{
		$phone_attr=get_phone_attr($Val);
		if (empty($Val)) return 'x';
		if($Link)
			{
			$call_via_ip = ($GLOBALS['ENABLE_CALL_VIA_IP'] && isset($_COOKIE['dn']))?"call_via_ip":"";

			if($GLOBALS['FORMAT_CELL_PHONE'])
				{
				if($GLOBALS['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$phone_title="title=\"".$phone_attr['provider_desc']."\"";

				$phone_for_call_via_ip = str_replace ("+7" , $GLOBALS['CALL_VIA_IP_CHANGE_PLUS_AND_SEVEN'], $phone_attr['clear_phone']);
				@$Val="<a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$phone_attr['clear_phone']."\" data-phone-for-ip-call=\"".$phone_for_call_via_ip."\" class=\"in_link cell_phone ".$call_via_ip."\" ".$phone_title.">".$phone_attr['format_phone']."</a>";
				}
			else
				{	
				$phone_for_call_via_ip = str_replace ("+7" , $GLOBALS['CALL_VIA_IP_CHANGE_PLUS_AND_SEVEN'], $Val);	
				$Val="<a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$Val."\" data-phone-for-ip-call=\"".$phone_for_call_via_ip."\" class=\"in_link cell_phone ".$call_via_ip."\">".$Val."</a>";
				}
			}
		else
			{
			if(@$GLOBALS['FORMAT_CELL_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}	
	// ---------------------------------------------------------------------------------------------------------------	
	public static function makeHomePhone($Val, $Link=true)
	{
		$phone_attr=get_phone_attr($Val);
		if (empty($Val)) return 'x';
		if($Link)
		{
			if($GLOBALS['FORMAT_HOME_PHONE'])
			{
				if($GLOBALS['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$Val="<acronym title =\"".$phone_attr['provider_desc']."\"><a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$phone_attr['clear_phone']."\" class=\"in_link homephone\">".$phone_attr['format_phone']."</a></acronym>";
				else
					$Val="<a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$phone_attr['clear_phone']."\" class=\"in_link homephone\">".$phone_attr['format_phone']."</a>";
			}
			else
				$Val="<a href=\"".@$GLOBALS['PHONE_LINK_TYPE'].$Val."\" class=\"in_link homephone\">".$Val."</a>";
		}
		else
			{
			if(@$GLOBALS['FORMAT_HOME_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}
	// ===============================================================================================================
		
	public static function makeComputerName($Val)
		{
		if($Val)
			return $Val;
		else
			return "x";	
		}

	public static function makeTitle($Val)
		{
		if($Val)
			return preg_replace('/(?:\"([^\"]+)\")/u', '&laquo;\\1&raquo;', $Val);
		else
			return "x";	
		}
		
	public static function makeDepartment($Val, $MakeAdd=false)
		{
		if($Val)
			{
			$return="<span class=\"dep_name\">".preg_replace('/(?:\"([^\"]+)\")/u', '&laquo;\\1&raquo;', str_replace("\\", " &rarr; ", $Val))."</span>";	
			if($MakeAdd)
				@$return.=$GLOBALS['DEP_ADD'][$Val];			
			return $return;
			}
		else
			return "x";	
		}

	public static function checkInVacation($StDate, $EndDate)
		{
		if($StDate&&$EndDate)
			{
			$time=time();
			if((Time::getTimeOfDMYHI($EndDate, $GLOBALS['VAC_DATE_FORMAT'])>=$time)&&(Time::getTimeOfDMYHI($StDate, $GLOBALS['VAC_DATE_FORMAT'])<=$time))
				return true;
			else
				return false;
			}
		else
			return false;
		}

	public static function getVacationState($StDate, $EndDate)
		{
		if($StDate&&$EndDate)
			{
			$end_time=Time::getTimeOfDMYHI($EndDate, $GLOBALS['VAC_DATE_FORMAT']);
			$start_time=Time::getTimeOfDMYHI($StDate, $GLOBALS['VAC_DATE_FORMAT']);
			$time=time();
			if(($end_time>=$time)&&($start_time<=$time))
				return 0;	// в отпуске
			else 
				{
				if($start_time>$time)
					return 1;	// отпуск еще предстоит
				else
					return -1;	// отпуск закончился
				}

			}
		}

	public static function checkShowVacOnCurrentPage($StDate, $EndDate)
		{
		if($StDate&&$EndDate)
			{
			$VacationState=self::getVacationState($StDate, $EndDate);
			if(
				(
				(($VacationState == 0) && $GLOBALS['SHOW_CURRENT_VAC'][$GLOBALS['menu_marker']]) 
				|| (($VacationState > 0) && $GLOBALS['SHOW_NEXT_VAC'][$GLOBALS['menu_marker']])
				|| (($VacationState < 0) && $GLOBALS['SHOW_PREV_VAC'][$GLOBALS['menu_marker']])
				) && $GLOBALS['VACATION']
			  )
				return true;
			else
				return false;	
			}
		}

	public static function printVacOnCurrentPage($StDate, $EndDate)
		{
		$VacationState=self::getVacationState($StDate, $EndDate);
		if(self::checkShowVacOnCurrentPage($StDate, $EndDate))
			{
			if($VacationState===0)
				{
				$class='alarm';
				$vac_title=$GLOBALS['L']->l("in_vacation_until");
				$vac_period=Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
				if($GLOBALS['menu_marker']=='si_employeeview')
					{
					$vac_title="<h6 class=\"alarm\">".$GLOBALS['L']->l("in_vacation").":</h6>";
					$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
					}
				}
			if($VacationState>0)
				{
				$class='next_vac';
				$vac_title="Ближайший отпуск: ";
				$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);		
				if($GLOBALS['menu_marker']=='si_employeeview')
					$vac_title="<h6 class=\"".$class."\">".$vac_title."</h6>";					
				}
			if($VacationState<0)
				{
				$class='prev_vac';
				$vac_title="Прошедший отпуск: ";
				$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
				if($GLOBALS['menu_marker']=='si_employeeview')
					$vac_title="<h6 class=\"".$class."\">".$vac_title."</h6>";	
				}
						

			if($GLOBALS['menu_marker']=='si_alph_staff_list' || $GLOBALS['menu_marker']=='si_dep_staff_list' || $GLOBALS['menu_marker']=='si_stafflist' )
				echo"<span class=\"".$class."\">".$vac_title.$vac_period."</span>";
			if($GLOBALS['menu_marker']=='si_employeeview')
				echo"<div class=\"birthday\">".$vac_title.$vac_period."</div>";
			}
		}

	public static function makeAvatar($dn)
	{
		if($GLOBALS['DIRECT_PHOTO'])
			$Image=$GLOBALS['ldap']->getImage($dn, $GLOBALS['LDAP_AVATAR_FIELD']);
		else
			$Image=$GLOBALS['ldap']->getImage($dn, $GLOBALS['LDAP_AVATAR_FIELD'], $GLOBALS['PHOTO_DIR']."/avatar_".md5($dn).".jpg");

		if($Image)
			return "<div class=\"avatar\"><img src=\"".$Image."\" height=\"".$GLOBALS['THUMBNAIL_PHOTO_MAX_HEIGHT']."\" width=\"".$GLOBALS['THUMBNAIL_PHOTO_MAX_WIDTH']."\" /></div>";	
		else
			{
			if($GLOBALS['SHOW_EMPTY_AVATAR'])
				return "<div class=\"avatar\"><img src=\"./skins/".$GLOBALS['CURRENT_SKIN']."/images/user_avatar.png\" alt=\"user avatar\" height=\"32\" width=\"32\" /></div>";	
			}
	}


	public static function getNumStaffTableColls()
		{
		$num=5;
		
		if($GLOBALS['menu_marker']=='si_export_pdf_alphabet' || $GLOBALS['menu_marker']=='si_export_pdf_department')
			$num=5;
		if($GLOBALS['menu_marker']=='si_alph_staff_list' || $GLOBALS['menu_marker']=='si_dep_staff_list')
			{
			$num=5;
			if( self::showComputerName($GLOBALS['Login']))
				$num++;		
			if($GLOBALS['FAVOURITE_CONTACTS'] && !empty($_COOKIE['dn']))
				$num++;	

			}

		if(! $GLOBALS['HIDE_CITY_PHONE_FIELD'])
			$num++;
		if(! $GLOBALS['HIDE_CELL_PHONE_FIELD'])
			$num++;
		if(! $GLOBALS['HIDE_ROOM_NUMBER'])
			$num++;


		if(empty($_COOKIE['dn']) && $GLOBALS['ENABLE_DANGEROUS_AUTH'])
			$num++;
		if($GLOBALS['XMPP_ENABLE'] && $GLOBALS['XMPP_MESSAGE_LISTS_ENABLE'] && !empty($_COOKIE['dn']))
			$num++;

		return $num;

		}
	public static function highlightSearchResult($Str, $SearchStr)
		{
		//echo "/((?:<[^>]+>)*[^<]*)(".$SearchStr.")([^<]*(?:<[^>]+>)*)/";
		$Str=preg_replace ("/(>[^>]*)(".$SearchStr.")([^<]*<)/i", "\\1<span class=\"found\">\\2</span>\\3", $Str);
		$Str=preg_replace ("/^([^>]*)(".$SearchStr.")([^<]*)$/i", "\\1<span class=\"found\">\\2</span>\\3", $Str);
		return $Str;
		}

	//Выводит строку таблицы с информацией по определенному сотруднику
	public static function printUserTableRow($Staff, $key, $Vars)
		{
		$StDate=$Staff[$GLOBALS['LDAP_ST_DATE_VACATION_FIELD']][$key]; 
		$EndDate=$Staff[$GLOBALS['LDAP_END_DATE_VACATION_FIELD']][$key];
		$VacationState=self::getVacationState($StDate, $EndDate);	// проверка: в каком состоянии отпуск?
		($VacationState===0) ? $tag="del" : $tag="span";	// в зависимости от этого применяем разные стили
				
		// Строки таблицы
		//-------------------------------------------------------------------------------------------------------------
		$data_parent_id=($Vars['data_parent_id']) ? "data-parent-id=".md5($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]) : '';
		$id=($Vars['id']) ? "id=".md5($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]) : '';
		echo"<tr class=\"".$Vars['row_css']."\" ".$id." ".$data_parent_id.">";
		echo "<td>";
		self::printVacOnCurrentPage($StDate, $EndDate);		
		if($GLOBALS['THUMBNAIL_PHOTO_VIS'])	
			echo self::makeAvatar($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]);
		if( (self::checkInVacation($StDate, $EndDate) && $GLOBALS['BIND_DEPUTY_AND_VACATION']) || !$GLOBALS['BIND_DEPUTY_AND_VACATION'])	//
			self::printDeputyInList($Staff[$GLOBALS['LDAP_DEPUTY_FIELD']][$key], $Vars['ldap_conection']->getValue($Staff[$GLOBALS['LDAP_DEPUTY_FIELD']][$key], $GLOBALS['DISPLAY_NAME_FIELD']));

		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo self::makeNameUrlFromDn($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key], $Staff[$Vars['display_name']][$key]); //Делаем ссылку на полную информацию о сотруднике
		else
			echo self::highlightSearchResult(self::makeNameUrlFromDn($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key], $Staff[$Vars['display_name']][$key]), $Vars['search_str']); //Делаем ссылку на полную информацию о сотруднике

		echo "</td>";
		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo "<td>".self::makeTitle($Staff[$GLOBALS['LDAP_TITLE_FIELD']][$key])."</td>"; //Выводим должность
		else
			echo "<td>".self::highlightSearchResult(self::makeTitle($Staff[$GLOBALS['LDAP_TITLE_FIELD']][$key]), $Vars['search_str'])."</td>"; //Выводим должность

		if(isset($Vars['locked_date']))
			echo "<td>".Time::modifyDateFormat(self::makeTitle($Staff[$GLOBALS['LDAP_CHANGED_DATE_FIELD']][$key]), $GLOBALS['LDAP_CHANGED_DATE_FORMAT'], "yyyy-mm-dd")."</td>"; //Выводим должность


		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo "<td>".self::makeMailUrl($Staff[$GLOBALS['LDAP_MAIL_FIELD']][$key])."</td>"; //Выводим почту
		else
			echo "<td>".self::highlightSearchResult(self::makeMailUrl($Staff[$GLOBALS['LDAP_MAIL_FIELD']][$key]), $Vars['search_str'])."</td>"; 

		if(!$GLOBALS['HIDE_ROOM_NUMBER'] && isset($Staff[$GLOBALS['LDAP_ROOM_NUMBER_FIELD']][$key]))
			{
			if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
				echo "<td>".self::makePlainText($Staff[$GLOBALS['LDAP_ROOM_NUMBER_FIELD']][$key])."</td>"; //Выводим сотовый
			else
				echo "<td>".self::highlightSearchResult(self::makePlainText($Staff[$GLOBALS['LDAP_ROOM_NUMBER_FIELD']][$key]), $Vars['search_str'])."</td>"; //Делаем ссылку на полную информацию о сотруднике
			}

		echo "<td><".$tag.">".self::makeInternalPhone($Staff[$GLOBALS['LDAP_INTERNAL_PHONE_FIELD']][$key],$GLOBALS['ENABLE_CALL_VIA_IP'])."</".$tag."></td>"; //Выводим внутренний

		if(!$GLOBALS['HIDE_CITY_PHONE_FIELD'])
			{
			echo "<td><".$tag.">".self::makeCityPhone($Staff[$GLOBALS['LDAP_CITY_PHONE_FIELD']][$key],$GLOBALS['ENABLE_CALL_VIA_IP'])."</".$tag."></td>"; //Выводим городской
			}

		if(!$GLOBALS['HIDE_CELL_PHONE_FIELD'])
			{
			if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты	
				echo "<td>".self::makeCellPhone($Staff[$GLOBALS['LDAP_CELL_PHONE_FIELD']][$key],$GLOBALS['ENABLE_CALL_VIA_IP'])."</td>"; //Выводим сотовый
			else
				echo "<td>".self::highlightSearchResult(self::makeCellPhone($Staff[$GLOBALS['LDAP_CELL_PHONE_FIELD']][$key],$GLOBALS['ENABLE_CALL_VIA_IP']), $Vars['search_str'])."</td>"; //Делаем ссылку на полную информацию о сотруднике
			}
        // Домашний
        if(!$GLOBALS['HIDE_HOME_PHONE_FIELD'] && isset($Staff[$GLOBALS['LDAP_HOMEPHONE_FIELD']][$key])){
        	echo "<td><".$tag.">".self::makeHomePhone($Staff[$GLOBALS['LDAP_HOMEPHONE_FIELD']][$key],$GLOBALS['ENABLE_CALL_VIA_IP'])."</".$tag."></td>"; //Выводим домашний
        }

		if(self::showComputerName($Vars['current_login'])) //Если сотрудник является администратором справочника
			{
			if (isset($Staff[$GLOBALS['LDAP_COMPUTER_FIELD']][$key])){
			    if(empty($Vars['search_str']) ) //Если не велся поиск, то не подсвечивавем результаты
				    echo "<td>".self::makeComputerName($Staff[$GLOBALS['LDAP_COMPUTER_FIELD']][$key])."</td>"; //Выводим имя компьютера

			    else
				    echo "<td>".self::highlightSearchResult(self::makeComputerName($Staff[$GLOBALS['LDAP_COMPUTER_FIELD']][$key]), $Vars['search_str'])."</td>"; //Выводим имя компьютера
			    }
			}
		if( @$Staff[$GLOBALS['LDAP_CREATED_DATE_FIELD']][$key] ) 
			echo "<td>".Time::getHandyDateOfDMYHI($Staff[$GLOBALS['LDAP_CREATED_DATE_FIELD']][$key], $GLOBALS['LDAP_CREATED_DATE_FORMAT'])."</td>"; //Выводим дату принятия на работу

		if($GLOBALS['XMPP_ENABLE'] && $GLOBALS['XMPP_MESSAGE_LISTS_ENABLE'] && $_COOKIE['dn'])
			{
			if(is_array($_COOKIE['xmpp_list']) && in_array($Staff[$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']][$key], $_COOKIE['xmpp_list']))
				$xmpp_link_class="in_xmpp_list";
			else
				$xmpp_link_class='out_xmpp_list';

			echo "<td>
				  <a href=\"#\" class=\"add_xmpp_list ".$xmpp_link_class." in_link\" title=\"".$GLOBALS['L']->l("add_contact_to_xmpp_list")."\" data-login=".$Staff[$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']][$key]."></a>
				  </td>"; //Выводим иконку добавления сотрудника в группу рассылки
			}
		if(isset($GLOBALS['FAVOURITE_CONTACTS'] )&& isset($_COOKIE['dn']))
			{
			if(is_array($Vars['favourite_dns']))
				$favourite_link_class=(in_array($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key], $Vars['favourite_dns'])) ? 'fav_true' : 'fav_false';
			else
				$favourite_link_class='fav_false';
			echo "<td>
				  <a href=\"javascript: F();\" class=\"favourite ".$favourite_link_class." in_link\" title=\"Добавить контакт в избранные.\"></a>
				  <div class=\"hidden\">
				  <div class=\"favourite_user_dn\">".$Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]."</div>
				  </div>
				  </td>";
			}
			if(empty($_COOKIE['dn']) && $GLOBALS['ENABLE_DANGEROUS_AUTH'])
			{
			echo "<td><div><a href=\"\" class=\"is_it_you window in_link\">!</a></div><div class=\"window hidden\">".self::getAuthForm(md5($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]), $Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key])."</div></td>";
			}

		echo"</tr>";
		//-------------------------------------------------------------------------------------------------------------

		}

		public static function getAuthForm($id, $dn)
			{
			if((! empty($_POST['auth_form_id'])) && $id == $_POST['auth_form_id'])
				{$form_sent_class='auth_form_sent'; $password_class="error";}
			else
				{$form_sent_class=''; $password_class="";}

			$Form="<form method=\"POST\" class=\"".$form_sent_class."\" action=\"".$_SERVER['PHP_SELF']."\">";
			$Form.="<label for=\"password_".$id."\">Введите пароль</label><br/>";
			$Form.="<input type=\"password\" class=\"password ".$password_class."\" id=\"password_".$id."\" name=\"password\"/><br/>";

			$Form.=Application::getHiddenFieldForForm();
			$Form.="<input type=\"hidden\" name=\"dn\" value=\"".$dn."\">";
			$Form.="<input type=\"hidden\" name=\"auth_form_id\" value=\"".$id."\">";

			$Form.="<input type=\"submit\" name=\"BItSMe\" value=\"Докажите\">";
			$Form.="</form>";
			return $Form;
			}
}


?>