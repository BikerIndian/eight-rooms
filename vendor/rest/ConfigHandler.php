<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 17.06.2022
 */

namespace ru860e\rest;

class ConfigHandler
{

    public function getConfig(){
    $conf[0] = "default_company";
    $path_config = dirname(__FILE__)."/../../config/company/".$conf[0]."/config.php";

    if ($this->is_file_check($path_config)) {
      require_once($path_config);
    }


      $CONFIG['CONFIG_LDAP_ATTRIBUTE'] = $CONFIG_LDAP_ATTRIBUTE;
      $CONFIG['CONFIG_PHOTO'] = $CONFIG_PHOTO;
      $CONFIG['CONFIG_APP'] = $CONFIG_APP;
      $CONFIG['CONFIG_PHONE'] = $CONFIG_PHONE;
      $CONFIG['CONFIG_XMPP'] = $CONFIG_XMPP;
      $CONFIG['LDAP_USER'] = $LDAP_USER;
      $CONFIG['CONFIG_LDAP'] = $CONFIG_LDAP;
      $CONFIG['BOOKMARK'] = $BOOKMARK;
      $CONFIG['PAGE_LINKS'] = $PAGE_LINKS;
      $CONFIG['CONFIG_PDF'] = $CONFIG_PDF;
      $CONFIG['CONFIG_EXEL'] = $CONFIG_EXEL;
      $CONFIG['BLOCK_VIS'] = $BLOCK_VIS;
      $CONFIG['BIRTHDAYS'] = $BIRTHDAYS;
      $CONFIG['DEP_SORT_ORDER'] = $DEP_SORT_ORDER;
      $CONFIG['STAFF_SORT_ORDER'] = $STAFF_SORT_ORDER;
      $CONFIG['CALL_VIA_IP'] = $CALL_VIA_IP;


      return $CONFIG;
    }

    function getConfigPhone(){
    $path_phone_codes = dirname(__FILE__)."/../../config/phone/phone_codes.php";


      if ($this->is_file_check($path_phone_codes)) {
       require_once($path_phone_codes);
      }

      $path_provider_desc = dirname(__FILE__)."/../../config/phone/provider_desc.php";
      if ($this->is_file_check($path_provider_desc)) {
       require_once($path_provider_desc);
      }

      $CONFIG_PHONE['PHONE_CODES'] = $PHONE_CODES;
      $CONFIG_PHONE['PROVIDER_DESC'] = $PROVIDER_DESC;

      return  $CONFIG_PHONE;
    }

    function is_file_check($path){

      if (is_file($path)) {
      $check = true;
      } else {
       echo "Не найден файл: "+ $path;
       $check = false;
      }

    return $check;
    }
}
