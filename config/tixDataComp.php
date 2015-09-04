<?php
require('dbconnect.php');

$computerid = $_REQUEST['computerid'];
$sql = $pdo->prepare("SELECT DATE(StartedDate) AS EventDate, COUNT(*) AS Tickets FROM tickets WHERE starteddate > DATE_ADD(NOW(), INTERVAL -180 DAY) AND externalid > 0 AND computerid = :computerid GROUP BY DATE(StartedDate);");
$sql->execute(array('computerid' => $computerid));
//$result = mysqli_query($conn,$sql);

$date = array();
$date['name'] = 'Date';
$tix['name'] = 'Alert Tickets';

//while($row = mysqli_fetch_array($result)) {
foreach($sql as $row) {
	$date['data'][] = $row['EventDate'];
	$tix['data'][] = $row['Tickets'];
}

$rslt = array();
	array_push($rslt, $date);
	array_push($rslt, $tix);
print json_encode($rslt, JSON_NUMERIC_CHECK);

?>
