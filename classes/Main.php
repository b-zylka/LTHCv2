<?php

class Main {
  function __construct() { }
  function __destruct() { }

  public function loadModules() {
		$module = array();
		$mod_count = count($module);
  }

	public function loadPage($name) {
    include('pages/' .$name .'.php');
	}

	public function buildTemplate($type) {
		if ($type === 'head') {
  		echo "
      <script src='js/pace.min.js'></script>
      <link href='css/pace-corner-yellow.css' rel='stylesheet' />
      <link rel='shortcut icon' href='images/marco.ico' type='image/x-icon'/>
      <link rel='stylesheet' href='css/test.css' />
      <link rel='stylesheet' href='css/foundation-icons.css' />
      <link rel='stylesheet' type='text/css' href='https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' />
      <link rel='stylesheet' type='text/css' href='//cdn.datatables.net/plug-ins/1.10.7/integration/jqueryui/dataTables.jqueryui.css' />
      <link rel='stylesheet' type='text/css' href='//cdn.datatables.net/tabletools/2.2.4/css/dataTables.tableTools.css' />
      <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/buttons/1.0.0/css/buttons.dataTables.min.css'/>
      <link rel='stylesheet' type='text/css' href='css/weather/weather-icons.min.css'>
      <link rel='stylesheet' type='text/css' href='css/theWeather.css'>

      <script type='text/javascript' src='//code.jquery.com/jquery-1.11.3.min.js'></script>
      <script type='text/javascript' src='https://code.jquery.com/ui/1.11.4/jquery-ui.min.js'></script>
      <script type='text/javascript' src='//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js'></script>
      <script type='text/javascript' src='//cdn.datatables.net/responsive/1.0.6/js/dataTables.responsive.min.js'></script>
      <script type='text/javascript' src='//cdn.datatables.net/tabletools/2.2.4/js/dataTables.tableTools.min.js'></script>
      <script type='text/javascript' src='https://cdn.datatables.net/buttons/1.0.0/js/dataTables.buttons.min.js'></script>
      <script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
      <script type='text/javascript' src='//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js'></script>
      <script type='text/javascript' src='//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js'></script>
      <script type='text/javascript' src='//cdn.datatables.net/buttons/1.0.0/js/buttons.html5.min.js'></script>
      <script type='text/javascript' src='//cdn.datatables.net/buttons/1.0.0/js/buttons.print.min.js'></script>
      <!--<script type='text/javascript' src='js/jquery.ba-outside-events.min.js'></script>-->
      <script src='http://code.highcharts.com/highcharts.js'></script>
      <script src='http://code.highcharts.com/modules/exporting.js'></script>
      <!--<script type='text/javascript' src='highcharts/js/themes/gray.js'></script>-->
      <script src='js/jquery.easytabs.min.js' type='text/javascript'></script>
      		";
    }

		if ($type === 'nav') {
      $db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      $sql4 = "SELECT DISTINCT `Client Specialist` AS client_spec FROM v_extradataclients WHERE `client specialist` != '' ORDER BY client_spec asc";
      $return = $db->query($sql4);
      while ($row = $db->fetch_array($return)) {
        $encode = urlencode($row['client_spec']);
        echo "<li><a href='#'>".$row['client_spec']."</a></li>";
      }
		}

		if ($type === 'footer') {
      echo "
      <div id='theFooter'>
        <hr>Copyright &copy; 2015 <a href='http://www.marconet.com/' class='foot'>marconet.com</a>
      </div>
      <script src='js/jquery.simpleWeather.js'></script>
      <!--<script src='https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=true'></script>-->
      <script src='https://maps.googleapis.com/maps/api/js?callback=initMap' async defer></script>
      ";
		}

    if ($type ==='scripts'){
      echo "

      ";
    }
	}
}
?>
