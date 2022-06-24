<?php


ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/*
*/
require_once(dirname(__FILE__)."/config_ldap.php");

$COMPANY_DIR = "default"; // Каталог для фоток ./temp/[$COMPANY_DIR]

$CONFIG_LDAP['DIS_USERS_COND'] = "(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(!(useraccountcontrol:1.2.840.113556.1.4.803:=16))(!(memberOf=".$CONFIG_APP['LDAP_NO_SHOW_GROOP']."))"; // Условие фильтра LDAP, которое должно препятствовать выводу заблокированных и отключенных в Active Directory пользователей. По умолчанию используется значение «(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(!(useraccountcontrol:1.2.840.113556.1.4.803:=16))»
$CONFIG_LDAP['LDAP_SIZE_LIMIT_COMPATIBILITY'] =false; //Сделать возможным выбирать большее количество сотрудников чем указано в SIZE LIMIT сервера? См. также $LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD
//----------------------------------------------------------------------------

// LDAP FIELDS
//----------------------------------------------------------------------------
$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']                    ="distinguishedname";
$CONFIG_LDAP_ATTRIBUTE['si_employeeview']                                 =false;
$CONFIG_LDAP_ATTRIBUTE['LDAP_USERPRINCIPALNAME_FIELD']                    ="userprincipalname";
$CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD']                                 ="name";
$CONFIG_LDAP_ATTRIBUTE['LDAP_OBJECTCLASS_FIELD']                          ="objectclass";
$CONFIG_LDAP_ATTRIBUTE['LDAP_CN_FIELD']                                   ="cn";
$CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD']                                ="title";                       // Атрибут LDAP в котором будет должность сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']                           ="department";                  // Атрибут LDAP в котором будет отдел сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_MANAGER_FIELD']                              ="manager";                     // Атрибут LDAP в котором будет хранится ссылка на руководителя сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_HOMEPHONE_FIELD']                            ="homephone";                   // Атрибут LDAP в котором будет хранится домашний телефон сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_SN_FIELD']                                   ="sn";                          // Атрибут LDAP в котором будет хранится фамилия сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_INITIALS_FIELD']                             ="initials";                    // Атрибут LDAP в котором будет хранится инициалы сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPUTY_FIELD']                               ="extensionattribute15";        // Атрибут LDAP в котором будет хранится ссылка на замещающего на время отпуска
$CONFIG_LDAP_ATTRIBUTE['LDAP_FAVOURITE_USER_FIELD']                       ="alexFavorites";               // Атрибут LDAP в котором будут хранится ссылки на избранных сотрудником сотрудников
$CONFIG_LDAP_ATTRIBUTE['LDAP_GUID_FIELD']                                 ="objectguid";
$CONFIG_LDAP_ATTRIBUTE['LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD']              ="displayname";                 // Атрибут LDAP по которому будет разбиваться выборка сотрудников из LDAP (что бы обойти серверный SIZE LIMIT)
$CONFIG_LDAP_ATTRIBUTE['LDAP_CREATED_DATE_FIELD']                         ="whenCreated";                 // Атрибут LDAP в котором будет хранится дата создания учетной записи
$CONFIG_LDAP_ATTRIBUTE['LDAP_CHANGED_DATE_FIELD']                         ="whenChanged";
$CONFIG_LDAP_ATTRIBUTE['LDAP_CHANGED_DATE_FORMAT']                        ="yyyymmddhhmmss";
$CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD']                                ="extensionattribute10";        // Параметр LDAP в котором будет хранится дата рождения сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_COMPUTER_FIELD']                             ="wWWHomePage";                 // Поле LDAP (AD), которое будет использоваться для хранения имени компьютера пользователя. Сотруднику должна быть разрешена запись в данное поле. По умолчанию используется значение {wwwhomepage}.
$CONFIG_LDAP_ATTRIBUTE['DISPLAY_NAME_FIELD']                              ="displayname";	              // Поле LDAP (AD), которое будет использоваться для хранения ФИО сотрудника. По умолчанию используется значение {displayname}. Менять его не рекомендуется.
$CONFIG_LDAP_ATTRIBUTE['LDAP_DATA_FIELD']                                 ="description";                 // Поле LDAP (AD), которое будет использоваться для хранения различных данных о сотруднике, для которых не предусмотрены поля в схеме AD по умолчанию. Данное поле должно поддерживать запись нескольких значение. Какие поля в AD поддерживают такую запись можно посмотреть на странице http://msdn.microsoft.com/en-us/library/ms676199.aspx (параметр Is-Single-Valued). По умолчанию используется значение {description}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD']                                 ="mail";                        // Атрибут LDAP, в котором должен храниться адрес электронной почты. По умолчанию используется значение {mail}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_INTERNAL_PHONE_FIELD']                       ="telephonenumber";	          // Атрибут LDAP, в котором должен храниться внутренний номер телефона. По умолчанию используется значение {othertelephone}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_CITY_PHONE_FIELD']                           ="";		                      // Атрибут LDAP, в котором должен храниться городской номер телефона. По умолчанию используется значение {telephonenumber}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_CELL_PHONE_FIELD']                           ="mobile";	                  // Атрибут LDAP, в котором должен храниться мобильный номер телефона. По умолчанию используется значение {mobile}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_ST_DATE_VACATION_FIELD']                     ="extensionattribute13";	      // Атрибут LDAP, в котором должна храниться дата начала отпуска сотрудника.
$CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD']                    ="extensionattribute14";	      // Атрибут LDAP, в котором должна храниться дата окончания отпуска сотрудника.
$CONFIG_LDAP_ATTRIBUTE['LDAP_AVATAR_FIELD']                               ="thumbnailphoto";              // Атрибут LDAP в котором будет хранится аватарка пользователя в бинарном виде
$CONFIG_LDAP_ATTRIBUTE['LDAP_PHOTO_FIELD']                                ="jpegphoto";                   // Атрибут LDAP в котором будет хранится полное фото пользователя бинарном виде
$CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD']                          ="physicaldeliveryofficename";  // Атрибут LDAP в котором будет хранится номер кабинета
//----------------------------------------------------------------------------


