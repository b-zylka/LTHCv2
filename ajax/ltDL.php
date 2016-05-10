<?php
require('../config/dbconnect.php');
require('../config/branding.php');
?>

<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
  $('#ltDL').dataTable({
    "scrollY": "500px",
    "scrollCollapse": true,
    searching: true,
    lengthChange: false,
    bPaginate: false,
    bInfo: true,
    jQueryUI: true
  });
});
</script>

<table id="ltDL" class="display compact" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th style="text-align:left;">Client</th>
      <th style="text-align:left;">Location</th>
      <th style="text-align:left;">Agent Download</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT c.name AS cName, l.name AS lName, l.locationid AS locationid, c.clientid AS clientid FROM locations l JOIN clients c USING (clientid)";
    foreach ($pdo->query($sql) as $locInfo) {
      echo "<tr>
      <td style='padding-left:10px;'><a href='clients.php?clientid=".$locInfo['clientid']. "'>" . $locInfo['cName'] . "</a></td>
      <td style='padding-left:10px;'><a href='locations.php?locationid=" . $locInfo['locationid'] . "'>" . $locInfo['lName'] . "</a></td>
      <td style='padding-left:10px;'><a href='https://".LTURL."/Labtech/Deployment.aspx?Probe=1&ID=" . $locInfo['locationid'] . "'>EXE</a> &#9900; <a href='https://".LTURL."/Labtech/Deployment.aspx?Probe=1&MSILocations=" . $locInfo['locationid'] . "'>MSI</a> &#9900; <a href='https://".LTURL."/Labtech/Deployment.aspx?PROBE=1&Linux=1&ID=" . $locInfo['locationid'] . "'>Linux</a> &#9900; <a href='https://".LTURL."/Labtech/Deployment.aspx?PROBE=1&Linux=2&ID=" . $locInfo['locationid'] . "'>OS X</a></td>
      </tr>";
    } ?>
    <!-- https://".LTURL."/labtech/deployment.aspx?PROBE=1&Linux=1&ID=1 -->
    <!--  &#9900; <a href='https://".LTURL."/Labtech/Deployment.aspx?PROBE=1&Linux=1&ID=" . $locInfo['locationid'] . "'>Linux</a> &#9900; <a href='https://".LTURL."/Labtech/Deployment.aspx?PROBE=1&Linux=2&ID=" . $locInfo['locationid'] . "'>OS X</a> -->
    <!-- https://".LTURL."/labtech/deployment.aspx?PROBE=2&Linux=2&ID=1 -->
  </tbody>
</table>
