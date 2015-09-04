<?php  require('../config/dbconnect.php');

$specScore = "SELECT `Client Specialist`, ROUND(AVG(`Antivirus`),1) AS 'Antivirus', ROUND(AVG(`Disk`),1) AS 'Disk', ROUND(AVG(`Intrusion`),1) AS 'Intrusion', ROUND(AVG(`Usability`),1) AS 'Usability', ROUND(AVG(`Services`),1) AS 'Services', ROUND(AVG(`Updates`),1) AS 'Updates', ROUND(AVG(`Event_Log`),1) AS 'Events', ROUND((COALESCE(antivirus,0) + COALESCE(DISK,0) + COALESCE(intrusion,0) + COALESCE(usability,0) + COALESCE(services,0) + COALESCE(updates,0) + COALESCE(event_log,0)) / (COALESCE(antivirus/antivirus,0) + COALESCE(DISK/DISK,0) + COALESCE(intrusion/intrusion,0) + COALESCE(usability/usability,0) + COALESCE(services/services,0) + COALESCE(updates/updates,0) + COALESCE(event_log/event_log,0)),1) AS 'Overall Score' FROM plugin_lthc_scores JOIN v_extradataclients v USING (clientid) GROUP BY v.`Client Specialist`";
$teamScore = "SELECT `Team_Assignment` as 'Team Assignment', ROUND(AVG(`Antivirus`),1) AS 'Antivirus', ROUND(AVG(`Disk`),1) AS 'Disk', ROUND(AVG(`Intrusion`),1) AS 'Intrusion', ROUND(AVG(`Usability`),1) AS 'Usability', ROUND(AVG(`Services`),1) AS 'Services', ROUND(AVG(`Updates`),1) AS 'Updates', ROUND(AVG(`Event_Log`),1) AS 'Events', ROUND((COALESCE(antivirus,0) + COALESCE(DISK,0) + COALESCE(intrusion,0) + COALESCE(usability,0) + COALESCE(services,0) + COALESCE(updates,0) + COALESCE(event_log,0)) / (COALESCE(antivirus/antivirus,0) + COALESCE(DISK/DISK,0) + COALESCE(intrusion/intrusion,0) + COALESCE(usability/usability,0) + COALESCE(services/services,0) + COALESCE(updates/updates,0) + COALESCE(event_log/event_log,0)),1) AS 'Overall Score' FROM plugin_lthc_scores WHERE `Team_Assignment` != '' AND `Team_Assignment` != 'None' GROUP BY `Team_Assignment`";


 ?>
<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
  $('#teamTable, #specTable').DataTable( {
    responsive: true,
    lengthChange: false,
    bPaginate: false,
    searching: false,
    jQueryUI: true,
    /*ordering: false,*/
    "aaSorting": [],
    buttons: true
    });
    var t = $('#teamTable').DataTable();
    t.buttons().container().insertBefore('#teamTable_wrapper');
    var s = $('#specTable').DataTable();
    s.buttons().container().insertBefore('#specTable_wrapper');
});
</script>

<h2>Team Score Summaries</h2>
<table id="teamTable" class="display hcScoreSummary compact" cellspacing="0" width="100%">
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
foreach($pdo->query($teamScore) as $t) {
echo "<tr>
    <td><a href='#'>".$t['Team Assignment']."</a></td>
    <td style='text-align:center;'>".$t['Overall Score']."</td>
    <td style='text-align:center;'>".$t['Antivirus']."</td>
    <td style='text-align:center;'>".$t['Disk']."</td>
    <td style='text-align:center;'>".$t['Intrusion']."</td>
    <td style='text-align:center;'>".$t['Usability']."</td>
    <td style='text-align:center;'>".$t['Services']."</td>
    <td style='text-align:center;'>".$t['Updates']."</td>
    <td style='text-align:center;'>".$t['Events']."</td>
  </tr>";
};
?>
</tbody>
</table>

<br><p><br><h2>Specialist Score Summaries</h2>
<table id="specTable" class="display hcScoreSummary compact" cellspacing="0" width="100%">
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
foreach($pdo->query($specScore) as $spec) {
echo "<tr>
      <td><a href='#'>".$spec['Client Specialist']."</a></td>
      <td style='text-align:center;'>".$spec['Overall Score']."</td>
      <td style='text-align:center;'>".$spec['Antivirus']."</td>
      <td style='text-align:center;'>".$spec['Disk']."</td>
      <td style='text-align:center;'>".$spec['Intrusion']."</td>
      <td style='text-align:center;'>".$spec['Usability']."</td>
      <td style='text-align:center;'>".$spec['Services']."</td>
      <td style='text-align:center;'>".$spec['Updates']."</td>
      <td style='text-align:center;'>".$spec['Events']."</td>
    </tr>";
};
?>
</tbody>
</table>
