<?php
/**
 * User: Vladimir Svishch
 * Mail: 5693031@gmail.com
 * Git: https://github.com/BikerIndian
 * Date: 19.01.2018
 * Time: 13:46
 */


$counts = $this->getCounts();
$localeVars = $this->getLocaleVars();

?>

<div align=\"center\">
    <span class=\"counter\"><?= $localeVars["total_visits"] ?> - <?= $counts["totalVisits"] ?> | <?= $localeVars["per_day"] ?> - <?= $this->counts["perDay"] ?> | <?= $localeVars["today_uniques"] ?>  - <?= $counts["todayUniques"] ?></span>
</div>
