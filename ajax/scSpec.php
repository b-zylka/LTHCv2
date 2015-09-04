<?php
require('../config/dbconnect.php');
  $sql = "SELECT
    `plugin_lthc_scores`.clientid as clientid,
  	client_name as 'Client Name',
  	team_assignment as 'Team Assignment',
  	`Client Specialist`,
  	ROUND(AVG(antivirus),1) AS Antivirus,
  	ROUND(AVG(DISK),1) AS Disk,
  	ROUND(AVG(intrusion),1) AS Intrusion,
  	ROUND(AVG(usability),1) AS Usability,
  	ROUND(AVG(services),1) AS Services,
  	ROUND(AVG(updates),1) AS Updates,
  	ROUND(AVG(`event_log`),1) AS Events,
  	ROUND((COALESCE(antivirus,0) + COALESCE(DISK,0) + COALESCE(intrusion,0) + COALESCE(usability,0) + COALESCE(services,0) + COALESCE(updates,0) + COALESCE(event_log,0)) /
  	(COALESCE(antivirus/antivirus,0) + COALESCE(DISK/DISK,0) + COALESCE(intrusion/intrusion,0) + COALESCE(usability/usability,0) + COALESCE(services/services,0) + COALESCE(updates/updates,0) + COALESCE(event_log/event_log,0)),1) AS 'Overall Score'
  FROM plugin_lthc_scores JOIN v_extradataclients USING(clientid) WHERE `v_extradataclients`.`Name` NOT LIKE '~%' GROUP BY client_name";

  $list_spec = "SELECT distinct `Client Specialist` as 'specialist' FROM v_extradataclients WHERE `Client Specialist` != 'None' order by specialist asc";
  $listTeamSql = "select distinct `Team_Assignment` as 'team' from plugin_lthc_scores where `Team_Assignment` != '' and `Team_Assignment` != 'None'";
 ?>
 <script src="js/jquery.dataTables.columnFilter.js" type="text/javascript"></script>
 <script type="text/javascript" language="javascript" class="init">
 $(document).ready(function() {
   $('#hcAllScores').DataTable( {
     scrollCollapse: true,
     scrollY: "400px",
     responsive: true,
     lengthChange: false,
     bPaginate: false,
     searching: true,
     jQueryUI: true,
     /*ordering: false,*/
     "aaSorting": [],
     buttons: true
     });
     var tEX = $('#hcAllScores').DataTable();
     tEX.buttons().container().insertBefore('#hcAllScores_filter');
 });
 $('#hcAllScores').dataTable().columnFilter({
   aoColumns:[
     null,
     { type: "select",
       values: [<?php foreach($pdo->query($list_spec) as $listSpec) {echo "'".$listSpec['specialist']."',"; }; ?>]
       },
     {sSelector: "#teamFilter",
       type: "select",
       values: [<?php foreach($pdo->query($listTeamSql) as $listTeam) {echo "'".$listTeam['team']."',"; }; ?>]
       },
     null,
     null,
     null,
     null,
     null,
     null,
     null,
     ]}
   );
 </script>

  <table id="hcAllScores" class="compact display" cellspacing="0" width="100%">
  <thead>
    <tr>
    <th>Client Name</th>
    <th>Specialist</th>
    <th>Team</th>
    <th><img src="images/icons/medal_gold_1.png" height="16" width="16"> Overall</th>
    <th><img src="images/icons/shield.png" height="16" width="16"> AV</th>
    <th><img src="images/icons/drive_magnify.png" height="16" width="16"> DSK</th>
    <th><img src="images/icons/status_busy.png" height="16" width="16"> INT</th>
    <th><img src="images/icons/chart_curve.png" height="16" width="16"> USB</th>
    <th><img src="images/icons/cog.png" height="16" width="16"> SRV</th>
    <th><img src="images/icons/package.png" height="16" width="16"> UPD</th>
    <th><img src="images/icons/chart_bar_error.png" height="16" width="16"> EVT</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
    <th>Client Name</th>
    <th>Specialist</th>
    <th>Team</th>
    <th>Overall</th>
    <th>AV</th>
    <th>DSK</th>
    <th>INT</th>
    <th>USB</th>
    <th>SRV</th>
    <th>UPD</th>
    <th>EVT</th>
    </tr>
  </tfoot>
  <tbody>
  <?php
  foreach($pdo->query($sql) as $row) {
    echo "<tr>
      <td><a href='clients.php?clientid=".$row['clientid']."'>".$row['Client Name']."</td>
      <td style='text-align:left;'>".$row['Client Specialist']."</td>
      <td style='text-align:left;'>".$row['Team Assignment']."</td>
      <td style='text-align:center;'>".$row['Overall Score']."</td>
      <td style='text-align:center;'>".$row['Antivirus']."</td>
      <td style='text-align:center;'>".$row['Disk']."</td>
      <td style='text-align:center;'>".$row['Intrusion']."</td>
      <td style='text-align:center;'>".$row['Usability']."</td>
      <td style='text-align:center;'>".$row['Services']."</td>
      <td style='text-align:center;'>".$row['Updates']."</td>
      <td style='text-align:center;'>".$row['Events']."</td>
    </tr>";
    };
  ?>
</tbody>
</table>
