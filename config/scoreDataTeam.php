<?php
require('dbconnect.php');
$id = $_REQUEST['id'];

if ($id == 'All') {
  $sql = "SELECT round(avg(antivirus),1) as antivirus, round(avg(disk),1) as disk, round(avg(intrusion),1) as intrusion, round(avg(usability),1) as usability, round(avg(services),1) as services, round(avg(updates),1) as updates, round(avg(`event_log`),1) as events, round(avg(`avg_score`),1) as overall, date(checkdate) AS month, date(checkdate) as mnthorder FROM plugin_lthc_scores_weekly WHERE checkdate > DATE_ADD(NOW(), INTERVAL -180 DAY) GROUP BY month order by mnthorder asc";
	} else {
	  $sql = $pdo->prepare("SELECT round(avg(antivirus),1) as antivirus, round(avg(disk),1) as disk, round(avg(intrusion),1) as intrusion, round(avg(usability),1) as usability, round(avg(services),1) as services, round(avg(updates),1) as updates, round(avg(`event_log`),1) as events, round(avg(`avg_score`),1) as overall, date(checkdate) AS month, date(checkdate) as mnthorder FROM plugin_lthc_scores_weekly WHERE `Team_Assignment`= :id AND checkdate > DATE_ADD(NOW(), INTERVAL -180 DAY) GROUP BY month order by mnthorder asc");
    $sql->execute(array('id' => $id));
	};

//$result = mysqli_query($conn,$sql);

$month = array();
$month['name'] = 'Month';
$avg['name'] = 'Overall Score';
$av['name'] = 'Antivirus';
$disk['name'] = 'Disk';
$intrustion['name'] = 'Intrustion';
$usability['name'] = 'Usability';
$services['name'] = 'Services';
$updates['name'] = 'Updates';
$events['name'] = 'Event Logs';


//while($row = mysqli_fetch_array($result)) {	
foreach($sql as $row) {
	$month['data'][] = $row['month'];
	$avg['data'][] = $row['overall'];
	$av['data'][] = $row['antivirus'];
	$disk['data'][] = $row['disk'];
	$intrustion['data'][] = $row['intrusion'];
	$usability['data'][] = $row['usability'];
	$services['data'][] = $row['services'];
	$updates['data'][] = $row['updates'];
	$events['data'][] = $row['events'];
	}

$rslt = array();
	array_push($rslt, $month);
	array_push($rslt, $avg);
	array_push($rslt, $av);
	array_push($rslt, $disk);
	array_push($rslt, $intrustion);
	array_push($rslt, $usability);
	array_push($rslt, $services);
	array_push($rslt, $updates);
	array_push($rslt, $events);
	print json_encode($rslt, JSON_NUMERIC_CHECK);

?>
