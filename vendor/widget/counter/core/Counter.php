<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 19.01.2018
 * Time: 12:41
 */

namespace ru860e\rest;
require_once (dirname(__FILE__) . "/../../../rest/Content.php");
require_once (dirname(__FILE__) . "/../../../rest/Localization.php");


class Counter extends Content
{
    private $countFileName;
    private $ip = "";
    private $counts = array();
    private $contentIsFileArr = array();
    private $temp;
    private $localization="ru";

    /**
     * @return array
     */
    public function getCounts()
    {
        $this->toCount();
        return $this->counts;
    }

    public function printHtml()
    {
        require_once(dirname(__FILE__) . "/../views/HTMLcounter.php");
    }

    public function getHtml()
    {
        // TODO: Implement getHtml() method.
    }

    public function getLocaleVars(){
        $locale = new Localization(dirname(__FILE__) ."/../locales/" . $this->localization . ".yml");
        return $locale->getLocaleVars();
    }

    /**
     * @param mixed $localization
     */
    public function setLocalization($localization)
    {
        $this->localization = $localization;
    }



    private function toCount()
    {
        $this->countFileName = $this->temp;
        $this->counts["date"] = date("d.m.Y", time() - 3 * 3600);
        $this->ip = getenv("REMOTE_ADDR") . "::" . getenv("HTTP_X_FORWARDED_FOR");

        $this->loadLog();

        // Добавление нового IP
        if (!in_array($this->ip, $this->contentIsFileArr)) {
            $this->contentIsFileArr[] = $this->ip;
            $this->counts["todayUniques"]++;
        }

        $this->contentIsFileArr[0] = $this->counts["date"] . "|" . $this->counts["todayUniques"] . "|" . $this->counts["perDay"] . "|" . $this->counts["totalVisits"];

        $this->writeFile();
    }

    private function loadLog()
    {

        // "Если Файл существует";
        if (file_exists($this->countFileName)) {

            $contentIsFile = $this->readFile(); //Чтение файла

            $this->contentIsFileArr = explode("\n", $contentIsFile);
            $counts_arr = explode("|", $this->contentIsFileArr[0]);

            $this->counts["todayUniques"] = $counts_arr[1];
            $this->counts["totalVisits"] = $counts_arr[3] + 1;
            $this->counts["perDay"] = $counts_arr[2] + 1;


            // Если новая дата текущая то
            if ($counts_arr[0] != $this->counts["date"]) {
                $this->counts[0] = $this->counts["date"];
                $this->counts["todayUniques"] = 1;
                $this->counts["perDay"] = 1;
            }


        } else {
            $this->contentIsFileArr[0] = "";

            $this->counts["todayUniques"] = "0";  // Всего уникальных. $counts[1]
            $this->counts["perDay"] = "1";        //Всего за день - " . $counts[2]
            $this->counts["totalVisits"] = "1";   // Всего посещений - " . $counts[3]
        }
        $this->counts["totalVisits"] = chop($this->counts["totalVisits"]);

    }

    private function readFile()
    {
        // Чтение файла
        $fp = fopen("$this->countFileName", "rb");
        flock($fp, 1);

        $contentIsFile = fread($fp, filesize($this->countFileName));
        fclose($fp);

        return $contentIsFile;
    }

    private function writeFile()
    {
        // Блокируем файл
        $fd = fopen("$this->countFileName", "a");
        //$locked = flock($fd, 2);
        $locked = true;

        // Запись в файл
        if ($locked) {
            $fp = fopen("$this->countFileName", "wb");
            fwrite($fp, implode("\n", $this->contentIsFileArr));
            fclose($fp);
        }
        fclose($fd);
    }

    /**
     * @param mixed $temp
     */
    public function setTemp($temp)
    {
        $this->temp = $temp;
    }

    /**
     * @return mixed
     */
    public function getTemp()
    {
        return $this->temp;
    }


}