// Дни рождений
//----------------------------------------------------------------------------
$BIRTHDAYS['NUM_ALARM_DAYES']               = 14;           //Количество дней, за которое необходимо предупреждать о днях рождениях.
$BIRTHDAYS['NEAR_BIRTHDAYS']                = true;         //Выводить ближайшее дни рождения (true) или нет (false)?
$BIRTHDAYS['BIRTH_DATE_FORMAT']             = "dd.mm.yyyy"; // Формат хранения даты рождения в атрибуте LDAP. Доступные значения: «yyyy-mm-dd» и «dd.mm.yyyy»
$BIRTHDAYS['BIRTH_VIS_ROW_NUM']             = 3;            // Количество видимых предупреждений о ближайших днях рождения. Остальные скрываются под стрелочку.
$BIRTHDAYS['SHOW_JUBILEE_INFO']             = true;

//----------------------------------------------------------------------------

// Различные данные о пользователе
//----------------------------------------------------------------------------
$CONFIG_APP['USE_DISPLAY_NAME']=true;	// Если параметр равен «true», то в качестве ФИО сотрудника справочник будет использовать значение из поля AD, указанного в параметре $DISPLAY_NAME_FIELD, если «false» — то ФИО будет формироваться из значения указанного в атрибуте {distinguishedName}.
//----------------------------------------------------------------------------

// Имя компьютера пользователя
//----------------------------------------------------------------------------

$CONFIG_APP['SHOW_COMPUTER_FIELD']=true; // Столбец компьютер по умалчяанию false
$CONFIG_APP['SHOW_DEPUTY']=true;
$CONFIG_APP['SHOW_DEPUTY_IN_LISTS']=false;

/*
$SHOW_COMPUTER_FIELD
$SHOW_DEPUTY
$SHOW_DEPUTY_IN_LISTS
*/
//----------------------------------------------------------------------------

