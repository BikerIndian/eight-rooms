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

    private $content = 'VmxhZGltaXIgU3Zpc2hjaCwgMjAxNyAtIDIwMTgsIG1haWw6ICA8YSBocmVmPSJtYWlsdG86NTY5MzAzMUBnbWFpbC5jb20iIGNsYXNzPSJpbl9saW5rIj41NjkzMDMxQGdtYWlsLmNvbTwvYT4sR2l0OiA8YSBocmVmPSJodHRwczovL2dpdGh1Yi5jb20vQmlrZXJJbmRpYW4vZWlnaHQtcm9vbXMiPlJlbGVhc2UuPC9hPg0KPGJyPiYgVmxhZGltaXIgUGl0aW4sIDIwMTIgIA0KDQo=';

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