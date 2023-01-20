<?php


$CONFIG_LDAP['LDAP_ACCESS_GROUP']   =  $LDAP_ACCESS_GROUP;   // Группа пользователей для доступа к справочнику

$CONFIG['CONFIG_LDAP']                                  = $CONFIG_LDAP;
$CONFIG['CONFIG_LDAP']['OU']                            = $OU;
$CONFIG['CONFIG_LDAP']['LDAP_ACCESS_GROUP']             = $LDAP_ACCESS_GROUP;
$CONFIG['CONFIG_LDAP']['FILTER_ACCESS_USERS']           = $FILTER_ACCESS_USERS;
$CONFIG['CONFIG_LDAP']['DIS_USERS_COND']                = $DIS_USERS_COND;
$CONFIG['CONFIG_LDAP']['LDAP_SIZE_LIMIT_COMPATIBILITY'] = $LDAP_SIZE_LIMIT_COMPATIBILITY;

$CONFIG['CONFIG_LDAP_ATTRIBUTE']['LDAP_COMPANY_FIELD'] = $LDAP_COMPANY_FIELD;

$CONFIG['LDAP_USER']['SERVER_LDAP'] = $LDAPServer;
$CONFIG['LDAP_USER']['USER_READ'] = $LDAPUser;
$CONFIG['LDAP_USER']['PASSWORD_USER_READ'] = $LDAPPassword;
$CONFIG['LDAP_USER']['OU_USER_READ'] = $OU;


$CONFIG_APP['LDAP_NO_SHOW_GROOP']   = 'TelNoShow,CN=Users,DC=ad,DC=loc'; // Группа пользователей, которую не отображает справочник


// Дни рождений
$CONFIG['BIRTHDAYS']['NUM_ALARM_DAYES']                 = $NUM_ALARM_DAYES;             //Количество дней, за которое необходимо предупреждать о днях рождениях.
$CONFIG['BIRTHDAYS']['NEAR_BIRTHDAYS']                  = $NEAR_BIRTHDAYS;              //Выводить ближайшее дни рождения (true) или нет (false)?
$CONFIG['BIRTHDAYS']['BIRTH_DATE_FORMAT']               = $BIRTH_DATE_FORMAT;           // Формат хранения даты рождения в атрибуте LDAP. Доступные значения: «yyyy-mm-dd» и «dd.mm.yyyy»
$CONFIG['BIRTHDAYS']['BIRTH_VIS_ROW_NUM']               = $BIRTH_VIS_ROW_NUM;           // Количество видимых предупреждений о ближайших днях рождения. Остальные скрываются под стрелочку.
$CONFIG['BIRTHDAYS']['SHOW_JUBILEE_INFO']               = $SHOW_JUBILEE_INFO;


$CONFIG['CONFIG_LDAP_ATTRIBUTE'] = $CONFIG_LDAP_ATTRIBUTE;
$CONFIG['CONFIG_APP'] = $CONFIG_APP;
//$CONFIG['CONFIG_PHOTO'] = $CONFIG_PHOTO;
//$CONFIG['CONFIG_PHONE'] = $CONFIG_PHONE;
//$CONFIG['CONFIG_XMPP'] = $CONFIG_XMPP;
//$CONFIG['LDAP_USER'] = $LDAP_USER;
$CONFIG['BOOKMARK'] = $BOOKMARK;
$CONFIG['PAGE_LINKS'] = $PAGE_LINKS;
//$CONFIG['CONFIG_PDF'] = $CONFIG_PDF;
//$CONFIG['CONFIG_EXEL'] = $CONFIG_EXEL;
$CONFIG['BLOCK_VIS'] = $BLOCK_VIS;
//$CONFIG['BIRTHDAYS'] = $BIRTHDAYS;
$CONFIG['DEP_SORT_ORDER'] = $DEP_SORT_ORDER;
$CONFIG['STAFF_SORT_ORDER'] = $STAFF_SORT_ORDER;
//$CONFIG['CALL_VIA_IP'] = $CALL_VIA_IP;


