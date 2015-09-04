<?php
require('config/dbconnect.php');
/**
*   LabTech PHP/PDO API
*
*   Written by Brandon Zylka, 2015.
*
*		Example Usage:
*			$lt = new ltAPI();
*			$company = $lt->getCompany("1234");
*
*			foreach($company as $row {
*  			print "Company Name: " . $row['Name'] . "<br>";
*			}
*/

/* LabTech Clients */
class ltClient {

  function getCompany($clientid) {
    $sql = $pdo->prepare("SELECT * FROM clients JOIN v_extradataclients USING (clientid) WHERE clients.clientid = :clientid");
    $sql->execute(array('clientid' => $clientid));
    return $sql;
  }

  function getLocation($clientid) {
    $sql = $pdo->prepare("SELECT * FROM locations JOIN v_extradatalocations USING (locationid) WHERE locations.clientid = :clientid");
    $sql->execute(array('clientid' => $clientid));
    return $sql;
  }

  function getComputer($clientid) {
    $sql = $pdo->prepare("SELECT * FROM computers JOIN v_extradatacomputers USING (computerid) WHERE computers.clientid = :clientid");
    $sql->execute(array('clientid' => $clientid));
    return $sql;
  }

  function getLocationHealthScores($clientid) {
    $stmt = $pdo->prepare("SELECT locations.name as 'Location Name',locations.locationid, ROUND(AVG(`Antivirus`),1) AS 'Antivirus',ROUND(AVG(`Disk`),1) AS 'Disk',ROUND(AVG(`Intrusion`),1) AS 'Intrusion',ROUND(AVG(`Usability`),1) AS 'Usability',ROUND(AVG(`Services`),1) AS'Services',ROUND(AVG(`Updates`),1) AS 'Updates',ROUND(AVG(`Event_Log`),1) AS 'Events',ROUND(((ROUND(AVG(`Antivirus`),1)+ROUND(AVG(`Disk`),1)+ROUND(AVG(`Intrusion`),1)+ROUND(AVG(`Usability`),1)+ROUND(AVG(`Services`),1)+ROUND(AVG(`Updates`),1)+ROUND(AVG(`Event_Log`),1))/7),1) AS 'Overall Score' FROM plugin_lthc_scores_computers join computers using (computerid) left join locations using(locationid) where locations.clientid= :clientid GROUP BY locations.locationid");
    $stmt->execute(array('clientid' => $clientid));
    return $stmt;
  }
}

/* LabTech Locations */
class ltLocation {
  function getCompany($locationid) {
    $sql = $pdo->prepare("SELECT * FROM clients JOIN v_extradataclients USING (clientid) WHERE clients.clientid = (SELECT clientid from locations where locationid = :locationid)");
    $sql->execute(array('locationid' => $locationid));
    return $sql;
  }

  function getLocation($locationid) {
    $sql = $pdo->prepare("SELECT * FROM locations JOIN v_extradatalocations USING (locationid) WHERE locations.locationid = :locationid");
    $sql->execute(array('location' => $locationid));
    return $sql;
  }

  function getComputer($locationid) {
    $sql = $pdo->prepare("SELECT * FROM computers JOIN v_extradatacomputers USING (computerid) WHERE computers.locationid = :locationid");
    $sql->execute(array('locationid' => $locationid));
    return $sql;
  }

}

/* LabTech Computers */
class ltComputer {
  function getCompany($clientid) {
    $sql = $pdo->prepare("SELECT * FROM clients JOIN v_extradataclients USING (clientid) WHERE clients.clientid = :clientid");
    $sql->execute(array('clientid' => $clientid));
    return $sql;
  }

  function getLocation($locationid) {
    $sql = $pdo->prepare("SELECT * FROM locations JOIN v_extradatalocations USING (locationid) WHERE locations.locationid = :locationid");
    $sql->execute(array('locationid' => $locationid));
    return $sql;
  }

  function getComputer($computerid) {
    $sql = $pdo->prepare("SELECT * FROM computers JOIN v_extradatacomputers USING (computerid) WHERE computers.computerid = :computerid");
    $sql->execute(array('computerid' => $computerid));
    return $sql;
  }

  function getHealthScore($computerid) {
    $sql = $pdo->prepare("SELECT * FROM plugin_lthc_scores_computers WHERE computerid = :computerid");
    $sql->execute(array('computerid' => $computerid));
    return $sql;
  }
}


?>
