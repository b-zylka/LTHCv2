<?php  require('../config/dbconnect.php');

$specScoreEx = "SELECT `Client Specialist`, ROUND(AVG(NULLIF(`AV_Ex`,0)),1) AS 'Antivirus', ROUND(AVG(NULLIF(`Disk_Ex`,0)),1) AS 'Disk', ROUND(AVG(NULLIF(`Intrusion_Ex`,0)),1) AS 'Intrusion',ROUND(AVG(NULLIF(`Usability_Ex`,0)),1) AS 'Usability', ROUND(AVG(NULLIF(`Services_Ex`,0)),1) AS 'Services', ROUND(AVG(NULLIF(`Updates_Ex`,0)),1) AS 'Updates', ROUND(AVG(NULLIF(`Event_Ex`,0)),1) AS 'Events', ROUND(((ROUND(AVG(NULLIF(`AV_Ex`,0)),1)+ROUND(AVG(NULLIF(`Disk_Ex`,0)),1)+ROUND(AVG(NULLIF(`Intrusion_Ex`,0)),1)+ROUND(AVG(NULLIF(`Usability_Ex`,0)),1)+ROUND(AVG(NULLIF(`Services_Ex`,0)),1)+ROUND(AVG(NULLIF(`Updates_Ex`,0)),1)+ROUND(AVG(NULLIF(`Event_Ex`,0)),1))/7),1) AS 'Overall Score' FROM plugin_lthc_scores JOIN v_extradataclients v USING (clientid) WHERE `Client Specialist` != 'None' AND plugin_lthc_scores.clientid NOT IN (SELECT clientid FROM v_plugin_lthc_ex_client) GROUP BY v.`Client Specialist`";
$teamScoreEx = "SELECT `Team_Assignment` as 'Team Assignment', ROUND(AVG(NULLIF(`AV_Ex`,0)),1) AS 'Antivirus', ROUND(AVG(NULLIF(`Disk_Ex`,0)),1) AS 'Disk', ROUND(AVG(NULLIF(`Intrusion_Ex`,0)),1) AS 'Intrusion', ROUND(AVG(NULLIF(`Usability_Ex`,0)),1) AS 'Usability', ROUND(AVG(NULLIF(`Services_Ex`,0)),1) AS 'Services', ROUND(AVG(NULLIF(`Updates_Ex`,0)),1) AS 'Updates', ROUND(AVG(NULLIF(`Event_Ex`,0)),1) AS 'Events', ROUND(((ROUND(AVG(NULLIF(`AV_Ex`,0)),1)+ROUND(AVG(NULLIF(`Disk_Ex`,0)),1)+ROUND(AVG(NULLIF(`Intrusion_Ex`,0)),1)+ROUND(AVG(NULLIF(`Usability_Ex`,0)),1)+ROUND(AVG(NULLIF(`Services_Ex`,0)),1)+ROUND(AVG(NULLIF(`Updates_Ex`,0)),1)+ROUND(AVG(NULLIF(`Event_Ex`,0)),1))/7),1) AS 'Overall Score' FROM plugin_lthc_scores WHERE plugin_lthc_scores.clientid NOT IN (SELECT clientid FROM v_plugin_lthc_ex_client) AND plugin_lthc_Scores.`Team_Assignment` != 'None' AND plugin_lthc_scores.`Team_Assignment` != '' GROUP BY plugin_lthc_scores.`Team_Assignment`";


 ?>
<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
  $('#teamExTable, #specExTable').DataTable( {
    responsive: true,
    lengthChange: false,
    bPaginate: false,
    searching: false,
    jQueryUI: true,
    /*ordering: false,*/
    "aaSorting": [],
    buttons: true
    });
    var tEX = $('#teamExTable').DataTable();
    tEX.buttons().container().insertBefore('#teamExTable_wrapper');
    var sEX = $('#specExTable').DataTable();
    sEX.buttons().container().insertBefore('#specExTable_wrapper');
});
</script>

<h2>Team Score Summaries | Exclusions Enabled</h2>
<table id="teamExTable" class="display hcScoreEX compact" cellspacing="0" width="100%">
<thead>
  <tr>
    <th style="text-align:left;">Team</th>
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
<tbody>
<?php
foreach($pdo->query($teamScoreEx) as $tex) {
echo "<tr>
    <td><a href='#'>".$tex['Team Assignment']."</a></td>
    <td style='text-align:center;'>".$tex['Overall Score']."</td>
    <td style='text-align:center;'>".$tex['Antivirus']."</td>
    <td style='text-align:center;'>".$tex['Disk']."</td>
    <td style='text-align:center;'>".$tex['Intrusion']."</td>
    <td style='text-align:center;'>".$tex['Usability']."</td>
    <td style='text-align:center;'>".$tex['Services']."</td>
    <td style='text-align:center;'>".$tex['Updates']."</td>
    <td style='text-align:center;'>".$tex['Events']."</td>
  </tr>";
};
?>
</tbody>
</table>

<br><p><br><h2>Specialist Score Summaries | Exclusions Enabled</h2>
<table id="specExTable" class="display hcScoreEX compact" cellspacing="0" width="100%">
<thead>
  <tr>
  <th style="text-align:left;">Specialist</th>
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
<tbody>
<?php
foreach($pdo->query($specScoreEx) as $specEx) {
echo "<tr>
      <td><a href='#'>".$specEx['Client Specialist']."</a></td>
      <td style='text-align:center;'>".$specEx['Overall Score']."</td>
      <td style='text-align:center;'>".$specEx['Antivirus']."</td>
      <td style='text-align:center;'>".$specEx['Disk']."</td>
      <td style='text-align:center;'>".$specEx['Intrusion']."</td>
      <td style='text-align:center;'>".$specEx['Usability']."</td>
      <td style='text-align:center;'>".$specEx['Services']."</td>
      <td style='text-align:center;'>".$specEx['Updates']."</td>
      <td style='text-align:center;'>".$specEx['Events']."</td>
    </tr>";
};
?>
</tbody>
</table>
