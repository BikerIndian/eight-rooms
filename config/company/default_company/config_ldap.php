<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 24.06.2022
 */

// LDAP
//----------------------------------------------------------------------------
$CONFIG_LDAP_ATTRIBUTE['LDAP_COMPANY_FIELD'] = "COMPANY"; 	//Атрибут LDAP в котором будет хранится название компании сотрудника
$LDAP_USER['SERVER_LDAP']           = 'ххх.ххх.ххх.ххх';	// Адрес сервера LDAP (Контроллер домена).
$LDAP_USER['USER_READ']             = 'sprav@ad.loc'; 		// Учетная запись c правом чтения из LDAP. Можно писать в формате user_for_reading_ldap@YOUR_DOMAIN
$LDAP_USER['PASSWORD_USER_READ']    = 'pass'; 			// Пароль для учетной записи, указанной в переменной $LDAPUser
$LDAP_USER['OU_USER_READ']          = "DC=ad,DC=loc"; 		// В каком Organization Unit искать сотрудников. Оставляем двойные кавычки (внутри, если есть пробелы - обязательно заключать в одинарные кавычки).


//Пользователи, которые имеют право изменять данные о сотрудниках в AD
//----------------------------------------------------------------------------
$ADMINS[]='admin@ad.loc';

//----------------------------------------------------------------------------

$LDAP_USER['USER_WRITE']            = 'domain_write_user@domain.ru';	// Учетная запись c правами записи в LDAP.
$LDAP_USER['PASSWORD_USER_WRITE']   =' password_for_domain_write_user'; // Пароль для учетной записи c правами записи в LDAP, указанной в переменной $LDAP_WRITE_USER.
$CONFIG_APP['LDAP_NO_SHOW_GROOP']   = 'TelNoShow,CN=Users,DC=ad,DC=loc'; // Группа пользователей, которую не отображает справочник

$LDAP_USER['ADMINS'] = $ADMINS;