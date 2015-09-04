<?php
require('../config/dbconnect.php');
header("Content-Type: application/json");

// ID: 1 = Computer; 2 = Location; 3 = Clients; 4 = Network Devices

$id = $_REQUEST['id'];

switch ($id) {
  case '1':
    $sql = "SELECT c.computerid as computerid, c.locationid, c.clientid, c.name as name, cl.name as cName, l.name as lName FROM computers c join locations l using(locationid) join clients cl on cl.clientid = c.clientid order by name asc";
    break;

  case '2':
    $sql = "SELECT l.locationid, l.clientid, l.name as name, clients.name as cName, l.city, l.state FROM locations l join clients using (clientid) order by l.name asc";
    break;

  case '3':
    $sql = "SELECT clientid, name, city, state FROM clients WHERE name NOT LIKE '~%' order by name asc";
    break;

  default:
    throw new Exception("Invalid ID", 1);
    break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$results=$stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
?>
