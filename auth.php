<?php
//-------------------------------------------------------------------------------------------------
//echo  'auth.php $_SERVER[REMOTE_USER] = '. $_SERVER['REMOTE_USER'];
//
// Авторизация при входе
if ($ENABLE_ACCESS){
    if(!isset($_COOKIE['dn'])){
        enableAccess($ldapConnector,$PHPPath,$CONFIG);
    }
    else{
        $user = $ldapConnector->getUserForDn($_COOKIE['dn']);
        if (!isset($user)) {
         printAccessForm($PHPPath);
        };
    }

} else {
    noEnableAccess();
}

if (@$_SERVER['REMOTE_USER']) //Если есть прозрачно аутентифицированный пользователь. И в серверной переменной хранится его логин
{
    if ($Login = $ldap->getValue($OU, $LDAP_USERPRINCIPALNAME_FIELD, $LDAP_USERPRINCIPALNAME_FIELD . "=" . $_SERVER['REMOTE_USER'] . "*")) //Проверяим есть ли юзер, с логином аутентифицированного пользователя в LDAP
    {
        if (in_array($Login, $ADMIN_LOGINS)) //Пользователь является администратором справочника
        {
            $Access = true;
        } else //Пользователь НЕ является администратором справочника
            $Access = false;
        $Valid = true;
    } else {
        $Access = false;
        $Valid = false;
    }
} else {
    $Login = array();
    if (isset($_COOKIE['dn'])) {
        $Login = $ldap->getValue($_COOKIE['dn'], $LDAP_USERPRINCIPALNAME_FIELD);
    };

    if (isset($Login)) //Если есть кука и в LDAP есть юзер с DN из этой куки, то пользователь был аутентифицирован не прозрачно ранее.
    {
        if (in_array($Login, $ADMIN_LOGINS)) //Пользователь является администратором справочника
        {
            $Access = true;
        } else //Пользователь НЕ является администратором справочника
            $Access = false;
        $Valid = true;
    } else {
        $Access = false;
        $Valid = false;
    }

}

function enableAccess($ldapConnector,$PHPPath,$CONFIG){


    if(isset($_POST['login'])
        && isset($_POST['password'])){

       $user = $ldapConnector->isAccessUser($_POST['login'],$_POST['password'],$CONFIG['CONFIG_LDAP']['OU'],$CONFIG['CONFIG_LDAP']['FILTER_ACCESS_USERS']);
       if (isset($user)){
        $dn =  $user->LDAP_DISTINGUISHEDNAME_FIELD;

        setcookie('dn', $dn, time() + 5000 * 24 * 60 * 60, "/"); //Сохраняем куку с distinguishedname, что бы в дальнейшем аутентифицировать пользователя по куке.
        $_COOKIE['dn'] = $dn;

       } else{
        printAccessForm($PHPPath);
       }

    } else {
        printAccessForm($PHPPath);
    }

}

function noEnableAccess(){
    if (@$_SERVER['REMOTE_USER']) { //Если есть прозрачно аутентифицированный пользователь. И в серверной переменной хранится его логин

        if ($DistinguishedName = $ldap->getValue($OU, $LDAP_DISTINGUISHEDNAME_FIELD, $LDAP_USERPRINCIPALNAME_FIELD . "=" . $_SERVER['REMOTE_USER'] . "*")) { //Находим его distinguishedname
            //Сохраняем куку с distinguishedname, что бы в дальнейшем аутентифицировать пользователя по куке.
            setcookie('dn', $DistinguishedName, time() + 5000 * 24 * 60 * 60, "/");
            $_COOKIE['dn'] = $DistinguishedName;
        }
    } else {

        if (@$_POST['password']) { //Если пользователь ввел пароль в ручную

            $LC = ldap_connect($LDAPServer); //Соединяемся с сервером LDAP
            if (@ldap_bind($LC, $ldap->getValue($dn, $LDAP_USERPRINCIPALNAME_FIELD), $_POST['password'])) { //Проверяем что пользователь может соединится с сервером LDAP используя введенный пароль.
                setcookie('dn', $dn, time() + 5000 * 24 * 60 * 60, "/"); //Сохраняем куку с distinguishedname, что бы в дальнейшем аутентифицировать пользователя по куке.
                $_COOKIE['dn'] = $dn;
            }
            /*
              else
              $Error['password']=true; */
        }
    }
}

  // Вывод окна авторизации
function printAccessForm($PHPPath){
    include($PHPPath . "/si_auth.php");
    exit;
}
//-------------------------------------------------------------------------------------------------
?>