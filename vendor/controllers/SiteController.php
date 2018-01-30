<?php
/**
 * User: vlad
 * Date: 13.01.2018
 * Time: 22:56
 */

namespace ru860e\controllers;

use ru860e\rest\Copyright;

class SiteController
{
    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionFoter()
    {
        require_once(dirname(__FILE__) . "/Footer.php");
        $foter = new Footer();
        $foter->printHtml();
        echo "<br>";
        require_once(dirname(__FILE__) . "/../rest/Copyright.php");
        $copyright = new Copyright();
        $copyright->printHtml();
    }

}