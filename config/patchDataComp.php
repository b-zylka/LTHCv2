<?php
require('dbconnect.php');

$computerid = $_REQUEST['computerid'];
$sql = $pdo->prepare("SELECT DATE(QueuedDate) AS EventDate, COUNT(*) AS Patching FROM h_patching WHERE queueddate > DATE_ADD(NOW(), INTERVAL -60 DAY) AND computerid = :computerid GROUP BY DATE(QueuedDate);");
$sql->execute(array('computerid' => $computerid));
//$result = mysqli_query($conn,$sql);

$date = array();
$date['name'] = 'Date';
$patch['name'] = 'Patches Deployed';

//while($row = mysqli_fetch_array($result)) {
foreach($sql as $row) {
	$date['data'][] = $row['EventDate'];
	$patch['data'][] = $row['Patching'];
}

$rslt = array();
	array_push($rslt, $date);
	array_push($rslt, $patch);
print json_encode($rslt, JSON_NUMERIC_CHECK);

?>
