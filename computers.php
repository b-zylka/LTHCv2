<?php
require('config/dbconnect.php');
require('config/branding.php');
require_once('classes/Main.php');
$mainClass = new Main();

 $computerid = $_REQUEST['computerid'];

 $getInfo = $pdo->prepare("SELECT l.name as 'lName', l.locationid as 'locationid', c.name as 'cName', c.clientid as 'clientid', com.name as 'computerName' from locations l join clients c on c.clientid=l.clientid join computers com on com.locationid=l.locationid WHERE computerid = :computerid");
 $getInfo->execute(array('computerid' => $computerid));
foreach($getInfo as $thisInfo) {
  $clientName = $thisInfo['cName'];
  $locationName = $thisInfo['lName'];
  $computerName = $thisInfo['computerName'];
  $clientid = $thisInfo['clientid'];
  $locationid = $thisInfo['locationid'];
};

$stmt = $pdo->prepare('SELECT * FROM v_extradataclients WHERE clientid = :clientid');
$stmt->execute(array('clientid' => $clientid));
foreach($stmt as $vcInfo) {
  $clientSpecialist = $vcInfo['Client Specialist'];
  $teamName = $vcInfo['Team Assignment'];
}

$tixResult = $pdo->prepare("SELECT computers.computerid as computerid, computers.name as 'Computer Name', infocategory.categoryname AS Category, IF(INSTR(SUBJECT,\":\"),CONCAT(RIGHT(LEFT(SUBJECT,INSTR(SUBJECT,\":\")-1),200)), tickets.subject) AS 'Subject', COUNT(tickets.category) AS times FROM tickets JOIN computers USING (computerid) LEFT JOIN clients ON tickets.clientid=clients.clientid JOIN infocategory ON infocategory.id=tickets.category WHERE tickets.starteddate > DATE_ADD(NOW(),INTERVAL - 30 DAY) AND computers.computerid= :computerid GROUP BY tickets.subject ORDER BY times DESC LIMIT 10;");
$tixResult->execute(array('computerid' => $computerid));

$tixlatResult = $pdo->prepare("SELECT c.computerid as computerid, c.name as name, IF(INSTR(SUBJECT,\":\"),CONCAT(RIGHT(LEFT(SUBJECT,INSTR(SUBJECT,\":\")-1),200)), tickets.subject) AS 'subject', ticketstatus.ticketstatus as 'Status', starteddate as 'Start', requestoremail as 'Who', externalid, tickets.locationid, ticketpriority.name as 'Priority' FROM tickets JOIN computers c USING (computerid) JOIN infocategory ON infocategory.id=tickets.category  JOIN ticketstatus ON ticketstatusid=tickets.status JOIN ticketpriority ON ticketpriority.priority=tickets.priority WHERE starteddate > DATE_ADD(NOW(), INTERVAL -30 DAY) AND c.computerid = :computerid ORDER BY starteddate DESC LIMIT 50;");
$tixlatResult->execute(array('computerid' => $computerid));

