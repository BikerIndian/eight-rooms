<?php
$CONFIG_LDAP_ATTRIBUTE['si_employeeview']                                 =false;
$CONFIG_LDAP_ATTRIBUTE['LDAP_DISTINGUISHEDNAME_FIELD']                    =$LDAP_DISTINGUISHEDNAME_FIELD;
$CONFIG_LDAP_ATTRIBUTE['LDAP_USERPRINCIPALNAME_FIELD']                    =$LDAP_USERPRINCIPALNAME_FIELD;
$CONFIG_LDAP_ATTRIBUTE['LDAP_NAME_FIELD']                                 =$LDAP_NAME_FIELD;
$CONFIG_LDAP_ATTRIBUTE['LDAP_OBJECTCLASS_FIELD']                          =$LDAP_OBJECTCLASS_FIELD;
$CONFIG_LDAP_ATTRIBUTE['LDAP_CN_FIELD']                                   =$LDAP_CN_FIELD;
$CONFIG_LDAP_ATTRIBUTE['LDAP_TITLE_FIELD']                                =$LDAP_TITLE_FIELD;                       // Атрибут LDAP в котором будет должность сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPARTMENT_FIELD']                           =$LDAP_DEPARTMENT_FIELD;                  // Атрибут LDAP в котором будет отдел сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_MANAGER_FIELD']                              =$LDAP_MANAGER_FIELD;                     // Атрибут LDAP в котором будет хранится ссылка на руководителя сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_HOMEPHONE_FIELD']                            =$LDAP_HOMEPHONE_FIELD;                   // Атрибут LDAP в котором будет хранится домашний телефон сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_SN_FIELD']                                   =$LDAP_SN_FIELD;                          // Атрибут LDAP в котором будет хранится фамилия сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_INITIALS_FIELD']                             =$LDAP_INITIALS_FIELD;                    // Атрибут LDAP в котором будет хранится инициалы сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_DEPUTY_FIELD']                               =$LDAP_DEPUTY_FIELD;                      // Атрибут LDAP в котором будет хранится ссылка на замещающего на время отпуска

$CONFIG_LDAP_ATTRIBUTE['LDAP_FAVOURITE_USER_FIELD']                       =$LDAP_FAVOURITE_USER_FIELD;              // Атрибут LDAP в котором будут хранится ссылки на избранных сотрудником сотрудников
$CONFIG_LDAP_ATTRIBUTE['LDAP_GUID_FIELD']                                 =$LDAP_GUID_FIELD;
$CONFIG_LDAP_ATTRIBUTE['LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD']              =$LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD;     // Атрибут LDAP по которому будет разбиваться выборка сотрудников из LDAP (что бы обойти серверный SIZE LIMIT)
$CONFIG_LDAP_ATTRIBUTE['LDAP_CREATED_DATE_FIELD']                         =$LDAP_CREATED_DATE_FIELD;                // Атрибут LDAP в котором будет хранится дата создания учетной записи
$CONFIG_LDAP_ATTRIBUTE['LDAP_CHANGED_DATE_FIELD']                         =$LDAP_CHANGED_DATE_FIELD;
$CONFIG_LDAP_ATTRIBUTE['LDAP_CHANGED_DATE_FORMAT']                        =$LDAP_CHANGED_DATE_FORMAT;
$CONFIG_LDAP_ATTRIBUTE['LDAP_BIRTH_FIELD']                                =$LDAP_BIRTH_FIELD;                       // Параметр LDAP в котором будет хранится дата рождения сотрудника
$CONFIG_LDAP_ATTRIBUTE['LDAP_COMPUTER_FIELD']                             =$LDAP_COMPUTER_FIELD;                    // Поле LDAP (AD), которое будет использоваться для хранения имени компьютера пользователя. Сотруднику должна быть разрешена запись в данное поле. По умолчанию используется значение {wwwhomepage}.
$CONFIG_LDAP_ATTRIBUTE['DISPLAY_NAME_FIELD']                              =$DISPLAY_NAME_FIELD;	                    // Поле LDAP (AD), которое будет использоваться для хранения ФИО сотрудника. По умолчанию используется значение {displayname}. Менять его не рекомендуется.
$CONFIG_LDAP_ATTRIBUTE['LDAP_DATA_FIELD']                                 =$LDAP_DATA_FIELD;                        // Поле LDAP (AD), которое будет использоваться для хранения различных данных о сотруднике, для которых не предусмотрены поля в схеме AD по умолчанию. Данное поле должно поддерживать запись нескольких значение. Какие поля в AD поддерживают такую запись можно посмотреть на странице http://msdn.microsoft.com/en-us/library/ms676199.aspx (параметр Is-Single-Valued). По умолчанию используется значение {description}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_MAIL_FIELD']                                 =$LDAP_MAIL_FIELD;                        // Атрибут LDAP, в котором должен храниться адрес электронной почты. По умолчанию используется значение {mail}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_INTERNAL_PHONE_FIELD']                       =$LDAP_INTERNAL_PHONE_FIELD;	            // Атрибут LDAP, в котором должен храниться внутренний номер телефона. По умолчанию используется значение {othertelephone}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_CITY_PHONE_FIELD']                           =$LDAP_CITY_PHONE_FIELD;		            // Атрибут LDAP, в котором должен храниться городской номер телефона. По умолчанию используется значение {telephonenumber}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_CELL_PHONE_FIELD']                           =$LDAP_CELL_PHONE_FIELD;	                // Атрибут LDAP, в котором должен храниться мобильный номер телефона. По умолчанию используется значение {mobile}.
$CONFIG_LDAP_ATTRIBUTE['LDAP_ST_DATE_VACATION_FIELD']                     =$LDAP_ST_DATE_VACATION_FIELD;	        // Атрибут LDAP, в котором должна храниться дата начала отпуска сотрудника.
$CONFIG_LDAP_ATTRIBUTE['LDAP_END_DATE_VACATION_FIELD']                    =$LDAP_END_DATE_VACATION_FIELD;	        // Атрибут LDAP, в котором должна храниться дата окончания отпуска сотрудника.
$CONFIG_LDAP_ATTRIBUTE['LDAP_AVATAR_FIELD']                               =$LDAP_AVATAR_FIELD;                      // Атрибут LDAP в котором будет хранится аватарка пользователя в бинарном виде
$CONFIG_LDAP_ATTRIBUTE['LDAP_PHOTO_FIELD']                                =$LDAP_PHOTO_FIELD;                       // Атрибут LDAP в котором будет хранится полное фото пользователя бинарном виде
$CONFIG_LDAP_ATTRIBUTE['LDAP_ROOM_NUMBER_FIELD']                          =$LDAP_ROOM_NUMBER_FIELD;                 // Атрибут LDAP в котором будет хранится номер кабинета

