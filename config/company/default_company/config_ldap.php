<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 17.01.2018
 * Time: 14:59
 */

// LDAP
//----------------------------------------------------------------------------
/**/
$LDAPServer='ххх.ххх.ххх.ххх';	// Адрес сервера LDAP (Контроллер домена).
$LDAPUser='sprav@ad.loc'; // Учетная запись c правом чтения из LDAP. Можно писать в формате user_for_reading_ldap@YOUR_DOMAIN
$LDAPPassword='pass'; // Пароль для учетной записи, указанной в переменной $LDAPUser
$OU="DC=ad,DC=loc"; // В каком Organization Unit искать сотрудников. Оставляем двойные кавычки (внутри, если есть пробелы - обязательно заключать в одинарные кавычки).

//Пользователи, которые имеют право изменять данные о сотрудниках в AD
//----------------------------------------------------------------------------
$ADMIN_LOGINS[]='admin@ad.loc';
//----------------------------------------------------------------------------

$LDAP_WRITE_USER='domain_write_user@domain.ru';	// Учетная запись c правами записи в LDAP.
$LDAP_WRITE_PASSWORD='password_for_domain_write_user'; // Пароль для учетной записи c правами записи в LDAP, указанной в переменной $LDAP_WRITE_USER.
$LDAP_NoSHOW_GROOP = 'CN=TelNoShow,CN=Users,DC=ad,DC=loc'; // Группа пользователей, которую не отображает справочник