<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 17.06.2022
 */

namespace net\svishch\php\ldap\config;

class ConfigHandler
{

    public function getConfig(){
        $conf[0] = "default_company";

        //echo dirname(__FILE__);
        require_once(dirname(__FILE__) . "/Config.php");
        $path_config = dirname(__FILE__)."/../../../../../../config/company/".$conf[0]."/config.php";

        if ($this->is_file_check($path_config)) {
          include($path_config);
          //require_once($path_config);
        }

        require_once(dirname(__FILE__) . "/ConfigUpdate.php");

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
       echo "Не найден файл: " . $path;
       $check = false;
      }

    return $check;
    }
}
