<?php

require('../config/dbconnect.php');

//Overal Health score per team in array as "Teameam" and "TotScore"
	$sql = "SELECT `Client_Name` as 'Client Name', `Team_Assignment` as 'Team Assignment', `Antivirus`, `Disk`, `Intrusion`, `Usability`, `Services`, `Updates`, `Event_Log` as 'Event Log', `Avg_Score` as 'Avg Score', WEEK(CheckDate) AS 'CheckDate' FROM plugin_lthc_scores_weekly;";
	//$result = mysqli_query($conn,$sql);
		//while($row = mysqli_fetch_array($result)) {
		foreach($pdo->query($sql) as $row) {
			$scores[] = array(
				'Client' => $row['Client Name'],
				'Team' => $row['Team Assignment'],
				'Antivirus' => $row['Antivirus'],
				'Disk' => $row['Disk'],
				'Intrusion' => $row['Intrusion'],
				'Usability' => $row['Usability'],
				'Services' => $row['Services'],
				'Updates' => $row['Updates'],
				'EventLog' => $row['Event Log'],
				'AvgScore' => $row['Avg Score'],
				'CheckDate' => $row['CheckDate']
				);
		}
	echo json_encode($scores);
?>
