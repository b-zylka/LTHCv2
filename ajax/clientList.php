<?php
//require('../config/dbconnect.php');
//require('../config/branding.php');

$cLsql = "select v.clientid as clientid, clients.externalid as recid, v.name as 'Client Name', `Team Assignment`, `Client Specialist`, `Managed IT - Active`, FORMAT(SUM(CASE WHEN os LIKE '%server%' THEN 1 ELSE 0 END),0) AS Servers, FORMAT(SUM(CASE WHEN os NOT LIKE '%server%' THEN 1 ELSE 0 END),0) AS Workstations from v_extradataclients v join computers using (clientid) join clients on v.clientid=clients.clientid where clients.name not like '~%' group by v.clientid";

?>

<script type="text/javascript" language='javascript' class="init">
$(document).ready(function() {
  $('#clientList').dataTable({
    "scrollCollapse": true,
    scrollY: "500px",
    lengthChange: false,
    bPaginate: false,
    "oLanguage": {
         "sInfo": "Showing _START_ to _END_ of _TOTAL_ clients"
     },
    jQueryUI: true,
    buttons: true
  });
  var cList = $('#clientList').DataTable();
  cList.buttons().container().insertBefore('#clientList_filter');
});
</script>

<h2>Current LabTech Client List</h2>
<table id="clientList" class="stripe compact" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th style='text-align: left; min-width: 200px;'><img src="../images/lt/client.gif" height="16" width="16"> Client Name</th>
      <th><img src="../images/icon2.png" height="16" width="16"> LT Status</th>
      <th style='text-align: center;'><img src="../images/icons/group.png" height="16" width="16"> Team</th>
      <th style='text-align: center;'><img src="../images/icons/group.png" height="16" width="16"> Client Specialist</th>
      <th><img src="../images/icons/server.png" height="16" width="16"> Servers</th>
      <th><img src="../images/icons/computer.png" height="16" width="16"> Workstations</th>
    </tr>
  </thead>
  <tbody>
  <?php
  foreach($pdo->query($cLsql) as $row) {
    if($row['Managed IT - Active'] == 1) {$status = "<strong style='color:green;'>Active</strong>";} else {$status = "<strong style='color:red;'>Inactive</strong>";};
    if($row['Team Assignment'] == "None") {$team = "";} else {$team = $row['Team Assignment'];};
    if($row['Client Specialist'] == "None") {$cs = "";} else {$cs = $row['Client Specialist'];};
    echo "<tr>
      <td><a href='clients.php?clientid=" . $row['clientid'] . "'>" . $row['Client Name'] . "</a></td>
      <td style='text-align:center;'>" . $status . "</td>
      <td style='text-align:center;'>" . $team . "</td>
      <td style='text-align: center;'>" . $cs . "</td>
      <td style='text-align: center;'>".$row['Servers']."</td>
      <td style='text-align: center;'>".$row['Workstations']."</td>
    </tr>";
      };
  ?>
  </tbody>
</table>
