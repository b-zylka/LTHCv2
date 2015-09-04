<?php
 require('config/dbconnect.php');
 require('config/branding.php');
 include('classes/httpful.phar');
 require('classes/cw.php');
 include_once('classes/Main.php');

 $mainClass = new Main();

 $sql = "SELECT FORMAT(COUNT(DISTINCT clientid),0) AS clients, FORMAT(COUNT(DISTINCT locationid),0) AS locations, FORMAT(COUNT(DISTINCT computerid),0) AS compcnt, FORMAT(SUM(CASE WHEN os LIKE '%server%' THEN 1 ELSE 0 END),0) AS TotalSrv, FORMAT(SUM(CASE WHEN os NOT LIKE '%server%' THEN 1 ELSE 0 END),0) AS TotalWS, FORMAT(SUM(CASE WHEN (os LIKE '%2003%' or os LIKE '%xp%') THEN 1 ELSE 0 END),0) AS TotalUS FROM computers";
 $stmt = $pdo->query($sql);
 $cnt = $stmt->fetch(PDO::FETCH_ASSOC);

 $tixSQL = "SELECT computers.computerid as computerid, OS, BiosFlash, computers.clientid as clientid, clients.name as 'Client Name', computers.name as 'Computer Name', infocategory.categoryname AS Category, IF(INSTR(SUBJECT,\":\"),CONCAT(RIGHT(LEFT(SUBJECT,INSTR(SUBJECT,\":\")-1),200)), tickets.subject) AS 'Subject', COUNT(tickets.category) AS times FROM tickets JOIN computers USING (computerid) LEFT JOIN clients ON tickets.clientid=clients.clientid JOIN infocategory ON infocategory.id=tickets.category WHERE tickets.starteddate > DATE_ADD(NOW(),INTERVAL - 30 DAY) GROUP BY tickets.subject ORDER BY times DESC LIMIT 25;";

 $usrsql = "SELECT FORMAT(COUNT(DISTINCT userid),0) AS users FROM users";
 $stmt = $pdo->query($usrsql);
 $usr = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
 <head>
   <title><?php echo APP_TITLE; ?></title>

   <?php $mainClass->buildTemplate('head'); ?>

 <script type="text/javascript" language="javascript" class="init">
 $(document).ready(function() {
   $("#tabs").tabs( {
      "activate": function(event, ui) {
            $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
        }
   });
   $("#hcTabs").tabs({
     "activate": function(event, ui) {
           $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
       }
   });
   $("#dlTabs").tabs({
      "activate": function(event, ui) {
            $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
        }
   });
   $('#tixTopCat').dataTable({
     "scrollCollapse": true,
     searching: false,
     lengthChange: false,
     bPaginate: false,
     bInfo: false,
     jQueryUI: true,
     "order": [[ 4, "desc" ]],
     buttons: true
   });
   var ttc = $('#tixTopCat').DataTable();
   ttc.buttons().container().insertBefore('#tixTopCat_wrapper');
   $('.statusDash2').DataTable({
     /*scrollCollapse: true,
     "scrollY": "430px",*/
     lengthChange: false,
     bPaginate: false,
     searching: false,
     jQueryUI: true,
     bInfo: false
    });
     $( "button" )
       .button()
       .click(function( event ) {
         event.preventDefault();
       });
     $("#helpDialog").dialog({
         autoOpen: false,
         width: 500,
         modal: true,
         buttons : {
             "Close" : function() {
               $(this).dialog("close");
             }
           }
         });
         $("#knownIssues").dialog({
             autoOpen: false,
             width: 500,
             modal: true,
             buttons : {
                 "Close" : function() {
                   $(this).dialog("close");
                 }
               }
             });
       $('#help').click(function(){
           $("#helpDialog").dialog('open');
         });
       $('#ki').click(function(){
           $("#knownIssues").dialog('open');
         });
       $( "#menu" ).menu({
      items: "> :not(.ui-widget-header)"
    });
    $( "#progressbar" ).progressbar({
      value: false
    });
    var $loading = $('#progressbar').hide();
  $(document)
    .ajaxStart(function () {
      $loading.show();
    })
    .ajaxStop(function () {
      $loading.hide();
    });
    $("#clientSearch").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "ajax/assets.php?id=3",
                type: "POST",
                dataType: "json",
                data: {
                    term: $("#clientSearch").val()
                },
                success: function( data ) {
                  var array = $.map(data, function(item) {
                  return {
                      label: item.name,
                      id: item.clientid,
                      abbrev: item.state
                      };
                });
                var results = $.ui.autocomplete.filter(array, request.term);
                if(!results.length) {
                  results = [{label: "No results found", id: null}];
                }
                  response(results);}
            });
        },
        minLength: 0,
        focus: function(event, ui) {
          if(ui.item.label !== 'No results found') {$("#clientSearch").val(ui.item.label);} else {$("#clientSearch").val('');};
        },
        select: function(event, ui) {
          if(ui.item.id === null) {click(function(){$("#clientSearch").val('');});} else {window.location = "/clients.php?clientid=" + ui.item.id;};
        }
    });
    $("#locationSearch").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "ajax/assets.php?id=2",
                type: "POST",
                dataType: "json",
                data: {
                    term: $("#locationSearch").val()
                },
                success: function( data ) {
                  var array = $.map(data, function(item) {
                  return {
                      label: item.cName + ': ' + item.name,
                      id: item.locationid,
                      abbrev: item.state
                      };
                });
                var results = $.ui.autocomplete.filter(array, request.term);
                if(!results.length) {
                  results = [{label: "No results found", id: null}];
                }
                  response(results);}
            });
        },
        minLength: 0,
        focus: function(event, ui) {
          if(ui.item.label !== 'No results found') {$("#locationSearch").val(ui.item.label);} else {$("#locationSearch").val('');};
        },
        select: function(event, ui) {
          if(ui.item.id === null) {click(function(){$("#locationSearch").val('');});} else {window.location = "/locations.php?locationid=" + ui.item.id;};
        }
    });
    $("#computerSearch").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "ajax/assets.php?id=1",
                type: "POST",
                dataType: "json",
                data: {
                    term: $("#computerSearch").val()
                },
                success: function( data ) {
                  var array = $.map(data, function(item) {
                  return {
                      label: item.cName + ': ' + item.lName + ': ' + item.name,
                      id: item.computerid
                      };
                });
                var results = $.ui.autocomplete.filter(array, request.term);
                if(!results.length) {
                  results = [{label: "No results found", id: null}];
                }
                  response(results);}
            });
        },
        minLength: 0,
        focus: function(event, ui) {
          if(ui.item.label !== 'No results found') {$("#computerSearch").val(ui.item.label);} else {$("#computerSearch").val('');};
        },
        select: function(event, ui) {
          if(ui.item.id === null) {click(function(){$("#computerSearch").val('');});} else {window.location = "/computers.php?computerid=" + ui.item.id;};
        }
    });
    $('#sClear').click(function(){
        $("#clientSearch").val('');
        $("#locationSearch").val('');
        $("#computerSearch").val('');
      });
      $( "#avDownload" ).selectmenu();
      /*getAjaxData('#');
      	$('#avDownload').change(function() {
           	var id = $('#avDownload').val();
            getAjaxData(id);
            });
        function getAjaxData(id) {
          window.location = id;
        };*/
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
  <h5><a class='linkNav' href='index.php'>Home</a></h5>
