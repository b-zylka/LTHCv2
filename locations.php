<?php
require('config/dbconnect.php');
require('config/branding.php');
include_once('classes/Main.php');
$mainClass = new Main();

$locationid = $_REQUEST['locationid'];

 $stmt = $pdo->prepare("SELECT l.name as 'lName', c.name as 'cName', c.clientid as 'clientid' FROM locations l JOIN clients c ON c.clientid=l.clientid WHERE locationid = :locationid");
 $stmt->execute(array('locationid' => $locationid));
 foreach($stmt as $thisInfo) {
   $clientName = $thisInfo['cName'];
   $locationName = $thisInfo['lName'];
   $clientid = $thisInfo['clientid'];
 }

$loc = $pdo->prepare('SELECT * FROM locations WHERE clientid = :clientid');
$loc->execute(array('clientid' => $clientid));
foreach($loc as $l) {
  $locCount = count($l['LocationID']);
}

$loc = $pdo->prepare('SELECT * FROM locations WHERE locationid = :locationid');
$loc->execute(array('locationid' => $locationid));
foreach($loc as $llInfo) {
  $geoAddress = $llInfo['Address'].', '.$llInfo['City'].', '.$llInfo['State'];
  $zip = $llInfo['Zip'];
}

$compResult = $pdo->prepare("SELECT computers.computerid as 'computerid', LastUsername, OS, LastContact, BiosFlash, `MSP - Manufacturer Date`, `MSP - Warranty Expiration`, computers.Name from computers join v_extradatacomputers using(computerid) where locationid = :locationid");
$compResult->execute(array('locationid' => $locationid));

$stmt = $pdo->prepare("SELECT * FROM v_extradataclients WHERE clientid = :clientid");
$stmt->execute(array('clientid' => $clientid));
foreach($stmt as $vcInfo) {
  $clientSpecialist = $vcInfo['Client Specialist'];
  $teamName = $vcInfo['Team Assignment'];
}

$stmt = $pdo->prepare("SELECT SUM(CASE WHEN os LIKE '%server%' THEN 1 ELSE 0 END) AS TotalSrv, SUM(CASE WHEN os NOT LIKE '%server%' THEN 1 ELSE 0 END) AS TotalWS, SUM(CASE WHEN (os LIKE '%2003%' or os LIKE '%xp%') THEN 1 ELSE 0 END) AS TotalUS, COUNT(computerid) AS Total from computers where locationid = :locationid");
$stmt->execute(array('locationid' => $locationid));
foreach($stmt as $osCountInfo) {
  $srvCount = $osCountInfo['TotalSrv'];
  $wsCount = $osCountInfo['TotalWS'];
  $usCount = $osCountInfo['TotalUS'];
  $compCount = $osCountInfo['Total'];
}

$tixResult = $pdo->prepare("SELECT computers.computerid as computerid, OS, BiosFlash, computers.name as 'Computer Name', infocategory.categoryname AS Category, IF(INSTR(SUBJECT,\":\"),CONCAT(RIGHT(LEFT(SUBJECT,INSTR(SUBJECT,\":\")-1),200)), tickets.subject) AS 'Subject', COUNT(tickets.category) AS times FROM tickets JOIN computers USING (computerid) LEFT JOIN clients ON tickets.clientid=clients.clientid JOIN infocategory ON infocategory.id=tickets.category WHERE tickets.starteddate > DATE_ADD(NOW(),INTERVAL - 30 DAY) AND computers.locationid= :locationid GROUP BY tickets.subject ORDER BY times DESC LIMIT 10;");
$tixResult->execute(array('locationid' => $locationid));

$tixlatResult = $pdo->prepare("SELECT c.computerid as computerid, OS, BiosFlash,c.name as name, subject, ticketstatus.ticketstatus as 'Status', starteddate as 'Start', requestoremail as 'Who', externalid, tickets.locationid, ticketpriority.name as 'Priority' FROM tickets JOIN computers c USING (computerid) JOIN infocategory ON infocategory.id=tickets.category  JOIN ticketstatus ON ticketstatusid=tickets.status JOIN ticketpriority ON ticketpriority.priority=tickets.priority WHERE category=5 AND starteddate > DATE_ADD(NOW(), INTERVAL -3 DAY) AND c.locationid = :locationid ORDER BY starteddate DESC;");
$tixlatResult->execute(array('locationid' => $locationid));
?>