// Кабинеты
//----------------------------------------------------------------------------
$CONFIG_APP['HIDE_ROOM_NUMBER']=false; //true - кабинеты НЕ показывать , folse кабинеты показывать
//----------------------------------------------------------------------------

// Номера телефонов
//----------------------------------------------------------------------------

$CONFIG_PHONE['HIDE_CITY_PHONE_FIELD']=false;		// Скрыть городской номер телефона (true) или нет (false)?
$CONFIG_PHONE['HIDE_CELL_PHONE_FIELD']=false;		// Скрыть сотовый номер телефона (true) или нет (false)?
$CONFIG_PHONE['FORMAT_CITY_PHONE']=true;	// Использование форматирования для городского номера телефона с помощью функции (true) или оставить, как есть в ldap (false)?
$CONFIG_PHONE['FORMAT_CELL_PHONE']=true;	// Использование форматирования для сотового номера телефона с помощью функции (true) или оставить, как есть в ldap (false)?
$CONFIG_PHONE['FORMAT_INTERNAL_PHONE']=true;	// Использование форматирования внутреннего (офисного) номера телефона с помощью функции (true) или оставить, как есть в ldap (false)?
$CONFIG_PHONE['FORMAT_HOME_PHONE']=true;	// Использование форматирования домашнего номера телефона с помощью функции (true) или оставить, как есть в ldap (false)?
$CONFIG_PHONE['USE_PHONE_CODES_DESCRIPTION']=true;	// Использовать определитель города или оператора по номеру во всплывающей подсказке (true) или нет (false)? Игнорируется, если $FORMAT_*_PHONE=false;
$CONFIG_PHONE['FORMAT_PHONE_BLOCKLEN']=3;	// Размер блока разбивки телефона. Пример: XXX-XX-XX (2), XXXX-XXX (3). Если длина номера нечетная, первая цифра всегда будет склеена с первым блоком

//----------------------------------------------------------------------------

// Отпуск сотрудников
//----------------------------------------------------------------------------
$CONFIG_APP['VACATION']=true;	// Если параметр равен «true», то справочник будет показывать отпуска сотрудников, давать возможность сотрудникам менять свой отпуск и т.д, если «false» — нет. По умолчанию используется значение «true».
$CONFIG_APP['VAC_CLAIM_ALARM']=true;	// Если параметр равен «true», то справочник будет предупреждать о необходимости написать заявление на отпуск, если «false» — нет. По умолчанию используется значение «true».
$CONFIG_APP['VAC_CLAIM_ALARM_DAYES_FROM']=21;	// За какое количество дней необходимо начинать предупреждать сотрудника написать заявление на отпуск. По умолчанию используется значение «21».
$CONFIG_APP['VAC_CLAIM_ALARM_DAYES_TO']=13;	// За какое количество дней необходимо закончить предупреждать сотрудника написать заявление на отпуск. По умолчанию используется значение «13».
$CONFIG_APP['VAC_DATE_FORMAT']="dd.mm.yyyy";

$SHOW_PREV_VAC['si_employeeview']=false;
$SHOW_NEXT_VAC['si_employeeview']=false;
$SHOW_CURRENT_VAC['si_employeeview']=true;

$SHOW_PREV_VAC['si_alph_staff_list']=false;
$SHOW_NEXT_VAC['si_alph_staff_list']=false;
$SHOW_CURRENT_VAC['si_alph_staff_list']=true;

$SHOW_PREV_VAC['si_dep_staff_list']=false;
$SHOW_NEXT_VAC['si_dep_staff_list']=false;
$SHOW_CURRENT_VAC['si_dep_staff_list']=true;
//----------------------------------------------------------------------------

// Вкладки организации и ее филиалов
//----------------------------------------------------------------------------
/* Ключ массива - название организации без ковычек. 
По данной строке осуществляется выборка записей из LDAP. 
Должно являтся подстрокой названия организации, хранящейся в LDAP. 
Значение - то как данная организация будет отображаться в закладках справочника 
Можно отображать не только организацию но и любые вкладки по атрибуту.
*/

$BOOKMARK_NAMES['company']['MultX']="MultikiX";
$BOOKMARK_NAMES['company']['MultY']="MultikiY";