</div>

<!-- Content -->
<div id="mainWrapper">
<div id="main-Header" class="ui-widget-header ui-corner-all">
  <div id="title"><h2 style="display: inline-block;"><?php echo COMPANY_NAME . " | Client Information Portal"; ?></h2></div>
  <div id="pgbar"><div id="progressbar"><div class="progress-label">Loading...</div></div></div>
</div>


<!-- Create icons on the tabs below -->
  <div id="main-bottom" class="ui-widget">

   <div id="tabs">
    <ul>
      <li><a href="#tab0"><img src="images/icons/information.png" height="16" width="16">  Site Information</a></li>
      <!--<li><a href="#tab1"><img src="images/check_256.png" height="16" width="16"> Service Status</a></li>-->
      <li><a href="#tab2"><img src="images/icons/bricks.png" height="16" width="16">  Health Scores</a></li>
      <li><a href="#tab3"><img src="images/icons/link.png" height="16"> Links/Downloads/Status</a></li>
      <!--<li><a href="ajax/clientList.php"><img src="images/info_256.png" height="16" width="16">  Client Information</a></li>-->
      <li><a href="#tab5"><img src="images/icons/error.png" height="16" width="16"> Top Recurring Alerts (30 Days)</a></li>
    </ul>

    <div id="tab0">
        <div id="main-top-right-left" style="width: 50%">
          <div class="ui-widget" style="width:400px;padding:10px 5px 10px 0;">
            <div class="ui-widget-header ui-corner-top" style="padding:3px;">
              Device Count
            </div>
            <div class="ui-widget-content ui-corner-bottom" style="height:115px;">
              <table class="noBorder" style="padding: 5px;">
                <tbody>
                  <tr>
                    <td><strong>Servers:</strong></td>
                    <td style="padding-left: 10px;text-align:right;"><?php echo $cnt['TotalSrv']; ?></td>
                    <td style="padding-left: 40px;"><strong>Total Clients:</strong></td>
                    <td style="padding-left: 10px;text-align:right;"><?php echo $cnt['clients']; ?></td>
                  </tr>
                  <tr>
                    <td><strong>Workstations:</strong></td>
                    <td style="padding-left: 10px;text-align:right;"><?php echo $cnt['TotalWS']; ?></td>
                    <td style="padding-left: 40px;"><strong>Total Locations:</strong></td>
                    <td style="padding-left: 10px;text-align:right;"><?php echo $cnt['locations']; ?></td>
                  </tr>
                  <tr>
                    <td><strong>(XP/2003):</strong></td>
                    <td style="padding-left: 10px;text-align:right;"><?php echo $cnt['TotalUS']; ?></td>
                    <td style="padding-left: 40px;"><strong>Total Computers:</strong></td>
                    <td style="padding-left: 10px;text-align:right;"><?php echo $cnt['compcnt']; ?></td>
                  </tr>
                </tbody>
              </table>
              <button id="help" style="margin-left:10px;margin-top:10px;">What's New?</button>
              <button id="ki" style="margin-left:10px;margin-top:10px;">Known Issues</button>
            </div>
          </div>
        </div>
        <div id="main-top-right-right" style="width: 50%">
          <div class="ui-widget" style="width:260px;padding:10px 10px 10px 0;">
            <div class="ui-widget-header ui-corner-top" style="padding-left:10px;padding: 3px;">
              Asset Search
            </div>
            <div class="ui-widget-content ui-corner-bottom" style="height:115px;">
              <table class="noBorder" style="padding: 5px;">
                <tbody>
                  <tr>
                    <td><strong><div class="ui-widget"><label for="client">Client: </label></strong></td>
                    <td style="padding-left: 10px;"><input type="text" id="clientSearch"></div></td>
                  </tr>
                  <tr>
                    <td><strong><div class="ui-widget"><label for="location">Location: </label></strong></td>
                    <td style="padding-left: 10px;"><input type="text" id="locationSearch"></div></td>
                  </tr>
                  <tr>
                    <td><strong><div class="ui-widget"><label for="computer">Computer: </label></strong></td>
                    <td style="padding-left: 10px;"><input type="text" id="computerSearch"></div></td>
                  </tr>
                </tbody>
              </table>
              <button id="sClear" style="margin-left:195px;">Clear</button>
            </div>
          </div>
        </div>
        <br>
        <hr>
        <p><?php include('ajax/clientList.php');?></p>
        <hr>
        <p><div id="hcChart" style="min-width: 100px; height: 300px; margin: 0 auto"></div>
        <p><strong>Please <a href='mailto:bobdole@email.com?subject=Health Score Website Request'>let me know</a> any features you'd like to see added and/or any bugs you find.</strong></p>
    </div>

    <div id="tab2">
      <div id="hcTabs">
        <ul>
          <li><a href="ajax/scAll.php"><img src="images/icons/bricks.png" height="16" width="16"> Standard</a></li>
          <li><a href="ajax/scEX.php"><img src="images/icons/bricks.png" height="16" width="16"> Exclusions</a></li>
          <li><a href="ajax/scExcl.php"><img src="images/icons/application_view_detail.png" height="16" width="16"> Exclusion List</a></li>
          <li><a href="ajax/scSpec.php"><img src="images/icons/bricks.png" height="16" width="16"> Team/Specialist Detail</a></li>
        </ul>
        <div id="tab21">
          <!-- All Scores -->
        </div>
        <div id="tab22">
          <!-- Exclusions Scores -->
        </div>
        <div id="tab23">
          <!-- client exclusions here -->
        </div>
      </div>
    </div>
    <div id="tab3">
      <div id="dlTabs">
        <ul>
          <li><a href="#tab31"><img src="images/icons/link.png" height="16" width="16"> Links &amp; AV Removal</a></li>
          <li><a href="ajax/ltDL.php"><img src="images/icon2.png" height="16" width="16"> LabTech Agents</a></li>
          <li><a href="#tab33"><img src="images/icons/silk/png/16/world_connect.png" height="16" width="16"> NOC Service Status</a></li>
        </ul>
        <div id="tab31">
          <!-- General Downloads -->
            <img src="images/marco.ico" height="16" width="16"><h2 style="display:inline">Common Marco Links and Downloads</h2>
            <ul class="dlList" id="jmenu" class="avList">
              <li><a href="https://docs.labtechsoftware.com/LabTech10/Default.htm" class="av">Link</a> | LabTech Documentation</li>
              <li><a href="https://docs.labtechsoftware.com/knowledgebase" class="av">Link</a> | LabTech Knowledgebase</li>
              <li><a href="http://university.connectwise.com/install/" class="av" target="blank">Link</a> | ConnectWise Installers</li>
              <li><a href="https://<?php echo LTURL; ?>/Labtech/Updates/ControlCenterInstaller.exe" class="av">Download</a> | LabTech Control Center</li>
              <li><a href="https://<?php echo LTURL; ?>/Labtech/Updates/ltnuke.zip" class="av">Download</a> | LT Nuke</li>
              <li><a href="https://<?php echo LTURL; ?>/Labtech/Updates/ltdiag.zip" class="av">Download</a> | LT Diag</li>
              <li><a href="https://<?php echo LTURL; ?>/Labtech/Updates/ltDiag.exe" class="av">Download</a> | LT AutoFix (BETA)</li>
              <li><a href="https://<?php echo LTURL; ?>/Labtech/Updates/LTserverPassword.zip" class="av">Download</a> | LT Server Password Fix (BETA)</li>
              <li><a href="https://<?php echo LTURL; ?>/Labtech/Updates/Agent_Uninstall.exe" class="av">Download</a> | Agent Uninstall (EXE)</li>
              <li><a href="https://<?php echo LTURL; ?>/Labtech/Updates/TreeSizeFree.exe" class="av">Download</a> | Tree Size Free</li>
              <li><a href="https://<?php echo LTURL; ?>/Labtech/Updates/WiresharkPortable-1.12.4.paf.exe" class="av">Download</a> | Wireshark Portable</li>
            </ul>
            <hr style="width:40%;margin-left:0;">
            <img src="images/icons/bug.png" height="16" width="16"><h2 style="display:inline">Common Antivirus Removal Tool Links</h2>
            <ul class="avList1" id="avList">
              <li><a href="https://www.avast.com/uninstall-utility" class="av" target="blank">Link</a> | Avast</li>
              <li><a href="http://www.avg.com/us-en/utilities" class="av" target="blank">Link</a> | AVG</li>
              <li><a href="http://www.bitdefender.com/support/How-to-uninstall-Bitdefender-333.html" class="av" target="blank">Link</a> | Bitdefender</li>
              <li><a href="http://kb.eset.com/esetkb/index?page=content&id=SOLN3527" class="av" target="blank">Link</a> | ESET</li>
              <li><a href="http://support.kaspersky.com/common/service.aspx?el=1464" class="av" target="blank">Link</a> | Kaspersky</li>
              <li><a href="http://www.malwarebytes.org/mbam-clean.exe" class="av" target="blank">Link</a> | Malwarebytes</li>
              <li><a href="http://download.mcafee.com/products/licensed/cust_support_patches/MCPR.exe" class="av" target="blank">Link</a> | McAfee</li>
              <li><a href="https://support.microsoft.com/en-us/kb/2435760" class="av" target="blank">Link</a> | Microsoft Security Essentials</li>
              <li><a href="http://www.pandasecurity.com/resources/sop/UNINSTALLER_08.exe" class="av" target="blank">Link</a> | Panda</li>
              <li><a href="http://www.pandasecurity.com/resources/sop/Cloud_AV_Uninstaller.exe" class="av" target="blank">Link</a> | Panda Cloud Internet Protection</li>
              <li><a href="http://www.sophos.com/support/knowledgebase/article/11019.html" class="av" target="blank">Link</a> | Sophos</li>
              <li><a href="ftp://ftp.symantec.com/public/english_us_canada/removal_tools/Norton_Removal_Tool.exe" class="av" target="blank">Link</a> | Symantec (Norton)</li>
              <li><a href="http://esupport.trendmicro.com/solution/en-us/1056551.aspx" class="av" target="blank">Link</a> | Trend Micro</li>
              <li><a href="http://esupport.trendmicro.com/solution/en-us/1059018.aspx" class="av" target="blank">Link</a> | Trend Micro Titanium</li>
              <li><a href="http://esupport.trendmicro.com/solution/en-us/1057237.aspx" class="av" target="blank">Link</a> | Trend Micro Worry Free Business</li>
              <li><a href="http://kb.threattracksecurity.com/articles/SkyNet_Article/How-to-Uninstall-VIPRE-Antivirus-and-VIPRE-Internet-Security" class="av" target="blank">Link</a> | Vipre</li>
              <li><a href="http://www.webroot.com/prodCheck/?pc=64150&origrc=1&oc=221&mjv=7&mnv=0&rel=6&bld=38&lang=en&loc=AUS&kc=ppc%60lkik^^afhgpewgfa&opi=2&omj=6&omn=1&osl=en&errid" class="av" target="blank">Link</a> | Webroot</li>
              <li><a href="http://download.microsoft.com/download/4/c/b/4cb845e7-1076-437b-852a-7842a8ab13c8/OneCareCleanUp.exe" class="av" target="blank">Link</a> | Windows Live OneCare</li>
            </ul>

        </div>
        <div id="tab32">
          <!-- LT Agent Download Tab -->
        </div>
        <div id="tab33">


        </div>
      </div>
      <br><hr>
      <p><strong>Please <a href='mailto:bobdole@hailtugboats.com?subject=Health Score Website Request'>let me know</a> any additional links or downloads to add.</strong></p>
    </div>
    <div id="tab4">
      <!-- Client List table -->
    </div>
    <div id="tab5">
      <table id="tixTopCat" class="stripe compact" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th style='text-align: left;'>Client Name</th>
            <th style='text-align: left;'>Computer Name</th>
            <th style='text-align: left;'>Ticket Category</th>
            <th style='text-align: left; max-width:400px !important;'>Monitor</th>
            <th>Count</th>
          </tr>
        </thead>
        <tbody>
        <?php
        foreach($pdo->query($tixSQL) as $row) {
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
            <td><a href='clients.php?clientid=" . $row['clientid'] . "'>" . $row['Client Name'] . "</a></td>
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
        <?php echo CITYSTATE; ?> Traffic
      </div>
      <div class="ui-widget-content ui-corner-bottom" style="height:250px;">
        <div id='map' style='height:100%;'></div>
      </div>
    </div>
  </div>

</div>
<?php $mainClass->buildTemplate('footer'); ?>
<div id="helpDialog" title="Help">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    <p style="padding-left:5px;"><strong>Mappings from old site and notable new features:</strong>
      <ul>
        <li>Completely redesigned interface</li>
        <li>All ID's link to LabTech</li>
        <li>All download links for EXE and MSI on client pages</li>
        <li>Recurring alerts for clients/locations/computers</li>
        <li>Latest tray tickets created/CW integration</li>
        <li>Service Status table for realtime updates</li>
      </ul>
  </p>
</div>
<div id="knownIssues" title="Known Issues">
  <p>
    <span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 50px 0;"></span>
    <p style="padding-left:5px;"><strong>Known Issues:</strong>
      <ul>
        <li>Client and computer exclusion export does not include the exclusions (non text content)</li>
        <li>Clients are restricted to a single Network Specialist</li>
      </ul>
  </p>
</div>

<!-- http://where.yahooapis.com/v1/places.q('Barrie CA')?appid=[yourappidhere] -->
<!-- http://maps.googleapis.com/maps/api/geocode/json?address={zipcode} -->
<script>
$.simpleWeather({
  location: '<?php echo CITYSTATE; ?>',
  woeid: '<?php echo WOEID; ?>',
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
/*** Google Maps ***/
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
  var trafficLayer = new google.maps.TrafficLayer();
  trafficLayer.setMap(map);
}

var options = {
  chart: {renderTo: 'hcChart', type: 'spline', zoomType: 'x', resetZoomButton: {position: {x: 0, y: -30}}},
  title: {text: 'Health Score Trending', x: -20},
  subtitle: {text: 'All Clients Last 180 Days', x: -20},
  xAxis: {categories: [], title: {text: 'Date'}},
  yAxis: {title: {text: 'Score'}, plotLines: [{value: 0, width: 1, color: '#808080'}]},
  legend: {layout: 'vertical', align: 'right', verticalAlign: 'middle', borderWidth: 0},
  tooltip: {
    positioner: function (labelWidth, labelHeight, point) {
      var tooltipX, tooltipY;
      if (point.plotX + options.plotLeft < labelWidth && point.plotY + labelHeight > options.plotHeight) {
        tooltipX = options.plotLeft;
        tooltipY = options.plotTop + options.plotHeight - 2 * labelHeight - 10;
      } else {
        tooltipX = options.plotLeft;
        tooltipY = options.plotTop + options.plotHeight - labelHeight;
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
  $.getJSON("config/allScoreData.php", function(json) {
    options.xAxis.categories = json[0]['data']; //xAxis: {categories: []}
    options.series[0] = json[1];
    options.series[1] = json[2];
    options.series[2] = json[3];
    options.series[3] = json[4];
    options.series[4] = json[5];
    options.series[5] = json[6];
    options.series[6] = json[7];
    options.series[7] = json[8];
    chart = new Highcharts.Chart(options);
  });
</script>
</body>
</html>
