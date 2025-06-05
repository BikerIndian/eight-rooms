<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 30.01.2018
 * Time: 9:40
 */

namespace ru860e\rest;


class Copyright extends Content
{

    private $content = 'wqkgVmxhZGltaXIgU3Zpc2hjaDogMjAxNyAtIDIwMjUsCjxhIGhyZWY9Imh0dHBzOi8vdC5tZS9laWdodF9yb29tcyIgY2xhc3M9ImluX2xpbmsiPnRlbGVncmFtPC9hPiwKPGEgaHJlZj0ibWFpbHRvOjU2OTMwMzFAZ21haWwuY29tIiBjbGFzcz0iaW5fbGluayI+bWFpbDwvYT4sICAKPGEgaHJlZj0iaHR0cHM6Ly9naXRodWIuY29tL0Jpa2VySW5kaWFuL2VpZ2h0LXJvb21zIj5HaXQ8L2E+Cjxicj5WbGFkaW1pciBQaXRpbjogMjAxMiAg';

    public function printHtml()
    {
      echo $this->recovery($this->content);
    }


    public function getHtml()
    {
        return $this->content;
    }

    private function recovery ($content){
        return base64_decode($content);
    }
}