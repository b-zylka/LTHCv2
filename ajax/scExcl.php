<?php  require('../config/dbconnect.php'); ?>
<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
$('#coEX, #clEX').DataTable( {
  "scrollY": "500px",
  "scrollCollapse": true,
  responsive: false,
  lengthChange: true,
  bPaginate: false,
  searching: true,
  jQueryUI: true,
  /*ordering: false,*/
  "aaSorting": [],
  buttons: true
  });
  var coEX = $('#coEX').DataTable();
  var clEX = $('#clEX').DataTable();
  coEX.buttons().container().insertBefore('#coEX_filter');
  clEX.buttons().container().insertBefore('#clEX_filter');
});
</script>

<h2>Client Score Exclusions</h2>
<table id="clEX" class="stripe compact" cellspacing="0" width="100%">
<thead>
  <tr>
    <th style="text-align:left;">Client Name</th>
    <th>Team</th>
    <th>Exclude All</th>
    <th>AV</th>
    <th>DSK</th>
    <th>INT</th>
    <th>USB</th>
    <th>SRV</th>
    <th>UPD</th>
    <th>EVT</th>
    <th style="min-width:100px;">Go Live Date</th>
    <th style="text-align:left;">Comments</th>
  </tr>
</thead>
<tbody>
<?php
$clSQL = "select * from v_plugin_lthc_ex_client";
foreach($pdo->query($clSQL) as $cl) {
  if($cl['Exclude Reporting'] == '1') {$all = "<img src='images/cancel_256.png' height='16' width='16' alt='excluded'>";} else {$all = NULL;};
  if($cl['Ignore Antivirus'] == '1') {$av = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$av = NULL;};
  if($cl['Ignore Disk'] == '1') {$dsk = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$dsk = NULL;};
  if($cl['Ignore Intrusion'] == '1') {$int = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$int = NULL;};
  if($cl['Ignore Usability'] == '1') {$usb = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$usb = NULL;};
  if($cl['Ignore Services'] == '1') {$srv = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$srv = NULL;};
  if($cl['Ignore Updates'] == '1') {$upd = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$upd = NULL;};
  if($cl['Ignore Event Log'] == '1') {$ev = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$ev = NULL;};
  if($cl['Go Live Date'] == '0000-00-00') {$gl = NULL;} else {$gl = $cl['Go Live Date'];};
echo "<tr>
      <td><a href='clients.php?clientid=".$cl['ClientID']."'>".$cl['Client Name']."</a></td>
      <td style='text-align:center;'>".$cl['Team Assignment']."</td>
      <td style='text-align:center;'>".$all."</td>
      <td style='text-align:center;'>".$av."</td>
      <td style='text-align:center;'>".$dsk."</td>
      <td style='text-align:center;'>".$int."</td>
      <td style='text-align:center;'>".$usb."</td>
      <td style='text-align:center;'>".$srv."</td>
      <td style='text-align:center;'>".$upd."</td>
      <td style='text-align:center;'>".$ev."</td>
      <td style='text-align:center;'>".$gl."</td>
      <td style='text-align:left;'>".$cl['Exclusion Comments']."</td>
    </tr>";
};
?>
</tbody>
</table>

<br><p><br><h2>Computer Score Exclusions</h2>
<table id="coEX" class="stripe compact" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th>Client Name</th>
      <th>Team</th>
      <th>Computer Name</th>
      <th>AV</th>
      <th>DSK</th>
      <th>INT</th>
      <th>USB</th>
      <th>SRV</th>
      <th>UPD</th>
      <th>EVT</th>
      <th style="text-align:left;">Comments</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $clSQL = "select * from v_plugin_lthc_ex_computer";
  foreach($pdo->query($clSQL) as $co) {
    if($co['Ignore Antivirus'] == '1') {$cav = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$cav = NULL;};
    if($co['Ignore Disk'] == '1') {$cdsk = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$cdsk = NULL;};
    if($co['Ignore Intrusion'] == '1') {$cint = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$cint = NULL;};
    if($co['Ignore Usability'] == '1') {$cusb = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$cusb = NULL;};
    if($co['Ignore Services'] == '1') {$csrv = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$csrv = NULL;};
    if($co['Ignore Updates'] == '1') {$cupd = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$cupd = NULL;};
    if($co['Ignore Event Logs'] == '1') {$cev = "<img src='images/cancel_256.png' height='16' width='16'>";} else {$cev = NULL;};
  echo "<tr>
        <td>".$co['Client Name']."</a></td>
        <td style='text-align:center;'>".$co['Team Assignment']."</td>
        <td style='text-align:center;'>".$co['Computer Name']."</td>
        <td style='text-align:center;'>".$cav."</td>
        <td style='text-align:center;'>".$cdsk."</td>
        <td style='text-align:center;'>".$cint."</td>
        <td style='text-align:center;'>".$cusb."</td>
        <td style='text-align:center;'>".$csrv."</td>
        <td style='text-align:center;'>".$cupd."</td>
        <td style='text-align:center;'>".$cev."</td>
        <td style='text-align:left;'>".$co['Exclusion Comments']."</td>
      </tr>";
  };
  ?>
  </tbody>
  </table>
