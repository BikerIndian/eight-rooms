Eight-rooms
=====

Телефонная книга LDAP (Active Directory)

## Контакты
Если возникли вопросы, пиши в телегу >>> https://t.me/eight_rooms

## Doker
https://github.com/BikerIndian/eight-rooms-docker
## Системные требования 
PHP: ^7.0

## Разработка и тестирование
Ubuntu 18.04.4 LTS

PHP 7.2.24-0ubuntu0.18.04.8 (cli) (built: Jul  5 2021 17:41:32) ( NTS )


## Установка 
* git clone https://github.com/BikerIndian/eight-rooms.git

  или 
* скачать https://github.com/BikerIndian/eight-rooms/archive/master.zip

## Настройка

* **Разрешения на запись к папкам temp**
```
Файл: .\eight-rooms\set_chmod
```
В корне лежит файл **set_chmod**. 
Его нужно запустить. Он даёт разрешения на запись к папкам temp.
После запуска можно удалить.


* **Конфигурирование**

```
Файл: .\eight-rooms\config\company\default_company\config_ldap.php
```

Пример для домена **" ad.loc "**

$LDAPServer='ххх.ххх.ххх.ххх';	// Адрес сервера LDAP (Контроллер домена).

$LDAPUser='sprav@ad.loc'; // Учетная запись c правом чтения из LDAP. Можно писать в формате user_for_reading_ldap@YOUR_DOMAIN

$LDAPPassword='pass'; // Пароль для учетной записи, указанной в переменной $LDAPUser

$OU="DC=ad,DC=loc";

// Администраторы
//----------------------------------------------------------------------------
$ADMIN_LOGINS[]='admin@ad.loc';

$LDAP_NoSHOW_GROOP = 'CN=TelNoShow,CN=Users,DC=ad,DC=loc'; // Группа пользователей, которую не отображает справочник. Путь указать полностью. 

Дополнительные настройки в 
.\eight-rooms\config\company\default_company\config.php


## Screenshots
* Спраночник по отделам
![Спраночник по отделам](https://raw.githubusercontent.com/BikerIndian/eight-rooms/master/temp/img/1.png)



* Спраночник по алфавиту
![Спраночник по алфавиту](https://raw.githubusercontent.com/BikerIndian/eight-rooms/master/temp/img/2.png)



* Подробно по пользователю
![Подробно по пользователю](https://raw.githubusercontent.com/BikerIndian/eight-rooms/master/temp/img/3.png)



* Спраночник по отделам
![PDF по отделам](https://raw.githubusercontent.com/BikerIndian/eight-rooms/master/temp/img/5.png)



* PDF - Спраночник по алфавиту
![PDF по алфавиту](https://raw.githubusercontent.com/BikerIndian/eight-rooms/master/temp/img/4.png)


* EXEL - Справочник
![EXEL](https://raw.githubusercontent.com/BikerIndian/eight-rooms/master/temp/img/6.png)
