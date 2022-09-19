<?php

// LDAP FIELDS
//----------------------------------------------------------------------------
$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']                    ="distinguishedname";
$CONFIG_LDAP_ATTRIBUTE['si_employeeview']                                 =false;
$CONFIG_LDAP_ATTRIBUTE['LDAP_USERPRINCIPALNAME_FIELD']                    ="userprincipalname";
$CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD']                                 ="name";
$CONFIG_LDAP_ATTRIBUTE['LDAP_OBJECTCLASS_FIELD']                          ="objectclass";
$CONFIG_LDAP_ATTRIBUTE['LDAP_CN_FIELD']                                   ="cn";
$CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD']                                ="title";                       // должность сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']                           ="department";                  // отдел сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_MANAGER_FIELD']                              ="manager";                     // ссылка на руководителя сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_HOMEPHONE_FIELD']                            ="homephone";                   // домашний телефон сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_SN_FIELD']                                   ="sn";                          // хранится фамилия сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_INITIALS_FIELD']                             ="initials";                    // хранится инициалы сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPUTY_FIELD']                               ="extensionattribute15";        // ссылка на замещающего на время отпуска
$CONFIG_LDAP_ATTRIBUTE['LDAP_FAVOURITE_USER_FIELD']                       ="alexFavorites";               // Атрибут LDAP в котором будут хранится ссылки на избранных сотрудником сотрудников
$CONFIG_LDAP_ATTRIBUTE['LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD']              ="displayname";                 // Атрибут LDAP по которому будет разбиваться выборка сотрудников из LDAP (что бы обойти серверный SIZE LIMIT)
$CONFIG_LDAP_ATTRIBUTE['LDAP_CREATED_DATE_FIELD']                         ="whenCreated";                 // Атрибут LDAP в котором будет хранится дата создания учетной записи
$CONFIG_LDAP_ATTRIBUTE['LDAP_CHANGED_DATE_FIELD']                         ="whenChanged";
$CONFIG_LDAP_ATTRIBUTE['LDAP_CHANGED_DATE_FORMAT']                        ="yyyymmddhhmmss";
$CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD']                                ="extensionattribute10";        // Параметр LDAP в котором будет хранится дата рождения сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_COMPUTER_FIELD']                             ="wWWHomePage";                 // Поле LDAP (AD), которое будет использоваться для хранения имени компьютера пользователя. Сотруднику должна быть разрешена запись в данное поле. По умолчанию используется значение {wwwhomepage}.
$CONFIG_LDAP_ATTRIBUTE['DISPLAY_NAME_FIELD']                              ="displayname";	              // Поле LDAP (AD), которое будет использоваться для хранения ФИО сотрудника. По умолчанию используется значение {displayname}. Менять его не рекомендуется.
$CONFIG_LDAP_ATTRIBUTE['LDAP_DATA_FIELD']                                 ="description";                 // Поле LDAP (AD), которое будет использоваться для хранения различных данных о сотруднике, для которых не предусмотрены поля в схеме AD по умолчанию. Данное поле должно поддерживать запись нескольких значение. Какие поля в AD поддерживают такую запись можно посмотреть на странице http://msdn.microsoft.com/en-us/library/ms676199.aspx (параметр Is-Single-Valued). По умолчанию используется значение {description}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD']                                 ="mail";                        // Атрибут LDAP, в котором должен храниться адрес электронной почты. По умолчанию используется значение {mail}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_INTERNAL_PHONE_FIELD']                       ="othertelephone";	          // Атрибут LDAP, в котором должен храниться внутренний номер телефона. По умолчанию используется значение {othertelephone}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_CITY_PHONE_FIELD']                           ="telephonenumber";		      // Атрибут LDAP, в котором должен храниться городской номер телефона. По умолчанию используется значение {telephonenumber}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_CELL_PHONE_FIELD']                           ="mobile";	                  // Атрибут LDAP, в котором должен храниться мобильный номер телефона. По умолчанию используется значение {mobile}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_ST_DATE_VACATION_FIELD']                     ="extensionattribute13";	      // Атрибут LDAP, в котором должна храниться дата начала отпуска сотрудника.
$CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD']                    ="extensionattribute14";	      // Атрибут LDAP, в котором должна храниться дата окончания отпуска сотрудника.
$CONFIG_LDAP_ATTRIBUTE['LDAP_AVATAR_FIELD']                               ="thumbnailphoto";              // Атрибут LDAP в котором будет хранится аватарка пользователя в бинарном виде
$CONFIG_LDAP_ATTRIBUTE['LDAP_PHOTO_FIELD']                                ="jpegphoto";                   // Атрибут LDAP в котором будет хранится полное фото пользователя бинарном виде
$CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD']                          ="physicaldeliveryofficename";  // Атрибут LDAP в котором будет хранится номер кабинета
//----------------------------------------------------------------------------

// LDAP
//----------------------------------------------------------------------------
$CONFIG_LDAP_ATTRIBUTE['LDAP_COMPANY_FIELD'] = "COMPANY"; 	//Атрибут LDAP в котором будет хранится название компании сотрудника
$LDAP_USER['SERVER_LDAP']           = 'ххх.ххх.ххх.ххх';	// Адрес сервера LDAP (Контроллер домена).
$LDAP_USER['USER_READ']             = 'sprav@ad.loc'; 		// Учетная запись c правом чтения из LDAP. Можно писать в формате user_for_reading_ldap@YOUR_DOMAIN
$LDAP_USER['PASSWORD_USER_READ']    = 'pass'; 			// Пароль для учетной записи, указанной в переменной $LDAPUser
$LDAP_USER['OU_USER_READ']          = "DC=ad,DC=loc"; 		// В каком Organization Unit искать сотрудников. Оставляем двойные кавычки (внутри, если есть пробелы - обязательно заключать в одинарные кавычки).

$CONFIG_APP['LDAP_NO_SHOW_GROOP']   = 'TelNoShow,CN=Users,DC=ad,DC=loc'; // Группа пользователей, которую не отображает справочник

$CONFIG_LDAP['DIS_USERS_COND'] = "(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(!(useraccountcontrol:1.2.840.113556.1.4.803:=16))(!(memberOf=".$CONFIG_APP['LDAP_NO_SHOW_GROOP']."))"; // Условие фильтра LDAP, которое должно препятствовать выводу заблокированных и отключенных в Active Directory пользователей. По умолчанию используется значение «(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(!(useraccountcontrol:1.2.840.113556.1.4.803:=16))»
$CONFIG_LDAP['LDAP_SIZE_LIMIT_COMPATIBILITY'] =false; //Сделать возможным выбирать большее количество сотрудников чем указано в SIZE LIMIT сервера? См. также $LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD
//$CONFIG_LDAP['OU'] = "";

$CONFIG['CONFIG_LDAP'] = $CONFIG_LDAP;
$CONFIG['CONFIG_LDAP_ATTRIBUTE'] = $CONFIG_LDAP_ATTRIBUTE;
$CONFIG['CONFIG_APP'] = $CONFIG_APP;
