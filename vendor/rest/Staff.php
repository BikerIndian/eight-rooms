<?php
namespace ru860e\rest;

class Staff
{
    private $CONFIG_LDAP_ATTRIBUTE;
    private $CONFIG_PHOTO;
    private $CONFIG_APP;
    private $CONFIG_PHONE;
    private $CONFIG_XMPP;
    private $LDAP_USER;
    private $CALL_VIA_IP;
    private $application;
    private $phones;


    function __construct($CONFIG, $application, $phones)
        {
        $this->CONFIG_LDAP_ATTRIBUTE = $CONFIG['LDAP_ATTRIBUTE'];
        $this->CONFIG_PHOTO = $CONFIG['CONFIG_PHOTO'];
        $this->CONFIG_APP = $CONFIG['CONFIG_APP'];
        $this->CONFIG_PHONE = $CONFIG['CONFIG_PHONE'];
        $this->CONFIG_XMPP = $CONFIG['CONFIG_XMPP'];
        $this->LDAP_USER = $CONFIG['LDAP_USER'];
        $this->CALL_VIA_IP = $CONFIG['CALL_VIA_IP'];
        $this->application = $application;
        $this->phones = $phones;
        }

	function showComputerName($Login)
		{

		if(in_array($Login, $this->LDAP_USER['ADMINS']) && $this->CONFIG_APP['SHOW_COMPUTER_FIELD'])
		{return true;}
		else {return false;}
		}