$BOOKMARK_NAME_EXACT_FIT['company']=true;
$BOOKMARK_NAME_EXACT_FIT['mobile']=false;
$BOOKMARK_MAX_NUM_ITEMS['company']=3;

$BOOKMARK['BOOKMARK_NAMES'] = $BOOKMARK_NAMES;
$BOOKMARK['BOOKMARK_NAME_EXACT_FIT'] = $BOOKMARK_NAME_EXACT_FIT;
$BOOKMARK['BOOKMARK_MAX_NUM_ITEMS'] = $BOOKMARK_MAX_NUM_ITEMS;
//----------------------------------------------------------------------------

// Экспорт в PDF
//----------------------------------------------------------------------------

$CONFIG_PDF['ENABLE_PDF_EXPORT']=true; // Меню экспрта в PDF вкл/откл
$CONFIG_PDF['PDF_TITLE']="Справочник сотрудников компании"; //Заголовок справочника при экспорте в PDF.
$CONFIG_PDF['PDF_SECOND_LINE']="тел.: +7 (495) xxx-xx-xx"; //Постоянные контакты, которые будут выводится в PDF (Например, номер факса)
$CONFIG_PDF['PDF_HIDE_STAFF_WITHOUT_PHONES']=true;	// Если параметр равен «true», то при экспорте в PDF не будут отображаться те сотрудники, у которых нет, хотя бы одного телефонного номера, если «false» — будут выводиться все. По умолчанию используется значение «true».
$CONFIG_PDF['PDF_MARGIN_LEFT']=5; //Отступ слева
$CONFIG_PDF['PDF_MARGIN_TOP']=5; //Отступ сверху
$CONFIG_PDF['PDF_MARGIN_RIGHT']=5; //Отступ справа
$CONFIG_PDF['PDF_MARGIN_BOTTOM']=5; //Отступ снизу
$CONFIG_PDF['PDF_LANDSCAPE']=false; // Если параметр равен «true», то при экспорте в PDF будет использоваться альбомная ориентация, если «false» — то нет. По умолчанию используется значение «false».
$CONFIG_PDF['PDF_LOGO']="../temp/".$COMPANY_DIR."/logo/logo.png";
$CONFIG_PDF['PDF_WIDTH_LOGO']="100";  //Ширина логотипа в пикселях (Vladimir Svishch)
$CONFIG_PDF['PDF_HEIGHT_LOGO']=""; //Высота фото в пикселях	(Vladimir Svishch)


//----------------------------------------------------------------------------

// Экспорт в EXEL
//----------------------------------------------------------------------------
$CONFIG_EXEL['ENABLE_EXEL_EXPORT']=true; // Меню экспрта в PDF вкл/откл

// Сортировка в справочнике
//----------------------------------------------------------------------------
$DIRECTOR_FULL_TITLE="Директор";	// Полная должность директора (так как она прописана у него в AD). Используется для определения директора организации. По умолчанию используется значение «Директор».
/* DEP_SORT_ORDER - параметр позволяет изменить порядок вывода отделов сотрудников в списке с разбивкой по отделам. По умолчанию отделы выводятся в алфавитном порядке. 
Данными атрибутами можно ввести коррективы в этот порядок.Например,

Непосредственно перед сортировкой отделов в названиях отделов подстрока «Дирекция» заменится на пробел, подстрока «Департамент ИТ\Системные администраторы» на «Департамент ИТ\». После чего произойдет сортировка отделов. */
$DEP_SORT_ORDER["Дирекция"]='order_replace';
$DEP_SORT_ORDER["Департамент ИТ\Системные администраторы"]["Департамент ИТ\\"]='order_replace';

