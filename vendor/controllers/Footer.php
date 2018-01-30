<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 19.01.2018
 * Time: 11:20
 */

namespace ru860e\controllers;
use ru860e\rest\Counter;


class Footer
{
    public function printHtml(){
        require_once(dirname(__FILE__) . '/../widget/counter/core/Counter.php');
        $tempFile = dirname(__FILE__)  .'/../../temp/counter.dat';
        $cellar = new  Counter();

        $cellar->setTemp($tempFile);
        $cellar->printHtml();
    }

}