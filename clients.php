<?php
require('config/dbconnect.php');
require('config/branding.php');
include_once('classes/api.php');
include_once('classes/Main.php');

$mainClass = new Main();

$clientid = $_REQUEST['clientid'];

$stmt = $pdo->prepare("SELECT `name` as 'Client Name', `Team Assignment` as 'Team Assignment', `Managed IT - Active` FROM `v_extradataclients` WHERE clientid = :clientid");
$stmt->execute(array('clientid' => $clientid));
foreach($stmt as $thisInfo) {
  $clientName = $thisInfo['Client Name'];
  $teamName = $thisInfo['Team Assignment'];
  $activeStatus = $thisInfo['Managed IT - Active'];
}

$stmt = $pdo->prepare('SELECT * FROM clients WHERE clientid = :clientid');
$stmt->execute(array('clientid' => $clientid));
foreach($stmt as $cInfo) {
  $geoAddress = $cInfo['Address1'].', '.$cInfo['City'].', '.$cInfo['State'];
  $recid = $cInfo['ExternalID'];
  $zip = $cInfo['Zip'];
  $CompanyName = $cInfo['Company'];
  $CompanyCity = $cInfo['City'];
  $CompanyState = $cInfo['State'];
}

// Get client address from CW
$c = new Company();
$ret = $c->getCompany($recid);

foreach($ret as $k=>$v) {
  if(isset($v->DefaultAddress->StreetLines->string[0])) {$gAdd1 = $v->DefaultAddress->StreetLines->string[0];} else {$gAdd1 = "";};
  if(isset($v->DefaultAddress->StreetLines->string[1])) {$gAdd2 = $v->DefaultAddress->StreetLines->string[1];} else {$gAdd2 = "";};
  $gCity = $v->DefaultAddress->City;
  $gState = $v->DefaultAddress->State;
  $gZip = $v->DefaultAddress->Zip;
  $gAddress = $gAdd1.' '.$gAdd2.', '.$gCity.', '.$gState.' '.$gZip;
  $cwStatus = $v->Status;
}

$loc = $pdo->prepare('SELECT * FROM locations WHERE clientid = :clientid');
$loc->execute(array('clientid' => $clientid));
foreach($loc as $l) {
  $locCount = count($l['LocationID']);
}


$stmt = $pdo->prepare('SELECT * FROM computers WHERE clientid = :clientid');
$stmt->execute(array('clientid' => $clientid));
foreach($stmt as $cInfo) {

}

$stmt = $pdo->prepare('SELECT * FROM v_extradataclients WHERE clientid = :clientid');
$stmt->execute(array('clientid' => $clientid));
foreach($stmt as $vcInfo) {
  $clientSpecialist = $vcInfo['Client Specialist'];
}

$stmt = $pdo->prepare("SELECT SUM(CASE WHEN os LIKE '%server%' THEN 1 ELSE 0 END) AS TotalSrv, SUM(CASE WHEN os NOT LIKE '%server%' THEN 1 ELSE 0 END) AS TotalWS, SUM(CASE WHEN (os LIKE '%2003%' or os LIKE '%xp%') THEN 1 ELSE 0 END) AS TotalUS, COUNT(computerid) AS Total FROM computers WHERE clientid = :clientid");
$stmt->execute(array('clientid' => $clientid));
foreach($stmt as $osCountInfo) {
  $srvCount = $osCountInfo['TotalSrv'];
  $wsCount = $osCountInfo['TotalWS'];
  $usCount = $osCountInfo['TotalUS'];
  $compCount = $osCountInfo['Total'];
}

$tixResult = $pdo->prepare("SELECT computers.computerid as computerid, OS, BiosFlash, computers.name as 'Computer Name', infocategory.categoryname AS Category, IF(INSTR(SUBJECT,\":\"),CONCAT(RIGHT(LEFT(SUBJECT,INSTR(SUBJECT,\":\")-1),200)), tickets.subject) AS 'Subject', COUNT(tickets.category) AS times FROM tickets JOIN computers USING (computerid) LEFT JOIN clients ON tickets.clientid=clients.clientid JOIN infocategory ON infocategory.id=tickets.category WHERE tickets.starteddate > DATE_ADD(NOW(),INTERVAL - 30 DAY) AND computers.clientid= :clientid GROUP BY tickets.subject ORDER BY times DESC LIMIT 10;");
$tixResult->execute(array('clientid' => $clientid));
?>