/* То же, что и $DEP_SORT_ORDER только для должностей сотрудников
Первым по списку будет должность, в которой есть подстрока «Старший», затем «Начальник» и т.д.
Все это касается списка сотрудников с разбивкой по отделам. */
$STAFF_SORT_ORDER["Президент"]='order_replace';
$STAFF_SORT_ORDER["Старший"]='order_replace';
$STAFF_SORT_ORDER["Начальник"]='order_replace';
$STAFF_SORT_ORDER["Директор"]='order_replace';
$STAFF_SORT_ORDER["Руководитель"]='order_replace';
$STAFF_SORT_ORDER["Заместитель"]='order_replace';
$STAFF_SORT_ORDER["Главный"]='order_replace';
//----------------------------------------------------------------------------

// Поиск в справочнике
//----------------------------------------------------------------------------
$SEARCH_DEFAULT_VALUE="*"; //Значение по умолчанию для поля поиска.
$ONLY_BOOKMARK=false; //Если false, то галочка "Искать только пользователей в закладке" снята, если true то - выставлена. 
$ONLY_BOOKMARK_VIS=true; //Если false, то галочка "Искать только пользователей в закладке" не будет отображаться, если true то - будет. 
//----------------------------------------------------------------------------

// Работа с фотографиями пользователей
//----------------------------------------------------------------------------
$CONFIG_PHOTO['PHOTO_DIR']="./temp/".$COMPANY_DIR."/img"; //Директория для хранения фотографий
$CONFIG_PHOTO['DIRECT_PHOTO']=false;	// Если параметр равен «true», то фотография сотрудника будет передоваться напрямую в атрибут «src» тега «img», если «false» — то фотография будет сохранена в файл в папку, указанную в параметре «PHOTO_DIR».
$CONFIG_PHOTO['PHOTO_MAX_SIZE']=500; //Максимальный размер файла в Кб для загрузки в качестве фотографии
$CONFIG_PHOTO['PHOTO_EXT']="jpg"; //Расширение файла для загрузки в качестве фотографии
$CONFIG_PHOTO['PHOTO_MAX_WIDTH']=""; //Максимальная ширина фото в пикселях
$CONFIG_PHOTO['PHOTO_MAX_HEIGHT']=300; //Максимальная высота фото в пикселях
$CONFIG_PHOTO['THUMBNAIL_PHOTO_MAX_WIDTH']=32; //Максимальный ширина фото в атрибуте thumbnailphoto в пикселях
$CONFIG_PHOTO['THUMBNAIL_PHOTO_MAX_HEIGHT']=32; //Максимальный высота фото в атрибуте thumbnailphoto в пикселях
$CONFIG_PHOTO['THUMBNAIL_PHOTO_MAX_SIZE']=10; //Максимальный размер в килобайтах записываемой в атрибут thumbnailphoto фотографии. Менять не рекомендуется. Размер данного атрибута может быть ограничен сервером LDAP.
$CONFIG_PHOTO['THUMBNAIL_PHOTO_EDIT']=true; //Редактировать атрибут thumbnailphoto при сохранении фотографии сотрудника
$CONFIG_PHOTO['THUMBNAIL_PHOTO_VIS']=true; //Отображать фото из атрибута thumbnailphoto в справочнике
$CONFIG_PHOTO['SHOW_EMPTY_AVATAR']=true; 	// Показывать пустую аватарку (если у пользователя нет фотографии)
//----------------------------------------------------------------------------