<!DOCTYPE html>
<html>
 <head>
 <title><?php echo APP_TITLE . " | " . $locationName; ?></title>

 <?php $mainClass->buildTemplate('head'); ?>

 <script type="text/javascript" language="javascript" class="init">
 $(document).ready(function() {
   $("#tabs").tabs( {
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
        "sDom":'<"H"lfr<"invTitle">>t<"F"ip>',
        buttons: true
  });
      $('#hcTeamScore').dataTable( {
    		"sScrollY": "250px",
    		"bScrollCollapse": true,
    		"bPaginate": false,
    		"bJQueryUI": true,
        "sDom":'<"H"lfr<"scTitle">>t<"F"ip>',
        buttons: true
  	 });
     $('#hcLocationScore').dataTable({
       "sScrollY": "250px",
       "bScrollCollapse": true,
       "bPaginate": false,
       "bJQueryUI": true,
        "sDom":'<"H"lfr<"locSCtitle">>t<"F"ip>',
        buttons: true
    });
   /*$("div.scTitle").html('Computer Health Scores');
   $("div.invTitle").html('Computer Inventory');
   $("div.locSCtitle").html('Location Health Scores');*/
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
     "order": [[ 3, "desc" ]],
     buttons: true
   });
   $('#tixNew').dataTable({
     "scrollCollapse": true,
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: true,
     "order": [[ 6, "desc" ]]
   });
   var ttc = $('#tixTopCat').DataTable();
   ttc.buttons().container().insertBefore('#tixTopCat_wrapper');
   var ts = $('#hcTeamScore').DataTable();
   ts.buttons().container().insertBefore('#hcTeamScore_filter');
   var ls = $('#hcLocationScore').DataTable();
   ls.buttons().container().insertBefore('#hcLocationScore_filter');
   var ci = $('#compInventory').DataTable();
   ci.buttons().container().insertBefore('#compInventory_filter');
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
  <h5><a class='linkNav' href='index.php'>Home</a> > <a class='linkNav' href='main.php'>Clients</a> > <a class='linkNav' href='clients.php?clientid=<?php echo $clientid; ?>'><?php echo $clientName; ?></a> > <a class='linkNav' href='locations.php?locationid=<?php echo $locationid; ?>'><?php echo $locationName . " (" . $locationid . ")"; ?></a></h5>
</div>

