<?php

namespace ru860e\rest;

class LdapAssistant
{
    private $ldap;
    private $CONFIG;

    function __construct($ldap, $CONFIG)
    {
        $this->ldap = $ldap;
        $this->CONFIG = $CONFIG;
    }

    function getFavourite(){

    }

}