// Регулярные выражения (http://ru2.php.net/manual/ru/reference.pcre.pattern.syntax.php)
//----------------------------------------------------------------------------
$RE_MAIL="(^\w+([\.\w]+)*\w@\w((\.\w)*\w+)*\.\w{2,4}$)|(^$)"; //Регулярное выражение для адреса электронной почты. В случае несоответствия, изменения не будут применены.
//$RE_OTHER_TELEPHONE="(^[0-9]{3}$)|(^$)"; //Регулярное выражение для внутреннего номера. В случае несоответствия, изменения не будут применены.
//$RE_OTHER_TELEPHONE=""; //Регулярное выражение для внутреннего номера. В случае несоответствия, изменения не будут применены.
//$RE_OTHER_TELEPHONE=""; //Регулярное выражение для внутреннего номера. В случае несоответствия, изменения не будут применены.
//$RE_MOBILE="(^\+7[0-9]{10}$)|(^[0-9]{6}$)|(^2[0-9]{6}$)|(^$)"; //Регулярное выражение для номера мобильного (сотового) телефона. В случае несоответствия, изменения не будут применены.
//$RE_MOBILE=""; //Регулярное выражение для номера мобильного (сотового) телефона. В случае несоответствия, изменения не будут применены.
//$RE_TELEPHONE_NUMBER="(^[0-9]{7}$)|(^8[0-9]{10}$)|(^$)"; //Регулярное выражение для городского номера телефона. В случае несоответствия, изменения не будут применены.
//$RE_TELEPHONE_NUMBER=""; //Регулярное выражение для городского номера телефона. В случае несоответствия, изменения не будут применены.
$RE_BIRTHDAY="(^[0-3]{1}[0-9]{1}.[0-1]{1}[0-9]{1}.[0-9]{4}$)|(^$)"; //Регулярное выражение для даты рождения. В случае несоответствия, изменения не будут применены.
$RE_FIO="(^[ёA-zА-я-]+[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)$)|(^[ёA-zА-я-]+[\s]{1}[ёA-zA-я]{1}.[\s]{1}[ёA-zА-я-]+$)|(^$)"; //Регулярное выражение для ФИО. В случае несоответствия, изменения не будут применены.
//----------------------------------------------------------------------------

// WEB-настройки справочника
//----------------------------------------------------------------------------
$TITLE="Телефонный справочник холдинга "; // Значение данного параметра будет выводиться в заголовке html-страницы
$CONFIG_APP['DEFAULT_PAGE']="si_dep_staff_list"; // Маркер страницы, которая должна открываться по умолчанию. Доступны значения, перечисленные в параметре $PAGE_LINKS. По умолчанию используется «si_dep_staff_list»
//$DEFAULT_PAGE="si_stafflist"; // Маркер страницы, которая должна открываться по умолчанию. Доступны значения, перечисленные в параметре $PAGE_LINKS. По умолчанию используется «si_dep_staff_list»
/* 
PAGE_LINKS - Данный атрибут позволяет управлять вкладками, отвечающими за представление информации о сотрудниках. На данный момент справочник может отображать информацию о сотрудниках:
- с разбивкой по отделам
	$PAGE_LINKS['si_dep_staff_list']="По отделам";
- с разбивкой по первым буквам фамилий
	$PAGE_LINKS['si_alph_staff_list']="По алфавиту";
- общим списком без разбивок с возможностью сортировать сотрудников по определенным параметрам.
	$PAGE_LINKS['si_stafflist']="Поиск сотрудников"; 
- список недавно принятых сотрудников.
	$PAGE_LINKS['si_stafflist']="Новички"; 
*/
$PAGE_LINKS['si_dep_staff_list']="По отделам";
$PAGE_LINKS['si_alph_staff_list']="По алфавиту";
#$PAGE_LINKS['si_stafflist']="Поиск сотрудников";
#$PAGE_LINKS['si_new_workers']="Новички";
#$PAGE_LINKS['si_locked_list']="Уволенные";

/* 
BLOCK_VIS - Данный параметр позволяет управлять блоками на вкладках, заданных в параметре $PAGE_LINKS
На данный момент есть 4 типа блоков:
- Блок поиска сотрудников ['search'] 
- Блок, выводящий информацию об аутентифицированном сотруднике ['profile']
- Блок ближайших дней рождений ['birthdays']
- Блок быстрого перехода на букву или на отдел ['fast_move']
*/
//Блок поиска сотрудников
$BLOCK_VIS['si_dep_staff_list']['search']=false;
$BLOCK_VIS['si_alph_staff_list']['search']=false;
$BLOCK_VIS['si_stafflist']['search']=true;

//Блок, выводящий информацию об аутентифицированном сотруднике
$BLOCK_VIS['si_dep_staff_list']['profile']=true; // Показывает меню администратора // по умалчанию false
$BLOCK_VIS['si_alph_staff_list']['profile']=true;  // по умалчанию false
$BLOCK_VIS['si_stafflist']['profile']=true;

