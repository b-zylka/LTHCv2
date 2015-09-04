<?php
require('dbconnect.php');

$clientid = $_REQUEST['clientid'];
$stmt = $pdo->prepare("SELECT DATE(QueuedDate) AS EventDate, COUNT(*) AS Patching FROM h_patching JOIN computers USING (computerid) WHERE queueddate > DATE_ADD(NOW(), INTERVAL -60 DAY) AND clientid = :clientid GROUP BY DATE(QueuedDate);");
$stmt->execute(array('clientid' => $clientid));
//$result = mysqli_query($conn,$sql);

$date = array();
$date['name'] = 'Date';
$patch['name'] = 'Patches Deployed';

foreach($stmt as $row) {
	$date['data'][] = $row['EventDate'];
	$patch['data'][] = $row['Patching'];
}

$rslt = array();
	array_push($rslt, $date);
	array_push($rslt, $patch);
print json_encode($rslt, JSON_NUMERIC_CHECK);

?>