<!-- Content -->
<div id="mainWrapper">
<div id="main-Header" class="ui-widget-header ui-corner-all" style="padding-left:10px; height: auto !important;">
  <h2 style="margin-left:0;"><?php echo "Location:  " . $locationName . " (" . $locationid . ")"; ?></h2><?php echo $geoAddress; ?>
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
      <li><a href="#tabCompScores"><img src="images/lt/comp.gif" height="16" width="16"> Computer Scores</a></li>
      <li><a href="#tabCompInv"><img src="images/icons/application_view_list.png" height="16" width="16"> Computer Inventory</a></li>
      <li><a href="#tabLocScores"><img src="images/lt/loc.gif" height="16"> Location Scores</a></li>
      <li><a href="#tabTopComp"><img src="images/icons/tag_orange.png" height="16" width="16"> Latest User Tickets</a></li>
      <li><a href="#tabTopAlert"><img src="images/icons/error.png" height="16" width="16"> Top Recurring Alerts (30 Days)</a></li>
    </ul>
    <div id="tabCompScores">
      <?php
      $sql = $pdo->prepare("SELECT `Client_Name` as 'Client Name', BiosFlash, OS, computers.computerid, `Computer_Name` as 'Computer Name', `Team_Assignment` as 'Team Assignment', locationid, ROUND(AVG(`Antivirus`),1) AS 'Antivirus',ROUND(AVG(`Disk`),1) AS 'Disk',ROUND(AVG(`Intrusion`),1) AS 'Intrusion',ROUND(AVG(`Usability`),1) AS 'Usability',ROUND(AVG(`Services`),1) AS'Services',ROUND(AVG(`Updates`),1) AS 'Updates',ROUND(AVG(`Event_Log`),1) AS 'Events',ROUND(((ROUND(AVG(`Antivirus`),1)+ROUND(AVG(`Disk`),1)+ROUND(AVG(`Intrusion`),1)+ROUND(AVG(`Usability`),1)+ROUND(AVG(`Services`),1)+ROUND(AVG(`Updates`),1)+ROUND(AVG(`Event_Log`),1))/7),1) AS 'Overall Score' FROM plugin_lthc_scores_computers join computers using (computerid) where locationid= :locationid GROUP BY `Computer_Name`");
      $sql->execute(array('locationid' => $locationid));
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
        foreach($sql as $row) {
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
    <div id="tabCompInv">
      <table id="compInventory" class="display compact" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>Computer Name</th>
          <th>Last User</th>
          <th>OS</th>
          <th>Purchase Date</th>
          <th>Warranty End</th>
          <th>Last Contact</th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach($compResult as $row) {
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
          <td><a href='details.php?computerid=".$row['computerid']."'>".$ctype." ".$row['Name']."</a></td>
          <td style='text-align:left;'>".$row['LastUsername']."</a></td>
          <td style='text-align:left;'>".$row['OS']."</a></td>
          <td style='text-align:right;'>".$row['MSP - Manufacturer Date']."</a></td>
          <td style='text-align:right;'>".$row['MSP - Warranty Expiration']."</a></td>
          <td style='text-align:right;'>".$row['LastContact']."</a></td>
        </tr>";
          };
      ?>
      </tbody>
    </table>
    </div>
    <div id="tabLocScores">
      <?php
      $sql = $pdo->prepare("SELECT locations.name as 'Location Name',locations.locationid, ROUND(AVG(`Antivirus`),1) AS 'Antivirus',ROUND(AVG(`Disk`),1) AS 'Disk',ROUND(AVG(`Intrusion`),1) AS 'Intrusion',ROUND(AVG(`Usability`),1) AS 'Usability',ROUND(AVG(`Services`),1) AS'Services',ROUND(AVG(`Updates`),1) AS 'Updates',ROUND(AVG(`Event_Log`),1) AS 'Events',ROUND((COALESCE(antivirus,0) + COALESCE(DISK,0) + COALESCE(intrusion,0) + COALESCE(usability,0) + COALESCE(services,0) + COALESCE(updates,0) + COALESCE(event_log,0)) / (COALESCE(antivirus/antivirus,0) + COALESCE(DISK/DISK,0) + COALESCE(intrusion/intrusion,0) + COALESCE(usability/usability,0) + COALESCE(services/services,0) + COALESCE(updates/updates,0) + COALESCE(event_log/event_log,0)),1) AS 'Overall Score' FROM plugin_lthc_scores_computers join computers using (computerid) left join locations using(locationid) where locations.clientid= :clientid GROUP BY locations.locationid");
      $sql->execute(array('clientid' => $clientid));
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
      	foreach($sql as $row) {
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
          <th style='text-align: left;'>Email</th>
          <th style='text-align: left;'>Subject</th>
          <th style='text-align: center;'>CW Ticket</th>
          <th style='text-align: left;'>Priority</th>
          <th style='text-align: left;'>Status</th>
          <th style='text-align: right;'>Opened</th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach($tixlatResult as $row) {
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
          <td><a href='computers.php?computerid=" . $row['computerid'] . "'>".$ctype." " . $row['name'] . "</a></td>
          <td>".$row['Who']."</td>
          <td>".$row['subject']."</td>
          <td style='text-align:center;'><a href='https://".CWURL."/v4_6_release/services/system_io/router/openrecord.rails?locale=en_US&recordType=ServiceFv&recid=".$row['externalid']."&companyName=".CWCOID."' target=blank>".$row['externalid']."</a></td>
          <td>".$row['Priority']."</td>
          <td>".$row['Status']."</td>
          <td>".$row['Start']."</td>
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
      <?php echo $llInfo['City'].', '.$llInfo['State']; ?> Traffic
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
  location: '<?php echo $geoAddress?>',
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