<!DOCTYPE html>
<html>
 <head>
 <title><?php echo APP_TITLE . " | " . $clientName; ?></title>

 <?php $mainClass->buildTemplate('head'); ?>

 <script type="text/javascript" language="javascript" class="init">
 $(document).ready(function() {
   $("#tabs").tabs( {
      "activate": function(event, ui) {
            $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
        }
   });
   $('table.display').dataTable( {
   "sScrollY": "250px",
   "bScrollCollapse": true,
   "bPaginate": false,
   "bJQueryUI": true,
   buttons: true
  });
   $('#locationList').dataTable({
     "scrollY": "120px",
     "scrollCollapse": true,
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: false,
     buttons: true
   });
   $('#tixTopCat').dataTable({
     "scrollCollapse": true,
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: true,
     "order": [[ 3, "desc" ]],
     buttons: true
   });
   $('#cwTix').dataTable({
     "sScrollY": "500px",
     "scrollCollapse": true,
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: true,
     "order": [[ 0, "desc" ]],
     buttons: true
   });
   var ttc = $('#tixTopCat').DataTable();
   ttc.buttons().container().insertBefore('#tixTopCat_wrapper');
   var tc = $('#cwTix').DataTable();
   tc.buttons().container().insertBefore('#cwTix_wrapper');
   var ts = $('#hcTeamScore').DataTable();
   ts.buttons().container().insertBefore('#hcTeamScore_filter');
   var ls = $('#hcLocationScore').DataTable();
   ls.buttons().container().insertBefore('#hcLocationScore_filter');
   $('#tixNew').dataTable({
     "scrollCollapse": true,
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: true,
     "order": [[ 6, "desc" ]]
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
  <h5><a class='linkNav' href='index.php'>Home</a> > <a class='linkNav' href='main.php'>Clients</a> > <a class='linkNav' href='clients.php?clientid=<?php echo $clientid; ?>'><?php echo $clientName . " (" . $clientid . ")"; ?></a></h5>
</div>

<!-- Content -->
<div id="mainWrapper">
<div id="main-Header" class="ui-widget-header ui-corner-all" style="padding-left:10px; height: auto !important;">
  <h2 style="margin-left:0;"><?php echo "Client:  " . $clientName . " (<a href='labtech:open?clientid=" . $clientid . "'>" . $clientid . "</a>)"; ?></h2><?php echo /*$geoAddress*/$gAddress; ?>
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
              <td><strong>LabTech Status:</strong></td>
              <td style="padding-left: 10px;"><?php if($activeStatus == '1') {echo "<strong style='color:green;'>Active</strong>";} else {echo "<strong style='color:red;'>Inactive</strong>";}; ?></td>
            </tr>
            <tr>
              <td><strong>ConnectWise Status:</strong></td>
              <td style="padding-left: 10px;"><?php if(strpos($cwStatus, 'Active') !== false) {echo "<strong style='color:green;'>".$cwStatus."</strong>";} else {echo "<strong style='color:red;'>".$cwStatus."</strong>";}; ?></td>
            </tr>
            <tr>
              <td><strong>ConnectWise Name:</strong></td>
              <td style="padding-left: 10px;"><?php echo $CompanyName; ?></td>
            </tr>
            <tr>
              <td><strong>Documentation:</strong></td>
              <td style="padding-left: 10px;"><a href="#">Documentation Link</a></td>
            </tr>
            <tr><td>&nbsp;</td><td></td>
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
      <div class="main-top-header ui-widget-header ui-helper-clearfix ui-corner-top">
        Location Information
      </div>
      <div class="ui-widget-content ui-corner-bottom" style="height:160px;">
        <div id="main-top-right-left">
          <table class="noBorder" style="padding: 5px;">
            <tbody>
              <tr>
                <td><strong>Locations:</strong></td>
                <td style="padding-left: 10px;"><?php echo $locCount; ?></td>
              </tr>
              <tr>
                <td><strong>Servers:</strong></td>
                <td style="padding-left: 10px;"><?php echo $srvCount; ?></td>
              </tr>
              <tr>
                <td><strong>Workstations:</strong></td>
                <td style="padding-left: 10px;"><?php echo $wsCount; ?></td>
              </tr>
              <tr>
                <td><strong>Unsupported (XP/2003):</strong></td>
                <td style="padding-left: 10px;"><?php echo $usCount; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="main-top-right-right">
          <table id="locationList" class="stripe compact" cellspacing="0" width="100%">
            <thead>
              <tr>
                <td><strong>Locations:</strong></td>
                <td><strong>Agent DL:</strong></td>
              </tr>
            </thead>
            <tbody>
              <?php
              $loc = $pdo->prepare('SELECT * FROM locations WHERE clientid = :clientid');
              $loc->execute(array('clientid' => $clientid));
               foreach($loc as $locInfo) {
                echo "<tr><td><a href='locations.php?locationid=" . $locInfo['LocationID'] . "'>" . $locInfo['Name'] . "</a></td>
                <td><a href='https://".LTURL."/Labtech/Deployment.aspx?Probe=1&ID=" . $locInfo['LocationID'] . "'>EXE</a> &#9900; <a href='https://".LTURL."/Labtech/Deployment.aspx?Probe=1&MSILocations=" . $locInfo['LocationID'] . "'>MSI</a></td>
                </tr>";
              } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

<!-- Create icons on the tabs below -->
  <div id="main-bottom" class="ui-widget">
   <div id="tabs">
    <ul>
      <li><a href="#tabScore" onclick="$('#hcAVG').highcharts().reflow()"><img src="images/icons/bricks.png" height="16" width="16">  Health Scores</a></li>
      <!--<li><a href="#tabCompScores"><img src="images/chart.png" height="16" width="16">  Computer Scores</a></li>-->
      <li><a href="#tabLocScores"><img src="images/lt/loc.gif" height="16" width="16"> Location Scores</a></li>
      <li><a href="#tabCwTix"><img src="images/cw3.png" height="16" width="16"> Recent Tickets</a></li>
      <li><a href="#tabTopAlert"><img src="images/icons/error.png" height="16" width="16"> Top Recurring Alerts (30 Days)</a></li>
    </ul>
    <div id="tabScore">
      <h2 style="margin-bottom: 5px;">Health Score Trending</h2>
      <div id="hcAVG" style="min-width: 600px; height: 300px; margin: 0 auto"></div>
      <hr>
      <p><h2 style="margin-bottom: 5px;">Computer Health Scores</h2></p>
      <?php
      $stmt = $pdo->prepare("SELECT `Client_Name` as 'Client Name', c.computerid, OS, BiosFlash, `Computer_Name` as 'Computer Name', `Team_Assignment` as 'Team Assignment', ROUND(AVG(`Antivirus`),1) AS 'Antivirus',ROUND(AVG(`Disk`),1) AS 'Disk',ROUND(AVG(`Intrusion`),1) AS 'Intrusion',ROUND(AVG(`Usability`),1) AS 'Usability',ROUND(AVG(`Services`),1) AS'Services',ROUND(AVG(`Updates`),1) AS 'Updates',ROUND(AVG(`Event_Log`),1) AS 'Events',ROUND((COALESCE(antivirus,0) + COALESCE(DISK,0) + COALESCE(intrusion,0) + COALESCE(usability,0) + COALESCE(services,0) + COALESCE(updates,0) + COALESCE(event_log,0)) / (COALESCE(antivirus/antivirus,0) + COALESCE(DISK/DISK,0) + COALESCE(intrusion/intrusion,0) + COALESCE(usability/usability,0) + COALESCE(services/services,0) + COALESCE(updates/updates,0) + COALESCE(event_log/event_log,0)),1) AS 'Overall Score' FROM plugin_lthc_scores_computers join computers c on c.computerid = plugin_lthc_scores_computers.computerid where c.clientid= :clientid GROUP BY `Computer_Name`");
      $stmt->execute(array('clientid' => $clientid));
      ?>
        <table id="hcTeamScore" class="display compact" cellspacing="0" width="100%">
        <thead>
          <tr>
          <th style="text-align: left;">Computer Name</th>
          <th>Overall</th>
          <th>AV</th>
          <th>DSK</th>
          <th>INT</th>
          <th>USB</th>
          <th>SRV</th>
          <th>UPD</th>
          <th>EVT</th>
          </tr>
        </thead>
        <tbody>
        <?php
        foreach($stmt as $row) {
          if(strpos($row['OS'], 'Server') !== false) {
            //server
            $ctype = '<img src="images/icons/server.png" height="16" width="16">';
          } else {
            if(strpos($row['BiosFlash'], 'Portable') !== false) {
              // laptop
              $ctype = '<img src="images/icons/laptop.png" height="16" width="16">';
            } else {
              // desktop
              $ctype = '<img src="images/icons/computer.png" height="16" width="16">';
            };
          };
          echo "<tr>
            <td><a href='computers.php?computerid=".$row['computerid']."'>".$ctype." ".$row['Computer Name']."</a></td>
            <td style='text-align:right;'>".$row['Overall Score']."</td>
            <td style='text-align:right;'>".$row['Antivirus']."</td>
            <td style='text-align:right;'>".$row['Disk']."</td>
            <td style='text-align:right;'>".$row['Intrusion']."</td>
            <td style='text-align:right;'>".$row['Usability']."</td>
            <td style='text-align:right;'>".$row['Services']."</td>
            <td style='text-align:right;'>".$row['Updates']."</td>
            <td style='text-align:right;'>".$row['Events']."</td>
          </tr>";
            };
        ?>
        </tbody>
      </table>
      <!--<div id='map' style='height:500px;'></div>-->
    </div>
    <div id="tabCompScores">

    </div>
    <div id="tabLocScores">
      <?php
      $stmt = $pdo->prepare("SELECT locations.name as 'Location Name',locations.locationid, ROUND(AVG(`Antivirus`),1) AS 'Antivirus',ROUND(AVG(`Disk`),1) AS 'Disk',ROUND(AVG(`Intrusion`),1) AS 'Intrusion',ROUND(AVG(`Usability`),1) AS 'Usability',ROUND(AVG(`Services`),1) AS'Services',ROUND(AVG(`Updates`),1) AS 'Updates',ROUND(AVG(`Event_Log`),1) AS 'Events',ROUND((COALESCE(antivirus,0) + COALESCE(DISK,0) + COALESCE(intrusion,0) + COALESCE(usability,0) + COALESCE(services,0) + COALESCE(updates,0) + COALESCE(event_log,0)) / (COALESCE(antivirus/antivirus,0) + COALESCE(DISK/DISK,0) + COALESCE(intrusion/intrusion,0) + COALESCE(usability/usability,0) + COALESCE(services/services,0) + COALESCE(updates/updates,0) + COALESCE(event_log/event_log,0)),1) AS 'Overall Score' FROM plugin_lthc_scores_computers join computers using (computerid) left join locations using(locationid) where locations.clientid= :clientid GROUP BY locations.locationid");
      $stmt->execute(array('clientid' => $clientid));
      ?>
      	<table id="hcLocationScore" class="display compact" cellspacing="0" width="100%">
      	<thead>
      		<tr>
      		<th style="text-align: left;">Location Name</th>
      		<th>Overall</th>
      		<th>AV</th>
      		<th>DSK</th>
      		<th>INT</th>
      		<th>USB</th>
      		<th>SRV</th>
      		<th>UPD</th>
      		<th>EVT</th>
      		</tr>
      	</thead>
      	<tbody>
      	<?php
        foreach($stmt as $row) {
      		echo "<tr>
      			<td><a href='locations.php?locationid=" . $row['locationid'] . "'>" . $row['Location Name'] . "</a></td>
      			<td style='text-align:right;'>".$row['Overall Score']."</td>
      			<td style='text-align:right;'>".$row['Antivirus']."</td>
      			<td style='text-align:right;'>".$row['Disk']."</td>
      			<td style='text-align:right;'>".$row['Intrusion']."</td>
      			<td style='text-align:right;'>".$row['Usability']."</td>
      			<td style='text-align:right;'>".$row['Services']."</td>
      			<td style='text-align:right;'>".$row['Updates']."</td>
      			<td style='text-align:right;'>".$row['Events']."</td>
      		</tr>";
      			};
      	?>
      	</tbody>
      </table>
    </div>
      <!-- Create table for top 10 active devices? -->
      <!-- Create table for current open alert and CW ticket number/link -->
      <!-- Create table for scheduled reports, time, freq, and contact -->
      <!-- create table for patch email notification -->
      <!--<div style="margin: 100px 0 100px 0; padding-left:30%;">
        <img src="images/tbrun1.png" height="185px" width="402px"></img>
      </div> -->
      <!--https://connect.marconet.com/support?company=marco&goto=1535310 -->

    <div id="tabCwTix">
      <!-- API CW Tickets -->
      <h3>Open ConnectWise Tickets (Max 25 most recent)</h3>
      <table id='cwTix' class="stripe compact" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th style='text-align:left;'>Ticket #</th>
          <th style='text-align:left;'>Summary</th>
          <th style='text-align:left;'>Status</th>
          <th style='text-align:left;'>Priority</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $connectwise = new cwAPI();
          $connectwise->setAction('FindServiceTickets');
          $options = array('conditions' => 'CompanyId = '.$recid, 'limit' => 25, 'isOpen' => true,  'orderBy' => 'EnteredDateUTC desc'); // Last 10 open tickets by age descending
          $connectwise->setParameters($options);
          $tix = $connectwise->makeCall();
          foreach($tix as $k=>$v) {
            if(count($v->TicketFindResult) <= 24) {$cnt = count($v->TicketFindResult)-1;} else {$cnt = 24;};
            for($x = 0; $x <=$cnt; $x++){
              $entDate = split("T", $v->TicketFindResult[$x]->EnteredDateUTC);
              $lastDate = split("T", $v->TicketFindResult[$x]->LastUpdatedUTC);
              if($v->TicketFindResult[$x]->IsInSla === true) {$sla = "Yes";} else {$sla = "No";}
              echo "
              <tr>
                <td><a href='https://".CWURL."/v4_6_release/services/system_io/router/openrecord.rails?locale=en_US&recordType=ServiceFv&recid=".$v->TicketFindResult[$x]->Id."&companyName=".CWCOID."' target=blank>" . $v->TicketFindResult[$x]->Id . "</a></td>
                <td>" . $v->TicketFindResult[$x]->Summary . "</td>
                <td>" . $v->TicketFindResult[$x]->TicketStatus . "</td>
                <td>" . $v->TicketFindResult[$x]->Priority . "</td>
              </tr>
              ";
            }
          }
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
        if(strpos($row['OS'], 'Server') !== false) {
          //server
          $ctype = '<img src="images/icons/server.png" height="16" width="16">';
        } else {
          if(strpos($row['BiosFlash'], 'Portable') !== false) {
            // laptop
            $ctype = '<img src="images/icons/laptop.png" height="16" width="16">';
          } else {
            // desktop
            $ctype = '<img src="images/icons/computer.png" height="16" width="16">';
          };
        };
        echo "<tr>
          <td><a href='computers.php?computerid=" . $row['computerid'] . "'>".$ctype." " . $row['Computer Name'] . "</a></td>
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

<div id="main-top" style="height:300px !important; margin:30px 0 0 0;">
  <div id="main-top-left" class="ui-widget">
    <div class="main-top-header ui-widget-header ui-helper-clearfix ui-corner-top">
      Current Weather
    </div>
    <div class="ui-widget-content ui-corner-bottom" style="height:250px; background-color:#1192d3;">
      <div id='weather'></div>
    </div>
  </div>
  <div id="main-top-right" class="ui-widget">
    <div class="main-top-header ui-widget-header ui-helper-clearfix ui-corner-top">
      <?php echo $CompanyCity.', '.$CompanyState; ?> Traffic
    </div>
    <div class="ui-widget-content ui-corner-bottom" style="height:250px;">
      <div id='map' style='height:100%;'></div>
    </div>
  </div>
</div>
</div>
<?php $mainClass->buildTemplate('footer'); ?>

<script>
$.simpleWeather({
  location: '<?php echo $zip?>',
  unit: 'f',
  success: function(weather) {
    html = '<h2><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg;'+weather.units.temp+'</h2>';
    html += '<ul><li>'+weather.city+', '+weather.region+'</li>';
    html += '<li class="currently">'+weather.currently+'</li>';
    html += '<li>'+weather.wind.direction+' '+weather.wind.speed+' '+weather.units.speed+'</li></ul>';

    $("#weather").html(html);
  },
  error: function(error) {
    $("#weather").html('<p>'+error+'</p>');
  }
});
var hcAVG = {
  chart: {renderTo: 'hcAVG', type: 'spline', zoomType: 'x', resetZoomButton: {position: {x: 0, y: -30}}},
  title: {text: '<?php echo $clientName ?> Health Score Trending', x: -20},
  subtitle: {text: 'Last 180 Days', x: -20},
  xAxis: {categories: [], title: {text: 'Date'}},
  yAxis: {title: {text: 'Score'}, plotLines: [{value: 0, width: 1, color: '#808080'}]},
  legend: {layout: 'vertical', align: 'right', verticalAlign: 'middle', borderWidth: 0},
  width: $('#tabScore').width(),
  tooltip: {
    positioner: function (labelWidth, labelHeight, point) {
      var tooltipX, tooltipY;
      if (point.plotX + hcAVG.plotLeft < labelWidth && point.plotY + labelHeight > hcAVG.plotHeight) {tooltipX = hcAVG.plotLeft; tooltipY = hcAVG.plotTop + hcAVG.plotHeight - 2 * labelHeight - 10;}
      else {tooltipX = hcAVG.plotLeft; tooltipY = hcAVG.plotTop + hcAVG.plotHeight - labelHeight;}
      return {x: tooltipX, y: tooltipY};}},
  credits: 0,
  series: []
};
$.getJSON('config/scoreData.php?clientid=<?php echo $clientid ?>', function(json) {
  hcAVG.xAxis.categories = json[0]['data']; //xAxis: {categories: []}
  hcAVG.series[0] = json[1];
  hcAVG.series[1] = json[2];
  hcAVG.series[2] = json[3];
  hcAVG.series[3] = json[4];
  hcAVG.series[4] = json[5];
  hcAVG.series[5] = json[6];
  hcAVG.series[6] = json[7];
  hcAVG.series[7] = json[8];
  chart = new Highcharts.Chart(hcAVG);
});

function initMap() {
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 11,
    center: {lat: <?php echo LAT; ?>, lng: <?php echo LON; ?>},
    styles: [
      {"featureType": "water", "stylers": [{"color": "#46bcec"}, {"visibility": "on"}]},
      {"featureType": "landscape", "stylers": [{"color": "#f2f2f2"}]},
      {"featureType": "road", "stylers": [{"saturation": -100}, {"lightness": 45}]},
      {"featureType": "road.highway", "stylers": [{"visibility": "simplified"}]},
      {"featureType": "road.arterial", "elementType": "labels.icon", "stylers": [{"visibility": "off"}]},
      {"featureType": "administrative", "elementType": "labels.text.fill", "stylers": [{"color": "#444444"}]},
      {"featureType": "transit", "stylers": [{"visibility": "off"}]},
      {"featureType": "poi", "stylers": [{"visibility": "off"}]}]
  });
  var geocoder = new google.maps.Geocoder();
  geocodeAddress(geocoder, map);
  var trafficLayer = new google.maps.TrafficLayer();
  trafficLayer.setMap(map);
}

function geocodeAddress(geocoder, resultsMap) {
  var address = '<?php echo $geoAddress; ?>';
  geocoder.geocode({'address': address}, function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      resultsMap.setCenter(results[0].geometry.location);
      var marker = new google.maps.Marker({
        map: resultsMap,
        position: results[0].geometry.location
      });
    } else {
      alert('Geocode was not successful for the following reason: ' + status);
    }
  });
}

</script>
</body>
</html>
