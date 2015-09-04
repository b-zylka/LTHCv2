<?php
require('dbconnect.php');

$computerid = $_REQUEST['computerid'];
$stat = $_REQUEST['stat'];

$sql14 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat14,6) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid={$computerid} AND INSTR(stat14,\";\") AND event14 <> -1 AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH)";
$sql15 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat15,8) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid={$computerid} AND INSTR(stat15,\";.,\") AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH)";
$sql16 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat16,6) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid={$computerid} AND (INSTR(stat16,\";|\") OR INSTR(stat16,\";\[\")) AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH)";
$sql17 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat17,6) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid={$computerid} AND stat17 < 1 AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH)";
$sql18 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat18,7) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid={$computerid} AND (INSTR(stat18,\";^\") OR INSTR(stat18,\";:\")) AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH)";
$sql19 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat19,6) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid={$computerid} AND INSTR(stat19,\";\") AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH)";
$sql20 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat20,7) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid={$computerid} AND INSTR(stat20,\";^\") AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH)";

if ($stat == '14') {
  $statLabel = 'Event Log';
  $statSQL = $sql14;
} elseif ($stat == '15') {
  $statLabel = 'Antivirus';
  $statSQL = $sql15;
} elseif ($stat == '16') {
  $statLabel = 'Disk';
  $statSQL = $sql16;
} elseif ($stat == '17') {
  $statLabel = 'Intrusion';
  $statSQL = $sql17;
} elseif ($stat == '18') {
  $statLabel = 'Usability';
  $statSQL = $sql18;
} elseif ($stat == '19') {
  $statLabel = 'Services';
  $statSQL = $sql19;
} elseif ($stat == '20') {
  $statLabel = 'Updates';
  $statSQL = $sql20;
} else {
  $statLabel = 'Stat Undefined';
};

$result = mysqli_query($conn,$statSQL);
  while($row = mysqli_fetch_array($result)) {
    $nestedData=array();

    $nestedData[] = $row['Computer Name'];
    $nestedData[] = $row['Event Date'];
    $nestedData[] = $row['Details'];
    $data[] = $nestedData;
}
$json_data = array("data" => $data);
echo json_encode($json_data);
?>
