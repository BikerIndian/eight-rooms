<?php

$CONFIG_LDAP_ATTRIBUTE['si_employeeview']                                 =false;
$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']                    ="distinguishedname";
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

