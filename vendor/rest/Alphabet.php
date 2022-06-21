<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 23.01.2018
 * Time: 10:12
 */

namespace ru860e\rest;

class Alphabet
{
    public function printGeneralLetters($localization,$CONFIG_APP)
    {
        echo "<fieldset id=\"move_to_letter\">
		<legend>".$localization->get('fast_move_by_first_letter_of_name')."</legend>";
        $i=0;
        foreach($localization->getArr('alphabet') AS $key=>$value)
        {
            if(!($i%$CONFIG_APP['ALPH_ITEM_IN_LINE'])&&($i!=0))
                echo"<br>";
            echo"<a href=\"#\" class=\"letter in_link\">".mb_strtoupper($value)."</a>";
            $i++;
        }
        echo "</fieldset>";
    }
}