//Блок ближайших дней рождений
$BLOCK_VIS['si_dep_staff_list']['birthdays']=true;
$BLOCK_VIS['si_alph_staff_list']['birthdays']=true;
$BLOCK_VIS['si_stafflist']['birthdays']=true;

//Блок быстрого перехода на букву или на отдел
$BLOCK_VIS['si_dep_staff_list']['fast_move']=true;
$BLOCK_VIS['si_alph_staff_list']['fast_move']=true;
$HIDE_STAFF_WITHOUT_PHONES=false; // То же что $PDF_HIDE_STAFF_WITHOUT_PHONES, но для списка сотрудников с разбивкой по отделам.

$CONFIG_APP['ALPH_ITEM_IN_LINE']=35; // Количество букв в одной строке в блоке быстрого перехода, на странице с разбивкой сотрудников по первым буквам фамилии. По умолчанию значение равно «35»
$CONFIG_APP['DEP_ITEM_IN_COL']=3; // Количество отделов в одном столбце в блоке быстрого перехода, на странице с разбивкой сотрудников по отделам. По умолчанию значение равно «3»
//$COPY_RIGHT="<a href=\"http://www.pitin.su\" target=\"NewWindow\">© V. Pitin, 2012 </a>"; // :-)
$COPY_RIGHT="© Vladimir Svishch, 2022, mail:  <a href=\"mailto:5693031@gmail.com\" class=\"in_link\">5693031@gmail.com</a> & Vladimir Pitin, 2012";

/* 
$DEP_ADD - Этот атрибут позволяет добавить дополнительную строку в конце названия отдела на странице с разбивкой сотрудников по отделам.
Вот так, например, можно добавить общий телефон отдела:
$DEP_ADD['Департамент управления складом\Группа приемки']="<span class=\"add_dep_info\"><a href=\"callto:234-52-23\" class=\"in_link int_phone\">234-52-23</a><span>"; 
*/
$DEP_ADD['Департамент управления складом\Группа приемки']="<span class=\"add_dep_info\"><a href=\"callto:222-22-22\" class=\"in_link int_phone\">222-22-22</a><span>";
$DEP_ADD['Департамент аптечной сети']="<span class=\"add_dep_info\"><a href=\"callto:111-11-11\" class=\"in_link int_phone\">111-11-11</a><span>";
$CONFIG['ALARM_MESSAGE'] = ""; // Если в параметре что-то есть, то будет выводиться «тревожное» сообщение на всех страницах справочника.
//----------------------------------------------------------------------------

//Skins
//----------------------------------------------------------------------------
/*
$CURRENT_SKIN - текущий (используемый) скин (оформление). Все скины храняться в папке "./skins/".
Можно создать еще одну папку со стилями и графикой и присвоить $CURRENT_SKIN имя это папки. Будет
использовано соответствующее оформление. Структура папок и именование должны быть в точности такие 
же, как и в default.
*/

$CONFIG_APP['CURRENT_SKIN']='default';	// По умолчанию $CURRENT_SKIN='default'.
//----------------------------------------------------------------------------

//Other
//----------------------------------------------------------------------------
// Во сех этих переменных ничего менять не нужно! Их наличие объясняется скриптами на основе которых родился телефонный справочник.

$CONFIG_APP['PHPPath']="./pages";
$CONFIG_APP['CHARSET_DATA']="UTF-8";
$CONFIG_APP['CHARSET_APP']="UTF-8";
$CONFIG_APP['AUTH_TYPE']="none"; //basic, sspi, none
$CONFIG_APP['MENU']=false;

$CONFIG_APP['BIND_DEPUTY_AND_VACATION']=true;

//Избраное
//----------------------------------------------------------------------------
$CONFIG_APP['FAVOURITE_CONTACTS'] =true; //Включить возможность добавлять сотрудников в избраное

//----------------------------------------------------------------------------