/* Score Detail Table Queries*/
$sql14 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat14,6) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid=:computerid AND INSTR(stat14,\";\") AND stat14 <> -1 AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH) order by eventdate desc";
$sql15 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat15,8) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid=:computerid AND INSTR(stat15,\";.,\") AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH) order by eventdate desc";
$sql16 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat16,6) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid=:computerid AND (INSTR(stat16,\";|\") OR INSTR(stat16,\";\[\")) AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH) order by eventdate desc";
$sql17 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat17,6) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid=:computerid AND stat17 < 1 AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH) order by eventdate desc";
$sql18 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat18,7) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid=:computerid AND (INSTR(stat18,\";^\") OR INSTR(stat18,\";:\") OR INSTR(stat18,\";|\")) AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH) order by eventdate desc";
$sql19 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat19,6) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid=:computerid AND INSTR(stat19,\";\") AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH) order by eventdate desc";
$sql20 = "SELECT computerid, computers.name as 'Computer Name', eventdate as 'Event Date', SUBSTRING(stat20,7) as 'Details' FROM h_extrastatsdaily JOIN computers USING (computerid) WHERE computers.computerid=:computerid AND INSTR(stat20,\";^\") AND eventdate > DATE_ADD(NOW(), INTERVAL -1 MONTH) order by eventdate desc";

$hcDetail14 = $pdo->prepare($sql14);
$hcDetail14->execute(array('computerid' => $computerid));
$hcDetail15 = $pdo->prepare($sql15);
$hcDetail15->execute(array('computerid' => $computerid));
$hcDetail16 = $pdo->prepare($sql16);
$hcDetail16->execute(array('computerid' => $computerid));
$hcDetail17 = $pdo->prepare($sql17);
$hcDetail17->execute(array('computerid' => $computerid));
$hcDetail18 = $pdo->prepare($sql18);
$hcDetail18->execute(array('computerid' => $computerid));
$hcDetail19 = $pdo->prepare($sql19);
$hcDetail19->execute(array('computerid' => $computerid));
$hcDetail20 = $pdo->prepare($sql20);
$hcDetail20->execute(array('computerid' => $computerid));
?>

<!DOCTYPE html>
<html>
 <head>
 <title><?php echo APP_TITLE . " | " . $computerName; ?></title>

 <?php $mainClass->buildTemplate('head'); ?>

 <script type="text/javascript" language="javascript" class="init">
 $(document).ready(function() {
   $("#tabs").tabs( {
      "activate": function(event, ui) {
            $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
        }
   });
   $("#sdTabs").tabs({
     "activate": function(event, ui) {
           $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
       }
   });
   /*$('table.display').dataTable( {*/
     $('#compInventory').dataTable({
    		"sScrollY": "250px",
    		"bScrollCollapse": true,
    		"bPaginate": false,
    		"bJQueryUI": true,
        "sDom":'<"H"lfr<"invTitle">>t<"F"ip>'
  });
   $('#locationList').dataTable({
     "scrollY": "120px",
     "scrollCollapse": true,
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: false
   });
   $('#tixTopCat').dataTable({
     "scrollCollapse": true,
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: true,
     "order": [[ 3, "desc" ]]
   });
   $('#tixNew').dataTable({
     "scrollCollapse": true,
     "scrollY": "300px",
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: true,
     "order": [[ 5, "desc" ]]
   });
   $('table.hcDetail').dataTable({
     "scrollCollapse": true,
     "scrollY": "300px",
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: true,
     "order": [[ 1, "desc" ]]
   });
 });
 </script>
</head>

<body>
<!-- Navbar -->

<!-- Header -->
<div id="mainHeader">
  <div id="mainHeaderContent">
  </div>
</div>
<div id="logo"></div>
<div id="navLink">
  <h5><a class='linkNav' href='index.php'>Home</a> > <a class='linkNav' href='main.php'>Clients</a> > <a class='linkNav' href='clients.php?clientid=<?php echo $clientid; ?>'><?php echo $clientName; ?></a> > <a class='linkNav' href='locations.php?locationid=<?php echo $locationid; ?>'><?php echo $locationName; ?></a> > <a class='linkNav' href='computers.php?computerid=<?php echo $computerid; ?>'><?php echo $computerName . " (" . $computerid . ")"; ?></a></h5>
</div>

<!-- Content -->
<div id="mainWrapper">
<div id="main-Header" class="ui-widget-header ui-corner-all" style="height: auto !important;">
  <h2><?php echo "Computer:  " . $computerName . " (" . $computerid . ")"; ?></h2>
</div>
  <div id="main-top">
    <div id="main-top-left" class="ui-widget">
      <div class="main-top-header ui-widget-header ui-helper-clearfix ui-corner-top">
        Client Information
      </div>
      <div class="ui-widget-content ui-corner-bottom" style="height:160px;">
        <table class="noBorder" style="padding: 5px;">
          <tbody>
            <tr>
              <td><strong>Client Name:</strong></td>
              <td style="padding-left: 10px;"><?php echo $clientName; ?></td>
            </tr>
            <tr>
              <td><strong>Location Name:</strong></td>
              <td style="padding-left: 10px;"><?php echo $locationName; ?></td>
            </tr>
            <tr>
              <td><strong>Client ID:</strong></td>
              <td style="padding-left: 10px;"><?php echo "<a href='labtech:open?clientid=" . $clientid . "'>" . $clientid . "</a>"; ?></td>
            </tr>
            <tr>
              <td><strong>Location ID:</strong></td>
              <td style="padding-left: 10px;"><?php echo "<a href='labtech:open?locationid=" . $locationid . "'>" . $locationid . "</a>"; ?></td>
            </tr>
            <tr><td>&nbsp;</td><td></td>
            </tr>
            <tr>
              <td><strong>Team Assignment:</strong></td>
              <td style="padding-left: 10px;"><?php echo $teamName; ?></td>
            </tr>
            <tr>
              <td><strong>Client Specialist:</strong></td>
              <td style="padding-left: 10px;"><?php echo $clientSpecialist; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div id="main-top-right" class="ui-widget">
      <!--<div class="main-top-header ui-widget-header ui-helper-clearfix ui-corner-top">
        Computer Stats
      </div>-->
      <div class="ui-widget-content ui-corner-bottom" style="height:190px;">
        <div id="hcCPU" style="min-width: 100px; height: 190px; margin: 0 auto"></div>
      </div>
    </div>
  </div>

<!-- Create icons on the tabs below -->
  <div id="main-bottom" class="ui-widget">
   <div id="tabs">
    <ul>
      <li><a href="#tabCompInfo"><img src="images/lt/comp.gif" height="16" width="16"> Computer Info</a></li>
      <li><a href="#tabCompScores"><img src="images/icons/bricks.png" height="16"> Health Scores</a></li>
      <li><a href="#tabScoreDetail"><img src="images/icons/application_view_detail.png" height="16" width="16"> Score Details</a></li>
      <li><a href="#tabTopComp"><img src="images/icons/tag_orange.png" height="16" width="16"> Latest Tickets</a></li>
      <li><a href="#tabTopAlert"><img src="images/icons/error.png" height="16" width="16"> Top Recurring Alerts (30 Days)</a></li>
    </ul>
    <div id="tabCompInfo" style="height:375px;">
      <?php
      /*Get computer table info*/
      $stmt = $pdo->prepare("SELECT IF(lastcontact > DATE_ADD(NOW(), INTERVAL - 15 MINUTE), 'online', 'offline') AS status FROM computers WHERE computerid = :computerid");
      $stmt->execute(array('computerid' => $computerid));
      foreach($stmt as $row) {
        if($row['status'] == 'online') {$onlineStatus = "<strong style='color:green;'>Online</strong>";} else {$onlineStatus = "<strong style='color:red'>Offline</strong>";};
      }

      $stmt = $pdo->prepare("SELECT * from computers join v_extradatacomputers using(computerid) where computerid = :computerid");
      $stmt->execute(array('computerid' => $computerid));
      foreach($stmt as $compInfo) {
        $vssql = "SELECT v.name as 'vname', date(c.virusdefs) as 'vdefs' from computers c join virusscanners v on c.virusscanner=v.vscanid where computerid = :computerid";
        if($compInfo['VirusScanner'] == 0) {$vscanner = "Not Detected"; $vdefs = "N/A";
          } else {
            $stmt = $pdo->prepare($vssql);
            $stmt->execute(array('computerid' => $computerid));
            foreach ($stmt as $vs) {
              $vscanner = $vs['vname'];
              $vdefs = $vs['vdefs'];};
            }
       ?>
       <div id="comLeft" class='twocol' style="float:left;">
         <div id="compLTStatus" class="ui-widget" style="width:400px;padding-bottom:10px;">
           <div class="ui-widget-header ui-corner-top" style="padding:3px;">
             LabTech Status
           </div>
           <div class="ui-widget-content ui-corner-bottom">
             <table class="noBorder" style="padding: 5px;">
               <tbody>
                 <tr>
                   <td style='width:150px;'><strong>Status:</strong></td>
                   <td style="padding-left: 10px;"><?php echo $onlineStatus; ?></td>
                 </tr>
                 <tr>
                   <td><strong>Onboarding:</strong></td>
                   <td style="padding-left: 10px;"><?php if($compInfo['Onboarding Complete'] = 1) {echo "<strong style='color:green;'>Complete</strong>";} else {echo "<strong style='color:red'>Incomplete</strong>";}; ?></td>
                 </tr>
                 <tr>
                   <td><strong>Computer ID:</strong></td>
                   <td style="padding-left: 10px;"><?php echo "<a href='labtech:open?computerid=" . $computerid . "'>" . $computerid . "</a>"; ?></td>
                 </tr>
                 <tr>
                   <td><strong>First Check In:</strong></td>
                   <td style="padding-left: 10px;"><?php $dt = new DateTime($compInfo['DateAdded']); echo $dt->format('Y-m-d'); ?></td>
                 </tr>
                 <tr>
                   <td><strong>VNC Policy:</strong></td>
                   <td style="padding-left: 10px;"><?php echo $compInfo['Access Control']; ?></td>
                 </tr>
                 <tr>
                   <td><strong>Remote Policy:</strong></td>
                   <td style="padding-left: 10px;"><?php echo $compInfo['Remote Control Policy']; ?></td>
                 </tr>
               </tbody>
             </table>
           </div>
         </div>
         <div id="secInfo" class="ui-widget" style="width:400px;padding-bottom:10px;">
           <div class="ui-widget-header ui-corner-top" style="padding:3px;">
             Security Information
           </div>
           <div class="ui-widget-content ui-corner-bottom">
             <table class="noBorder" style="padding: 5px;">
               <tbody>
                 <tr>
                   <td style='width:150px;'><strong>Windows Update:</strong></td>
                   <td style="padding-left: 10px;"><?php  if(strtotime($compInfo['WindowsUpdate'])<strtotime('-1 Months')) {echo "<strong style='color:red;'>" . $compInfo['WindowsUpdate'] . "</strong>";} else {echo $compInfo['WindowsUpdate'];};; ?></td>
                 </tr>
                 <tr><td>&nbsp;</td><td></td></tr>
                 <tr>
                   <td><strong>Purchase Date:</strong></td>
                   <td style="padding-left: 10px;"><?php echo $compInfo['MSP - Manufacturer Date']; ?></td>
                 </tr>
                 <tr>
                   <td><strong>Warranty Expiration:</strong></td>
                   <td style="padding-left: 10px;"><?php if(strtotime($compInfo['MSP - Warranty Expiration'])<strtotime('+3 Months')) {echo "<strong style='color:red;'>" . $compInfo['MSP - Warranty Expiration'] . "</strong>";} else {echo $compInfo['MSP - Warranty Expiration'];}; ?></td>
                 </tr>
                 <tr><td>&nbsp;</td><td></td></tr>
                 <tr>
                   <td><strong>Antivirus Detected:</strong></td>
                   <td style="padding-left: 10px;"><?php echo $vscanner; ?></td>
                 </tr>
                 <tr>
                   <td><strong>Antivirus Definitions:</strong></td>
                   <td style="padding-left: 10px;"><?php echo $vdefs;} ?></td>
                 </tr>
               </tbody>
             </table>
           </div>
         </div>
       </div>
     <div id="comright" class='twocol' style="float:left;">
       <!--get this to move to the right -->
       <div id="compStatus" class="ui-widget" style="width:400px;padding-bottom:10px;">
         <div class="ui-widget-header ui-corner-top" style="padding:3px;">
           Computer Information
         </div>
         <div class="ui-widget-content ui-corner-bottom">
           <table class="noBorder" style="padding: 5px;">
             <tbody>
               <tr>
                 <td style='width:150px;'><strong>Computer Name:</strong></td>
                 <td style="padding-left: 10px;"><?php echo "<a href='labtech:open?computerid=" . $computerid . "'>" . $computerName . "</a>"; ?></td>
               </tr>
               <tr>
                 <td><strong>OS:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['OS']; ?></td>
               </tr>
               <tr>
                 <td><strong>OS Version:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['Version']; ?></td>
               </tr>
               <tr>
                 <td><strong>Manufacturer:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['BiosMFG'] . " " . $compInfo['BiosFlash']; ?></td>
               </tr>
               <tr>
                 <td><strong>Serial Number:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['BiosVer']; ?></td>
               </tr>
               <tr><td>&nbsp;</td><td></td></tr>
               <tr>
                 <td><strong>Uptime:</strong></td>
                 <td style="padding-left: 10px;"><?php if($compInfo['UpTime'] > 1440) {$uptime=round($compInfo['UpTime']/1440,2); echo $uptime . " Days";} else {echo $compInfo['UpTime'] . " Minutes";}; ?></td>
               </tr>
               <tr>
                 <td><strong>Total Memory:</strong></td>
                 <td style="padding-left: 10px;"><?php if($compInfo['TotalMemory'] > 1040) {$memory=round($compInfo['TotalMemory']/1024,2); echo $memory . " GB";} else {echo $compInfo['TotalMemory'] . " MB";}; ?></td>
               </tr>
               <tr>
                 <td><strong>Current User:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['Username']; ?></td>
               </tr>
               <tr>
                 <td><strong>Last User:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['LastUsername']; ?></td>
               </tr>
             </tbody>
           </table>
         </div>
       </div>
       <div id="netInfo" class="ui-widget" style="width:400px;padding-bottom:10px;">
         <div class="ui-widget-header ui-corner-top" style="padding:3px;">
           Network Information
         </div>
         <div class="ui-widget-content ui-corner-bottom">
           <table class="noBorder" style="padding: 5px;">
             <tbody>
               <tr>
                 <td style='width:150px;'><strong>Domain:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['Domain']; ?></td>
               </tr>
               <tr>
                 <td><strong>Local IP Address:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['LocalAddress']; ?></td>
               </tr>
               <tr>
                 <td><strong>Public IP Address:</strong></td>
                 <td style="padding-left: 10px;"><?php echo $compInfo['RouterAddress']; ?></td>
               </tr>
             </tbody>
           </table>
         </div>
       </div>
     </div>
    </div>
    <div id="tabCompScores" style="height:300px;">
      <?php
      $pb = $pdo->prepare("SELECT * FROM plugin_lthc_scores_computers WHERE computerid = :computerid");
      $pb->execute(array('computerid' => $computerid));
      foreach($pb as $v) {
        $pbAV = $v['Antivirus'];
        $pbDSK = $v['Disk'];
        $pbINT = $v['Intrusion'];
        $pbUSB = $v['Usability'];
        $pbSRV = $v['Services'];
        $pbUPD = $v['Updates'];
        $pbEV = $v['Event_Log'];
        $pbAVG = $v['Avg_Score'];
      }

      $pbc = $pdo->prepare("SELECT * FROM plugin_lthc_scores WHERE clientid = :clientid");
      $pbc->execute(array('clientid' => $clientid));
      foreach($pbc as $v) {
        $pbcAV = $v['Antivirus'];
        $pbcDSK = $v['Disk'];
        $pbcINT = $v['Intrusion'];
        $pbcUSB = $v['Usability'];
        $pbcSRV = $v['Services'];
        $pbcUPD = $v['Updates'];
        $pbcEV = $v['Event_Log'];
        $pbcAVG = $v['Avg_Score'];
      }
       ?>
       <div id="comSLeft" class='twocolp' style="float:left;">
         <div id="compLTStatus" class="ui-widget" style="width:420px;padding-bottom:10px;">
           <div class="ui-widget-header ui-corner-top" style="padding:3px;">
             Computer Scores
           </div>
           <div class="ui-widget-content ui-corner-bottom">
            <table class="noBorder" style="padding: 5px;">
              <tbody>
                <tr>
                  <td style="width: 80px;">Antivirus:</td>
                  <td style="padding-left: 10px; width:325px;;"><div id="pbAV"><div class="progress-label"><?php if(!$pbAV) {echo "No Score";} else {echo $pbAV . "%";}; ?></div></div></td>
                </tr>
                <tr>
                  <td>Disk:</td>
                  <td style="padding-left: 10px; width:auto;"><div id="pbDSK"><div class="progress-label"><?php if(!$pbDSK) {echo "No Score";} else {echo $pbDSK . "%";}; ?></div></div></td>
                </tr>
                <tr>
                  <td>Intrusion:</td>
                  <td style="padding-left: 10px; width:auto;"><div id="pbINT"><div class="progress-label"><?php if(!$pbINT) {echo "No Score";} else {echo $pbINT . "%";}; ?></div></div></td>
                </tr>
                <tr>
                  <td>Usability:</td>
                  <td style="padding-left: 10px; width:auto;"><div id="pbUSB"><div class="progress-label"><?php if(!$pbUSB) {echo "No Score";} else {echo $pbUSB . "%";}; ?></div></div></td>
                </tr>
                <tr>
                  <td>Services:</td>
                  <td style="padding-left: 10px; width:auto;"><div id="pbSRV"><div class="progress-label"><?php if(!$pbSRV) {echo "No Score";} else {echo $pbSRV . "%";}; ?></div></div></td>
                </tr>
                <tr>
                  <td>Updates:</td>
                  <td style="padding-left: 10px; width:auto;"><div id="pbUPD"><div class="progress-label"><?php if(!$pbUPD) {echo "No Score";} else {echo $pbUPD . "%";}; ?></div></div></td>
                </tr>
                <tr>
                  <td>Event Log:</td>
                  <td style="padding-left: 10px; width:auto;"><div id="pbEV"><div class="progress-label"><?php if(!$pbEV) {echo "No Score";} else {echo $pbEV . "%";}; ?></div></div></td>
                </tr>
                <tr>
                  <td>Overall:</td>
                  <td style="padding-left: 10px; width:auto;"><div id="pbAVG"><div class="progress-label"><?php if(!$pbAVG) {echo "No Score";} else {echo $pbAVG . "%";}; ?></div></div></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div id="comSRight" class='twocolp' style="float:left;padding-left:17px;">
        <div id="compLTStatus" class="ui-widget" style="width:420px;padding-bottom:10px;">
          <div class="ui-widget-header ui-corner-top" style="padding:3px;">
            Client Scores
          </div>
          <div class="ui-widget-content ui-corner-bottom">
           <table class="noBorder" style="padding: 5px;">
             <tbody>
               <tr>
                 <td style="width: 80px;">Antivirus:</td>
                 <td style="padding-left: 10px; width:325px;;"><div id="pbcAV"><div class="progress-label"><?php if(!$pbcAV) {echo "No Score";} else {echo $pbcAV . "%";}; ?></div></div></td>
               </tr>
               <tr>
                 <td>Disk:</td>
                 <td style="padding-left: 10px; width:auto;"><div id="pbcDSK"><div class="progress-label"><?php if(!$pbcDSK) {echo "No Score";} else {echo $pbcDSK . "%";}; ?></div></div></td>
               </tr>
               <tr>
                 <td>Intrusion:</td>
                 <td style="padding-left: 10px; width:auto;"><div id="pbcINT"><div class="progress-label"><?php if(!$pbcINT) {echo "No Score";} else {echo $pbcINT . "%";}; ?></div></div></td>
               </tr>
               <tr>
                 <td>Usability:</td>
                 <td style="padding-left: 10px; width:auto;"><div id="pbcUSB"><div class="progress-label"><?php if(!$pbcUSB) {echo "No Score";} else {echo $pbcUSB . "%";}; ?></div></div></td>
               </tr>
               <tr>
                 <td>Services:</td>
                 <td style="padding-left: 10px; width:auto;"><div id="pbcSRV"><div class="progress-label"><?php if(!$pbcSRV) {echo "No Score";} else {echo $pbcSRV . "%";}; ?></div></div></td>
               </tr>
               <tr>
                 <td>Updates:</td>
                 <td style="padding-left: 10px; width:auto;"><div id="pbcUPD"><div class="progress-label"><?php if(!$pbcUPD) {echo "No Score";} else {echo $pbcUPD . "%";}; ?></div></div></td>
               </tr>
               <tr>
                 <td>Event Log:</td>
                 <td style="padding-left: 10px; width:auto;"><div id="pbcEV"><div class="progress-label"><?php if(!$pbcEV) {echo "No Score";} else {echo $pbcEV . "%";}; ?></div></div></td>
               </tr>
               <tr>
                 <td>Overall:</td>
                 <td style="padding-left: 10px; width:auto;"><div id="pbcAVG"><div class="progress-label"><?php if(!$pbcAVG) {echo "No Score";} else {echo $pbcAVG . "%";}; ?></div></div></td>
               </tr>
             </tbody>
           </table>
         </div>
       </div>
     </div>

    </div>
    <div id="tabScoreDetail">
      <div id="sdTabs">
        <ul>
          <li><a href="#sdTabAV"><img src="images/icons/shield.png" height="16" width="16"> Antivirus</a></li>
          <li><a href="#sdTabDSK"><img src="images/icons/drive_magnify.png" height="16" width="16"> Disk</a></li>
          <li><a href="#sdTabINT"><img src="images/icons/status_busy.png" height="16" width="16"> Intrusion</a></li>
          <li><a href="#sdTabUSB"><img src="images/icons/chart_curve.png" height="16" width="16"> Usability</a></li>
          <li><a href="#sdTabSRV"><img src="images/icons/cog.png" height="16" width="16"> Services</a></li>
          <li><a href="#sdTabUPD"><img src="images/icons/package.png" height="16" width="16"> Updates</a></li>
          <li><a href="#sdTabEV"><img src="images/icons/chart_bar_error.png" height="16" width="16"> Event Log</a></li>
        </ul>
        <div id="sdTabAV">
          <table id="hcDetail15" class="stripe hcDetail" cellspacing="0" width="100%">
            <thead>
              <tr>
              <th style="text-align: left;">Computer Name</th>
              <th>Event Date</th>
              <th>Antivirus Details</th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach($hcDetail15 as $row) {
              echo "<tr>
                <td><a href='labtech:open?computerid=".$row['computerid']."'>".$row['Computer Name']."</td>
                <td style='text-align:center;'>".$row['Event Date']."</td>
                <td style='text-align:center;'>".$row['Details']."</td>
              </tr>";
                };
            ?>
            </tbody>
          </table>
        </div>
        <div id="sdTabDSK">
          <table id="hcDetail16" class="stripe hcDetail compact" cellspacing="0" width="100%">
            <thead>
              <tr>
              <th style="text-align: left;">Computer Name</th>
              <th>Event Date</th>
              <th>Disk Details</th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach($hcDetail16 as $row) {
              echo "<tr>
                <td><a href='labtech:open?computerid=".$row['computerid']."'>".$row['Computer Name']."</td>
                <td style='text-align:center;'>".$row['Event Date']."</td>
                <td style='text-align:center;'>".$row['Details']."</td>
              </tr>";
                };
            ?>
            </tbody>
          </table>
        </div>
        <div id="sdTabINT">
          <table id="hcDetail17" class="stripe hcDetail compact" cellspacing="0" width="100%">
            <thead>
              <tr>
              <th style="text-align: left;">Computer Name</th>
              <th>Event Date</th>
              <th>Intrusion Details</th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach($hcDetail17 as $row) {
              echo "<tr>
                <td><a href='labtech:open?computerid=".$row['computerid']."'>".$row['Computer Name']."</td>
                <td style='text-align:center;'>".$row['Event Date']."</td>
                <td style='text-align:center;'>".$row['Details']."</td>
              </tr>";
                };
            ?>
            </tbody>
          </table>
        </div>
        <div id="sdTabUSB">
          <table id="hcDetail18" class="stripe hcDetail compact" cellspacing="0" width="100%">
            <thead>
              <tr>
              <th style="text-align: left;">Computer Name</th>
              <th>Event Date</th>
              <th>Usability Details</th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach($hcDetail18 as $row) {
              echo "<tr>
                <td><a href='labtech:open?computerid=".$row['computerid']."'>".$row['Computer Name']."</td>
                <td style='text-align:center;'>".$row['Event Date']."</td>
                <td style='text-align:center;'>".$row['Details']."</td>
              </tr>";
                };
            ?>
            </tbody>
          </table>
        </div>
        <div id="sdTabSRV">
          <table id="hcDetail19" class="stripe hcDetail compact" cellspacing="0" width="100%">
            <thead>
              <tr>
              <th style="text-align: left;">Computer Name</th>
              <th>Event Date</th>
              <th>Services Details</th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach($hcDetail19 as $row) {
              echo "<tr>
                <td><a href='labtech:open?computerid=".$row['computerid']."'>".$row['Computer Name']."</td>
                <td style='text-align:center;'>".$row['Event Date']."</td>
                <td style='text-align:center;'>".$row['Details']."</td>
              </tr>";
                };
            ?>
            </tbody>
          </table>
        </div>
        <div id="sdTabUPD">
          <table id="hcDetail20" class="stripe hcDetail compact" cellspacing="0" width="100%">
            <thead>
              <tr>
              <th style="text-align: left;">Computer Name</th>
              <th>Event Date</th>
              <th>Updates Details</th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach($hcDetail20 as $row) {
              echo "<tr>
                <td><a href='labtech:open?computerid=".$row['computerid']."'>".$row['Computer Name']."</td>
                <td style='text-align:center;'>".$row['Event Date']."</td>
                <td style='text-align:center;'>".$row['Details']."</td>
              </tr>";
                };
            ?>
            </tbody>
          </table>
        </div>
        <div id="sdTabEV">
          <table id="hcDetail14" class="stripe hcDetail compact" cellspacing="0" width="100%">
            <thead>
              <tr>
              <th style="text-align: left;">Computer Name</th>
              <th>Event Date</th>
              <th>Event Log Details</th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach($hcDetail14 as $row) {
              echo "<tr>
                <td><a href='labtech:open?computerid=".$row['computerid']."'>".$row['Computer Name']."</td>
                <td style='text-align:center;'>".$row['Event Date']."</td>
                <td style='text-align:center;'>".$row['Details']."</td>
              </tr>";
                };
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div id="tabTopComp">
      <!-- Create table for top 10 active devices? -->
      <!-- failed patches? -->
      <!-- Create table for current open alert and CW ticket number/link -->
      <!-- Create table for scheduled reports, time, freq, and contact -->
      <!-- create table for patch email notification -->
      <!-- <div style="margin: 100px 0 100px 0; padding-left:30%;">
        <img src="images/tbrun1.png" height="185px" width="402px"></img>
      </div> -->
      <table id="tixNew" class="stripe compact" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th style='text-align: left;'>Computer Name</th>
            <th style='text-align: left;'>Subject</th>
            <th style='text-align: center;'>CW Ticket</th>
            <th style='text-align: left;'>Priority</th>
            <th style='text-align: left;'>Status</th>
            <th style='text-align: left;'>Opened</th>
          </tr>
        </thead>
        <tbody>
        <?php
        foreach($tixlatResult as $row) {
          echo "<tr>
            <td><a href='computers.php?computerid=" . $row['computerid'] . "'>" . $row['name'] . "</a></td>
            <td>".$row['subject']."</td>
            <td style='text-align:center;'><a href='https://".CWURL."/v4_6_release/services/system_io/router/openrecord.rails?locale=en_US&recordType=ServiceFv&recid=".$row['externalid']."&companyName=".CWCOID."' target=blank>".$row['externalid']."</a></td>
            <td>".$row['Priority']."</td>
            <td>".$row['Status']."</td>
            <td style='text-align: left;'>".$row['Start']."</td>
          </tr>";
            };
        ?>
        </tbody>
      </table>
    </div>
    <div id="tabTopAlert">
      <table id="tixTopCat" class="stripe compact" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th style='text-align: left;'>Computer Name</th>
            <th style='text-align: left;'>Ticket Category</th>
            <th style='text-align: left;'>Monitor</th>
            <th>Count</th>
          </tr>
        </thead>
        <tbody>
        <?php
        foreach($tixResult as $row) {
          echo "<tr>
            <td><a href='computers.php?computerid=" . $row['computerid'] . "'>" . $row['Computer Name'] . "</a></td>
            <td>".$row['Category']."</td>
            <td>".$row['Subject']."</td>
            <td style='text-align:center;'>".$row['times']."</td>
          </tr>";
            };
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</div>
<?php $mainClass->buildTemplate('footer'); ?>

<script>
  var cpu = {
    chart: {renderTo: 'hcCPU', type: 'spline', zoomType: 'x', resetZoomButton: {position: {x: 0, y: -30}}},
    title: {text: '<?php echo $computerName ?> CPU Daily Average (60 Days)', x: -20},
    /*subtitle: {text: 'Daily Average', x: -20},*/
    xAxis: {categories: [], title: {text: 'Date'}},
    yAxis: {title: {text: 'Percent %'}, plotLines: [{value: 0, width: 1, color: '#808080'}]},
    legend: {layout: 'vertical', align: 'right', verticalAlign: 'middle', borderWidth: 0},
    tooltip: {
      positioner: function (labelWidth, labelHeight, point) {
        var tooltipX, tooltipY;
        if (point.plotX + cpu.plotLeft < labelWidth && point.plotY + labelHeight > cpu.plotHeight) {
          tooltipX = cpu.plotLeft;
          tooltipY = cpu.plotTop + cpu.plotHeight - 2 * labelHeight - 10;
        } else {
          tooltipX = cpu.plotLeft;
          tooltipY = cpu.plotTop + cpu.plotHeight - labelHeight;
        }
        return {
          x: tooltipX,
          y: tooltipY
        };
      }
    },
    credits: 0,
    series: []
  };
  $.getJSON('config/perfComp.php?computerid=<?php echo $computerid ?>', function(json) {
    cpu.xAxis.categories = json[0]['data'];
    cpu.series[0] = json[1];
    chart = new Highcharts.Chart(cpu);
  });
  $( "#pbAV" ).progressbar({value: <?php if(!$pbAV) {echo "0";} else {echo $pbAV;}; ?>, max: 100, min: 0});
  $( "#pbDSK" ).progressbar({value: <?php if(!$pbDSK) {echo "0";} else {echo $pbDSK;}; ?>, max: 100, min: 0});
  $( "#pbINT" ).progressbar({value: <?php if(!$pbINT) {echo "0";} else {echo $pbINT;}; ?>, max: 100, min: 0});
  $( "#pbUSB" ).progressbar({value: <?php if(!$pbUSB) {echo "0";} else {echo $pbUSB;}; ?>, max: 100, min: 0});
  $( "#pbSRV" ).progressbar({value: <?php if(!$pbSRV) {echo "0";} else {echo $pbSRV;}; ?>, max: 100, min: 0});
  $( "#pbUPD" ).progressbar({value: <?php if(!$pbUPD) {echo "0";} else {echo $pbUPD;}; ?>, max: 100, min: 0});
  $( "#pbEV" ).progressbar({value: <?php if(!$pbEV) {echo "0";} else {echo $pbEV;}; ?>, max: 100, min: 0});
  $( "#pbAVG" ).progressbar({value: <?php if(!$pbAVG) {echo "0";} else {echo $pbAVG;}; ?>, max: 100, min: 0});
  $( "#pbcAV" ).progressbar({value: <?php if(!$pbcAV) {echo "0";} else {echo $pbcAV;}; ?>, max: 100, min: 0});
  $( "#pbcDSK" ).progressbar({value: <?php if(!$pbcDSK) {echo "0";} else {echo $pbcDSK;}; ?>, max: 100, min: 0});
  $( "#pbcINT" ).progressbar({value: <?php if(!$pbcINT) {echo "0";} else {echo $pbcINT;}; ?>, max: 100, min: 0});
  $( "#pbcUSB" ).progressbar({value: <?php if(!$pbcUSB) {echo "0";} else {echo $pbcUSB;}; ?>, max: 100, min: 0});
  $( "#pbcSRV" ).progressbar({value: <?php if(!$pbcSRV) {echo "0";} else {echo $pbcSRV;}; ?>, max: 100, min: 0});
  $( "#pbcUPD" ).progressbar({value: <?php if(!$pbcUPD) {echo "0";} else {echo $pbcUPD;}; ?>, max: 100, min: 0});
  $( "#pbcEV" ).progressbar({value: <?php if(!$pbcEV) {echo "0";} else {echo $pbcEV;}; ?>, max: 100, min: 0});
  $( "#pbcAVG" ).progressbar({value: <?php if(!$pbcAVG) {echo "0";} else {echo $pbcAVG;}; ?>, max: 100, min: 0});
</script>
</body>
</html>
