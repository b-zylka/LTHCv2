<?php
require('dbconnect.php');

$clientid = $_REQUEST['clientid'];

// Weekly Query
$sql = $pdo->prepare("SELECT clientid, antivirus, disk, intrusion, usability, services, updates, `event_log` as events, `avg_score` as overall, date(checkdate) AS date FROM plugin_lthc_scores_weekly WHERE clientid= :clientid AND checkdate > DATE_ADD(NOW(), INTERVAL -180 DAY);");
$sql->execute(array('clientid' => $clientid));
# Monthly Query
//$sql = "SELECT clientid, antivirus, disk, intrusion, usability, services, updates, `event_log` as events, `avg_score` as overall, MONTHNAME(checkdate) AS month FROM plugin_lthc_scores_monthly WHERE clientid='{$clientid}'";


$month = array();
$month['name'] = 'Date';
$av['name'] = 'Antivirus';
$disk['name'] = 'Disk';
$intrustion['name'] = 'Intrusion';
$usability['name'] = 'Usability';
$services['name'] = 'Services';
$updates['name'] = 'Updates';
$events['name'] = 'Event Logs';
$avg['name'] = 'Overall Score';

foreach($sql as $row) {
  $month['data'][] = $row['date'];
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
