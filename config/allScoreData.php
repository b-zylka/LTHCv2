<?php
require('dbconnect.php');
header("Content-Type: application/json");

# Monthly query
//$sql = "SELECT round(avg(antivirus),1) as antivirus, round(avg(disk),1) as disk, round(avg(intrusion),1) as intrusion, round(avg(usability),1) as usability, round(avg(services),1) as services, round(avg(updates),1) as updates, round(avg(`event_log`),1) as events, round(avg(`avg_score`),1) as overall, MONTHNAME(checkdate) AS month, month(checkdate) as mnthorder FROM plugin_lthc_scores_monthly group by month order by mnthorder asc";

# Weekly Query
$sql = "SELECT round(avg(antivirus),1) as antivirus, round(avg(disk),1) as disk, round(avg(intrusion),1) as intrusion, round(avg(usability),1) as usability, round(avg(services),1) as services, round(avg(updates),1) as updates, round(avg(`event_log`),1) as events, round(avg(`avg_score`),1) as overall, date(checkdate) AS month, date(checkdate) as mnthorder FROM plugin_lthc_scores_weekly WHERE checkdate > DATE_ADD(NOW(), INTERVAL -360 DAY) group by month order by mnthorder asc";

$month = array();
$month['name'] = 'Month';
$av['name'] = 'Antivirus';
$disk['name'] = 'Disk';
$intrustion['name'] = 'Intrustion';
$usability['name'] = 'Usability';
$services['name'] = 'Services';
$updates['name'] = 'Updates';
$events['name'] = 'Event Logs';
$avg['name'] = 'Overall Score';

foreach($pdo->query($sql) as $row) {
	$month['data'][] = $row['month'];
	$av['data'][] = $row['antivirus'];
	$disk['data'][] = $row['disk'];
	$intrustion['data'][] = $row['intrusion'];
	$usability['data'][] = $row['usability'];
	$services['data'][] = $row['services'];
	$updates['data'][] = $row['updates'];
	$events['data'][] = $row['events'];
	$avg['data'][] = $row['overall'];
}

$rslt = array();
	array_push($rslt, $month);
	array_push($rslt, $av);
	array_push($rslt, $disk);
	array_push($rslt, $intrustion);
	array_push($rslt, $usability);
	array_push($rslt, $services);
	array_push($rslt, $updates);
	array_push($rslt, $events);
	array_push($rslt, $avg);
print json_encode($rslt, JSON_NUMERIC_CHECK);

?>
