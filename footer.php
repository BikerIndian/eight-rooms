<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 19.01.2018
 * Time: 11:42
 */

namespace ru860e;
use ru860e\controllers;
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);



require_once(dirname(__FILE__) . "/vendor/controllers/Footer.php");
$foter = new controllers\Footer();
$foter->printHtml();