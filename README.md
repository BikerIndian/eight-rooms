Eight-rooms
=====

Телефонная книга  LDAP(Active Directory)

Конфигурирование
Файл: .\eight-rooms\config\company\default_company\config_ldap.php

Для домена " ad.loc "
$LDAPServer='ххх.ххх.ххх.ххх';	// Адрес сервера LDAP (Контроллер домена).
$LDAPUser='sprav@ad.loc'; // Учетная запись c правом чтения из LDAP. Можно писать в формате user_for_reading_ldap@YOUR_DOMAIN
$LDAPPassword='pass'; // Пароль для учетной записи, указанной в переменной $LDAPUser
$OU="DC=ad,DC=loc";

// Администраторы
//----------------------------------------------------------------------------
$ADMIN_LOGINS[]='admin@ad.loc';

Дополнительные настройки в 
.\eight-rooms\config\company\default_company\config.php

## Screenshots

![Спраночник по отделам](https://raw.githubusercontent.com/BikerIndian/eight-rooms/master/temp/img/1.png)