//Новые сотрудники
//----------------------------------------------------------------------------
$CONFIG_APP['LDAP_CREATED_DATE_FORMAT']="dd.mm.yyyy hh:mm:ss"; //формат хранения даты в атрибуте $LDAP_CREATED_DATE_FIELD
$CONFIG_APP['NEW_USERS_NUM_DAYS']=30; //За какое количество дней отображать пользователей на страничке $PAGE_LINKS['si_new_workers'] (новых сотрудников)
$CONFIG_APP['EVALUATION_PERIOD']=30; //За какое количество дней отображать предупреждение на страничке с полной информацией о пользователе
$CONFIG_APP['SHOW_EVALUATION_PERIOD_MESSAGE']=true; //Отображать предупреждение о том что сотрудни новый на страничке с полной информацией о пользователе?

//----------------------------------------------------------------------------

// Включаем аутентификацию
$CONFIG_APP['ENABLE_DANGEROUS_AUTH']=true;

$CONFIG_APP['LOCALIZATION']="ru";

$LDAP_XMMP_GROUP_TITLE_FIELD = "description";

$LDAP_MEMBER_FIELD = "member";

$CONFIG_XMPP['XMPP_ENABLE']=false; // Закладка с права, отправить сообщение
$CONFIG_XMPP['$XMPP_ENCRYPTION']=false;
$CONFIG_XMPP['$XMPP_SERVER']="192.168.3.33";
$CONFIG_XMPP['$XMPP_PORT']="5222";
$CONFIG_XMPP['$XMPP_USER']="bot_fluder";
$CONFIG_XMPP['$XMPP_PASSWORD']="24234dsf";
$CONFIG_XMPP['$XMPP_DOMAIN']="your_jabber_server.your_domain.ru";
$CONFIG_XMPP['$XMPP_ACCOUNT_END']="srv-jabber";
$CONFIG_XMPP['$XMPP_MESSAGE_LISTS_ENABLE']=true;
$CONFIG_XMPP['$XMPP_LAST_MESSAGE_TIME_OF_KEEPING'] = 30*24*60*60;
$CONFIG_XMPP['$XMPP_MESSAGE_LISTS_TIME_OF_LIVE']=30*24*60*60;
$CONFIG_XMPP['$XMPP_LAST_MESS_NUM_SYM_OF_PRUNING'] = 100;
$CONFIG_XMPP['$XMPP_NUM_OF_LAST_MESSAGES_PER_USER'] = 10;
$CONFIG_XMPP['$XMPP_MESSAGE_SIGN_ENABLE'] = true;
$CONFIG_XMPP['$XMPP_USE_INTERNAL_PHONE_IN_SIGN_ENABLE'] = true;
$CONFIG_XMPP['$XMPP_USE_MOBILE_PHONE_IN_SIGN_ENABLE'] = true;
$CONFIG_XMPP['$XMPP_LDAP_GROUPS_ENABLE'] = true;
$CONFIG_XMPP['$XMPP_LDAP_GROUPS_OU'] = "OU=Группы безопасности,DC=PRP,DC=ru";
$CONFIG_XMPP['$XMPP_LDAP_GROUPS_SUBSTR'] = "jbr";


//Call via IP phones
//----------------------------------------------------------------------------
$CALL_VIA_IP['ENABLE_CALL_VIA_IP'] = true;
$CALL_VIA_IP['PHONE_LINK_TYPE'] = "callto:";
$CALL_VIA_IP['CALL_VIA_IP_CHANGE_PLUS_AND_SEVEN']="08";
$CALL_VIA_IP['CALL_VIA_IP_HOST'] = "192.192.192.192";
$CALL_VIA_IP['CALL_VIA_IP_USER'] = "user_name";
$CALL_VIA_IP['CALL_VIA_IP_SECRET'] = "Secret_111";
$CALL_VIA_IP['CALL_VIA_IP_CHANEL'] = "Infinity";
$CALL_VIA_IP['CALL_VIA_IP_CONTEXT'] = "phone-book";
$CALL_VIA_IP['CALL_VIA_IP_WAIT_TIME'] = "30";
$CALL_VIA_IP['CALL_VIA_IP_PRIORITY'] = "1";
$CALL_VIA_IP['CALL_VIA_IP_MAX_RETRY'] = "0";
//----------------------------------------------------------------------------
?>