	function getSurname($value)
		{
		if($this->CONFIG_APP['USE_DISPLAY_NAME'])
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

	function makeNameUrlFromDn($DN, $Title="")
		{
		if($this->CONFIG_APP['USE_DISPLAY_NAME'])
			{
			$DN=preg_replace("/([ёA-zА-я-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\3\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'>\\1</span> \\2</a>", $Title.$DN);
			$DN=preg_replace("/([ёA-zА-я-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zА-я-]+)(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\3\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'>\\2</span> \\1</a>", $DN);	
			$DN=preg_replace("/([ёA-zA-я0-9№\s-]{1,})(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\2\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'> \\1</span></a>", $DN);		
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
		
	function makeMailUrl($Mail)
		{
		if($Mail)
			return preg_replace("/([A-z0-9_\.\-]{1,20}@[A-z0-9\.\-]{1,20}\.[A-z]{2,4})/u", "<a href='mailto:\\1' class='in_link'>\\1</a>", $Mail);
		else
			return "x";
		}

	function makePlainText($Var)
		{
		if($Var)
			return $Var;
		else
			return "x";
		}
		

	function makeDeputy($DN, $Title='')
		{
		if($this->CONFIG_APP['USE_DISPLAY_NAME'])
			return $this->makeNameUrlFromDn($DN, $Title);
		else
			return $this->makeNameUrlFromDn($DN);
		}

	function printDeputyInList($DN, $Title='')
		{
		if($this->CONFIG_APP['SHOW_DEPUTY'] && $DN && $this->CONFIG_APP['SHOW_DEPUTY_IN_LISTS'])
			echo "<span class=\"unimportant\"> ".$localization->get("deputy")." </span><span class=\"deputy\">".$staff->makeNameUrlFromDn($DN, $Title)."</span>";
		}

	// Функции форматирования телефонных номеров
	// ===============================================================================================================
	function makeInternalPhone($Val, $Link=true)
	{
		$phone_attr=$this->phones->get_phone_attr($Val);
		$phone_type = $this->CALL_VIA_IP['PHONE_LINK_TYPE'];

		if (empty($Val)) return 'x';
		if($Link)
		{
			$call_via_ip = ($this->CALL_VIA_IP['ENABLE_CALL_VIA_IP'] && isset($_COOKIE['dn']))?"call_via_ip":"";
			if($this->CONFIG_PHONE['FORMAT_INTERNAL_PHONE'])
			{		
				$Val="<a href=\"".$phone_type.$phone_attr['clear_phone']."\" data-phone-for-ip-call=\"".$phone_attr['clear_phone']."\" class=\"in_link int_phone ".$call_via_ip."\">".$phone_attr['format_phone']."</a>";
			}	
			else
				$Val="<a href=\"".$phone_type.$Val."\" data-phone-for-ip-call=\"".$Val."\"  class=\"in_link int_phone ".$call_via_ip."\">".$Val."</a>";
		}
		else
			{
			if($this->$CONFIG_PHONE['FORMAT_INTERNAL_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}
	// ---------------------------------------------------------------------------------------------------------------		
	function makeCityPhone($Val, $Link=true)
	{
		$phone_attr=$this->phones->get_phone_attr($Val);
		$phone_type = $this->CALL_VIA_IP['PHONE_LINK_TYPE'];

		if (empty($Val)) return 'x';
		if($Link)
		{
			if($this->$CONFIG_PHONE['FORMAT_CITY_PHONE'])
			{		
				if($this->CONFIG_PHONE['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$phone_title="title=\"".$phone_attr['provider_desc']."\"";
				else
					$phone_title="title=\"\"";
				$Val="<a href=\"".$phone_type.$phone_attr['clear_phone']."\" class=\"in_link cityphone\" ".$phone_title.">".$phone_attr['format_phone']."</a>";
			}
			else
				$Val="<a href=\"".$phone_type.$Val."\" class=\"in_link cityphone\">".$Val."</a>";
		}
		else
			{
			if(@$GLOBALS['FORMAT_CITY_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		return $Val;
	}	
	// ---------------------------------------------------------------------------------------------------------------	
	function makeCellPhone($Val, $Link=true)
	{
		$phone_attr=$this->phones->get_phone_attr($Val);
		$phone_type = $this->CALL_VIA_IP['PHONE_LINK_TYPE'];

		if (empty($Val)) return 'x';
		if($Link)
			{	
			$call_via_ip = ($this->CALL_VIA_IP['ENABLE_CALL_VIA_IP'] && isset($_COOKIE['dn']))?"call_via_ip":"";

			if($this->CONFIG_PHONE['FORMAT_CELL_PHONE'])
				{
				if($this->CONFIG_PHONE['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$phone_title="title=\"".$phone_attr['provider_desc']."\"";

				$phone_for_call_via_ip = str_replace ("+7" , $this->CALL_VIA_IP['CALL_VIA_IP_CHANGE_PLUS_AND_SEVEN'], $phone_attr['clear_phone']);
				@$Val="<a href=\"".$phone_type .$phone_attr['clear_phone']."\" data-phone-for-ip-call=\"".$phone_for_call_via_ip."\" class=\"in_link cell_phone ".$call_via_ip."\" ".$phone_title.">".$phone_attr['format_phone']."</a>";
				}
			else
				{	
				$phone_for_call_via_ip = str_replace ("+7" , $this->CALL_VIA_IP['CALL_VIA_IP_CHANGE_PLUS_AND_SEVEN'], $Val);
				$Val="<a href=\"".$phone_type .$Val."\" data-phone-for-ip-call=\"".$phone_for_call_via_ip."\" class=\"in_link cell_phone ".$call_via_ip."\">".$Val."</a>";
				}
			}
		else
			{
			if($this->CONFIG_PHONE['FORMAT_CELL_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}	
	// ---------------------------------------------------------------------------------------------------------------	
	function makeHomePhone($Val, $Link=true)
	{
		$phone_attr = $this->phones->get_phone_attr($Val);
		$phone_type = $this->CALL_VIA_IP['PHONE_LINK_TYPE'];
		if (empty($Val)) return 'x';
		if($Link)
		{
			if($this->CONFIG_PHONE['FORMAT_HOME_PHONE'])
			{
				if($this->CONFIG_PHONE['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$Val="<acronym title =\"".$phone_attr['provider_desc']."\"><a href=\"".$phone_type.$phone_attr['clear_phone']."\" class=\"in_link homephone\">".$phone_attr['format_phone']."</a></acronym>";
				else
					$Val="<a href=\"".$phone_type.$phone_attr['clear_phone']."\" class=\"in_link homephone\">".$phone_attr['format_phone']."</a>";
			}
			else
				$Val="<a href=\"".$phone_type.$Val."\" class=\"in_link homephone\">".$Val."</a>";
		}
		else
			{
			if($this->CONFIG_PHONE['FORMAT_HOME_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}
	// ===============================================================================================================
		
	function makeComputerName($Val)
		{
		if($Val)
			return $Val;
		else
			return "x";	
		}

	function makeTitle($Val)
		{
		if($Val)
			return preg_replace('/(?:\"([^\"]+)\")/u', '&laquo;\\1&raquo;', $Val);
		else
			return "x";	
		}
		
	function makeDepartment($Val, $MakeAdd=false)
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

	function checkInVacation($StDate, $EndDate)
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

	function getVacationState($StDate, $EndDate)
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

	function checkShowVacOnCurrentPage($StDate, $EndDate)
		{
		$menuMarker =$GLOBALS['menu_marker'];
		if($StDate&&$EndDate)
			{
			$VacationState=$this->getVacationState($StDate, $EndDate);
			if(
				(
				(($VacationState == 0) && $GLOBALS['SHOW_CURRENT_VAC'][$menuMarker])
				|| (($VacationState > 0) && $GLOBALS['SHOW_NEXT_VAC'][$menuMarker])
				|| (($VacationState < 0) && $GLOBALS['SHOW_PREV_VAC'][$menuMarker])
				) && $GLOBALS['VACATION']
			  )
				return true;
			else
				return false;	
			}
		}

	function printVacOnCurrentPage($StDate, $EndDate)
		{
		$VacationState=$this->getVacationState($StDate, $EndDate);
		$menuMarker =$GLOBALS['menu_marker'];

		if($this->checkShowVacOnCurrentPage($StDate, $EndDate))
			{
			if($VacationState===0)
				{
				$class='alarm';
				$vac_title=$localization->get("in_vacation_until");
				$vac_period=Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
				if($menuMarker=='si_employeeview')
					{
					$vac_title="<h6 class=\"alarm\">".$localization->get("in_vacation").":</h6>";
					$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
					}
				}
			if($VacationState>0)
				{
				$class='next_vac';
				$vac_title="Ближайший отпуск: ";
				$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);		
				if($menuMarker=='si_employeeview')
					$vac_title="<h6 class=\"".$class."\">".$vac_title."</h6>";					
				}
			if($VacationState<0)
				{
				$class='prev_vac';
				$vac_title="Прошедший отпуск: ";
				$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
				if($menuMarker=='si_employeeview')
					$vac_title="<h6 class=\"".$class."\">".$vac_title."</h6>";	
				}
						

			if($menuMarker=='si_alph_staff_list' || $menuMarker=='si_dep_staff_list' || $menuMarker=='si_stafflist' )
				echo"<span class=\"".$class."\">".$vac_title.$vac_period."</span>";
			if($menuMarker=='si_employeeview')
				echo"<div class=\"birthday\">".$vac_title.$vac_period."</div>";
			}
		}

	function makeAvatar($dn)
	{
		if($this->CONFIG_PHOTO['DIRECT_PHOTO'])
			$Image=$GLOBALS['ldap']->getImage($dn, $this->CONFIG_LDAP_ATTRIBUTE['LDAP_AVATAR_FIELD']);
		else
			$Image=$GLOBALS['ldap']->getImage($dn, $this->CONFIG_LDAP_ATTRIBUTE['LDAP_AVATAR_FIELD'], $this->CONFIG_PHOTO['PHOTO_DIR']."/avatar_".md5($dn).".jpg");

		if($Image)
			return "<div class=\"avatar\"><img src=\"".$Image."\" height=\"".$this->CONFIG_PHOTO['THUMBNAIL_PHOTO_MAX_HEIGHT']."\" width=\"".$this->CONFIG_PHOTO['THUMBNAIL_PHOTO_MAX_WIDTH']."\" /></div>";
		else
			{
			if($this->CONFIG_PHOTO['SHOW_EMPTY_AVATAR'])
				return "<div class=\"avatar\"><img src=\"./skins/".$this->CONFIG_APP['CURRENT_SKIN']."/images/user_avatar.png\" alt=\"user avatar\" height=\"32\" width=\"32\" /></div>";
			}
	}


	function getNumStaffTableColls()
		{
		$num=5;
		$menuMarker =$GLOBALS['menu_marker'];

		if($menuMarker =='si_export_pdf_alphabet' || $menuMarker =='si_export_pdf_department')
			$num=5;
		if($menuMarker =='si_alph_staff_list' || $menuMarker =='si_dep_staff_list')
			{
			$num=5;
			if( $this->showComputerName($GLOBALS['Login']))
				$num++;		
			if($this->CONFIG_APP['FAVOURITE_CONTACTS'] && !empty($_COOKIE['dn']))
				$num++;	

			}

		if(! $this->CONFIG_PHONE['HIDE_CITY_PHONE_FIELD'])
			$num++;
		if(! $this->CONFIG_PHONE['HIDE_CELL_PHONE_FIELD'])
			$num++;
		if(! $this->CONFIG_APP['HIDE_ROOM_NUMBER'])
			$num++;


		if(empty($_COOKIE['dn']) && $this->CONFIG_APP['ENABLE_DANGEROUS_AUTH'])
			$num++;
		if($this->CONFIG_XMPP['XMPP_ENABLE'] && $this->CONFIG_XMPP['XMPP_MESSAGE_LISTS_ENABLE'] && !empty($_COOKIE['dn']))
			$num++;

		return $num;

		}
	function highlightSearchResult($Str, $SearchStr)
		{
		//echo "/((?:<[^>]+>)*[^<]*)(".$SearchStr.")([^<]*(?:<[^>]+>)*)/";
		$Str=preg_replace ("/(>[^>]*)(".$SearchStr.")([^<]*<)/i", "\\1<span class=\"found\">\\2</span>\\3", $Str);
		$Str=preg_replace ("/^([^>]*)(".$SearchStr.")([^<]*)$/i", "\\1<span class=\"found\">\\2</span>\\3", $Str);
		return $Str;
		}

	//Выводит строку таблицы с информацией по определенному сотруднику
	function printUserTableRow($staffUserList, $key, $Vars)
		{
		$StDate=$staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_ST_DATE_VACATION_FIELD']][$key];
		$EndDate=$staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD']][$key];
		$userName = $staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']][$key];

		$VacationState=$this->getVacationState($StDate, $EndDate);	// проверка: в каком состоянии отпуск?
		($VacationState===0) ? $tag="del" : $tag="span";	// в зависимости от этого применяем разные стили
				
		// Строки таблицы
		//-------------------------------------------------------------------------------------------------------------
		$data_parent_id=($Vars['data_parent_id']) ? "data-parent-id=".md5($userName) : '';
		$id=($Vars['id']) ? "id=".md5($userName) : '';

		echo"<tr class=\"".$Vars['row_css']."\" ".$id." ".$data_parent_id.">";
		echo "<td>";
		$this->printVacOnCurrentPage($StDate, $EndDate);		
		if($this->CONFIG_PHOTO['THUMBNAIL_PHOTO_VIS']){
			echo $this->makeAvatar($userName);
		}

		if( ($this->checkInVacation($StDate, $EndDate) && $this->CONFIG_LDAP_ATTRIBUTE['BIND_DEPUTY_AND_VACATION']) || !$this->CONFIG_APP['BIND_DEPUTY_AND_VACATION'])	//
			$this->printDeputyInList(
			    $staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_DEPUTY_FIELD']][$key],
			    $Vars['ldap_conection']->getValue($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_DEPUTY_FIELD']][$key],
			    $this->CONFIG_LDAP_ATTRIBUTE['DISPLAY_NAME_FIELD']));

		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo $this->makeNameUrlFromDn($userName, $staffUserList[$Vars['display_name']][$key]); //Делаем ссылку на полную информацию о сотруднике
		else
			echo $this->highlightSearchResult($this->makeNameUrlFromDn($userName, $staffUserList[$Vars['display_name']][$key]), $Vars['search_str']); //Делаем ссылку на полную информацию о сотруднике

		echo "</td>";
		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo "<td>".$this->makeTitle($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD']][$key])."</td>"; //Выводим должность
		else
			echo "<td>".$this->highlightSearchResult($this->makeTitle($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD']][$key]), $Vars['search_str'])."</td>"; //Выводим должность

		if(isset($Vars['locked_date']))
			echo "<td>".Time::modifyDateFormat($this->makeTitle($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_CHANGED_DATE_FIELD']][$key]), $staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_CHANGED_DATE_FORMAT']], "yyyy-mm-dd")."</td>"; //Выводим должность


		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo "<td>".$this->makeMailUrl($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD']][$key])."</td>"; //Выводим почту
		else
			echo "<td>".$this->highlightSearchResult($this->makeMailUrl($staffUserList[$$this->CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD']][$key]), $Vars['search_str'])."</td>";

		if(!$this->CONFIG_APP['HIDE_ROOM_NUMBER'] && isset($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD']][$key]))
			{
			if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
				echo "<td>".$this->makePlainText($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD']][$key])."</td>"; //Выводим сотовый
			else
				echo "<td>".$this->highlightSearchResult($this->makePlainText($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD']][$key]), $Vars['search_str'])."</td>"; //Делаем ссылку на полную информацию о сотруднике
			}

		echo "<td><".$tag.">".$this->makeInternalPhone($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_INTERNAL_PHONE_FIELD']][$key])."</".$tag."></td>"; //Выводим внутренний
		if(!$this->CONFIG_PHONE['HIDE_CITY_PHONE_FIELD'])
			{
			echo "<td><".$tag.">".$this->makeCityPhone($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_CITY_PHONE_FIELD']][$key])."</".$tag."></td>"; //Выводим городской
			}

		if(!$this->CONFIG_PHONE['HIDE_CELL_PHONE_FIELD'])
			{
			$phone = $this->makeCellPhone($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_CELL_PHONE_FIELD']][$key]);

			if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты	
				echo "<td>".$phone."</td>"; //Выводим сотовый
			else
				echo "<td>".$this->highlightSearchResult($phone, $Vars['search_str'])."</td>"; //Делаем ссылку на полную информацию о сотруднике
			}

		if($this->showComputerName($Vars['current_login'])) //Если сотрудник является администратором справочника
			{
			if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты	
				echo "<td>".$this->makeComputerName($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_COMPUTER_FIELD']][$key])."</td>"; //Выводим имя компьютера
			else
				echo "<td>".$this->highlightSearchResult($this->makeComputerName($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_COMPUTER_FIELD']][$key]), $Vars['search_str'])."</td>"; //Выводим имя компьютера
			}
		if( @$staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_CREATED_DATE_FIELD']][$key] )
			echo "<td>".Time::getHandyDateOfDMYHI($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_CREATED_DATE_FIELD']][$key], $this->CONFIG_LDAP_ATTRIBUTE['LDAP_CREATED_DATE_FORMAT'])."</td>"; //Выводим дату принятия на работу

		if($this->CONFIG_XMPP['XMPP_ENABLE'] && $this->CONFIG_XMPP['XMPP_MESSAGE_LISTS_ENABLE'] && $_COOKIE['dn'])
			{
			if(is_array($_COOKIE['xmpp_list']) && in_array($staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_USERPRINCIPALNAME_FIELD']][$key], $_COOKIE['xmpp_list']))
				$xmpp_link_class="in_xmpp_list";
			else
				$xmpp_link_class='out_xmpp_list';

			echo "<td>
				  <a href=\"#\" class=\"add_xmpp_list ".$xmpp_link_class." in_link\" title=\"".$localization->get("add_contact_to_xmpp_list")."\" data-login=".$staffUserList[$this->CONFIG_LDAP_ATTRIBUTE['LDAP_USERPRINCIPALNAME_FIELD']][$key]."></a>
				  </td>"; //Выводим иконку добавления сотрудника в группу рассылки
			}
		if(isset($this->CONFIG_APP['FAVOURITE_CONTACTS'])&& isset($_COOKIE['dn']))
			{


			if(is_array($Vars['favourite_dns']))
				$favourite_link_class=(in_array($userName, $Vars['favourite_dns'])) ? 'fav_true' : 'fav_false';
			else
				$favourite_link_class='fav_false';
			echo "<td>
				  <a href=\"javascript: F();\" class=\"favourite ".$favourite_link_class." in_link\" title=\"Добавить контакт в избранные.\"></a>
				  <div class=\"hidden\">
				  <div class=\"favourite_user_dn\">".$userName."</div>
				  </div>
				  </td>";
			}

		if(empty($_COOKIE['dn']) && $this->CONFIG_APP['ENABLE_DANGEROUS_AUTH'])
			{
			echo "<td><div><a href=\"\" class=\"is_it_you window in_link\">!</a></div><div class=\"window hidden\">"
			.$this->getAuthForm(md5($userName),$userName)
			."</div></td>";
			}

		echo"</tr>";
		//-------------------------------------------------------------------------------------------------------------

		}

		function getAuthForm($id, $dn)
			{
			if((! empty($_POST['auth_form_id'])) && $id == $_POST['auth_form_id'])
				{$form_sent_class='auth_form_sent'; $password_class="error";}
			else
				{$form_sent_class=''; $password_class="";}

			$Form="<form method=\"POST\" class=\"".$form_sent_class."\" action=\"".$_SERVER['PHP_SELF']."\">";
			$Form.="<label for=\"password_".$id."\">Введите пароль</label><br/>";
			$Form.="<input type=\"password\" class=\"password ".$password_class."\" id=\"password_".$id."\" name=\"password\"/><br/>";

			$Form.=$this->application->getHiddenFieldForForm();
			$Form.="<input type=\"hidden\" name=\"dn\" value=\"".$dn."\">";
			$Form.="<input type=\"hidden\" name=\"auth_form_id\" value=\"".$id."\">";

			$Form.="<input type=\"submit\" name=\"BItSMe\" value=\"Докажите\">";
			$Form.="</form>";
			return $Form;
			}
}


?>