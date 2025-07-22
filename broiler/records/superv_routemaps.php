<?php


include "../newConfig.php";
global $page_title; $page_title = "Supervisor Route Map";
include "header_head.php";
include "../broiler_check_tableavailability.php";
$supervisors_code = $_REQUEST['supervisors'];
if ($_REQUEST['date'] != '') {
  $date = date("Y-m-d", strtotime($_REQUEST['date']));
} else {
  $date = date('Y-m-d');
}
$db = $_SESSION['dbase'];
$user_code = $_SESSION['userid'];
if(isset($_REQUEST['submit_report']) == true){
    mysqli_query($conns,"INSERT INTO `client_maps_usage`(`dbname`, `empcode`, `api_name`, `page`) VALUES ('$db','$user_code','MAPs Display','superv_routemaps')");
}


$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
  $farm_code[$row['code']] = $row['code'];
  $farm_ccode[$row['code']] = $row['farm_code'];
  $farm_name[$row['code']] = $row['description'];
  $farm_branch[$row['code']] = $row['branch_code'];
  $farm_line[$row['code']] = $row['line_code'];
  $farm_supervisor[$row['code']] = $row['supervisor_code'];
  $farm_farmer[$row['code']] = $row['farmer_code'];
  $farm_latitude[$row['code']] = $row['latitude'];
  $farm_longitude[$row['code']] = $row['longitude'];
}

$supervisors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT empcode  FROM `main_access` WHERE `db_emp_code` LIKE '$supervisors_code'"))['empcode'];

$sql = "SELECT * FROM `broiler_employee`";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
  $supervisor_code[$row['code']] = $row['code'];
  $supervisor_name[$row['code']] = $row['name'];
}


$total_km = mysqli_fetch_assoc(mysqli_query($conn, "SELECT total_km FROM `trip_sheet` a where added_empcode = '$supervisors' and `date` = '$date'   ORDER BY `id` DESC limit 0,1"))['total_km'];

$waypoints = array();

$q3_Start = "SELECT latitude,longitude,farm_code,remarks,updated as date FROM `trip_sheet` a where added_empcode = '$supervisors' and trip_type = 'Start' and `date` = '$date' ";

$q3_Start = mysqli_query($conn, $q3_Start) or die(mysqli_error($conn));

$r3_Start = mysqli_fetch_assoc($q3_Start);

$q3_Start_cou = mysqli_num_rows($q3_Start);
if ($q3_Start_cou > 0) {
  $resultFlag = 1;
}

$origin1 = $r3_Start['latitude'];
$origin2 = $r3_Start['longitude'];
$description = $origin1 . "," . $origin2 . "</br>" . $r3_Start['farm_code'] . "-" . $r3_Start['remarks'] . "</br>" . "Entry Date: " . date("d.m.Y H:m:i", strtotime($r3_Start['date']));

if ($origin1 != '' && $origin2 != '' && $origin1 != '0.0' && $origin2 != '0.0') {
  $waypoints[] = array("title" => "Trip Start", "lat" => $origin1, "lng" => $origin2, "description" => $description);
}

$added_date = $date . "%";

$q3 = "SELECT latitude,longitude,farm_code,brood_age,mortality,addedtime as date FROM `broiler_daily_record` a where addedemp = '$supervisors' and `addedtime` like '$added_date' and  latitude != '0.0' ORDER BY `a`.`incr` ASC";

$q3 = mysqli_query($conn, $q3) or die(mysqli_error($conn));

$i = 0;

while ($r3 = mysqli_fetch_assoc($q3)) {

  if ($r3['latitude'] != '' && $r3['longitude'] != '' && $r3['latitude'] != '0.0' && $r3['longitude'] != '0.0') {

    $description = $r3['latitude'] . "," . $r3['longitude'] . "</br>" . "Age: " . $r3['brood_age'] . "</br>" . "Entry Date: " . date("d.m.Y H:i:s", strtotime($r3['date'])) . "</br>" . "Mortality: " . $r3['mortality'];

    $waypoints[] = array("title" => $farm_name[$r3['farm_code']], "lat" => $r3['latitude'], "lng" => $r3['longitude'], "description" => $description);
  }



  // $waypoints[] = array("location"=> "new google.maps.LatLng(".$r3['latitude'].",".$r3['longitude']."), stopover:true");

  $i++;
}

if($count134 > 0){
  $q3 = "SELECT latitude,longitude,farm,remarks,addedtime as date FROM `broiler_visit_emp` a where addedemp = '$supervisors' and `addedtime` like '$added_date' and  latitude != '0.0' ORDER BY `a`.`incr` ASC";

  $q3 = mysqli_query($conn, $q3) or die(mysqli_error($conn));
  
  $i = 0;
  
  while ($r3 = mysqli_fetch_assoc($q3)) {
  
    if ($r3['latitude'] != '' && $r3['longitude'] != '' && $r3['latitude'] != '0.0' && $r3['longitude'] != '0.0') {
  
      $description = $r3['latitude'] . "," . $r3['longitude'] . "</br>" . "Farm: " . $farm_name[$r3['farm']] . "</br>" . "Entry Date: " . date("d.m.Y H:i:s", strtotime($r3['date']));
  
      $waypoints[] = array("title" => $r3['remarks'], "lat" => $r3['latitude'], "lng" => $r3['longitude'], "description" => $description);
    }
  
  
  
    // $waypoints[] = array("location"=> "new google.maps.LatLng(".$r3['latitude'].",".$r3['longitude']."), stopover:true");
  
    $i++;
  }
  
}

$q3_End = "SELECT latitude,longitude,farm_code,remarks,updated as date FROM `trip_sheet` a where added_empcode = '$supervisors' and trip_type = 'End' and `date` = '$date' ";

$q3_End = mysqli_query($conn, $q3_End) or die(mysqli_error($conn));

$q3_End_cou = mysqli_num_rows($q3_End);
if ($q3_End_cou > 0) {
  $resultFlag = 1;
}

$r3_End = mysqli_fetch_assoc($q3_End);

$destination1 = $r3_End['latitude'];
$destination2 = $r3_End['longitude'];
$description = $destination1 . "," . $destination2 . "</br>" . $r3_End['farm_code'] . "-" . $r3_End['remarks'] . "</br>" . "Entry Date: " . date("d.m.Y H:m:i", strtotime($r3_End['date']));

if ($destination1 != '' && $destination2 != '' && $destination1 != '0.0' && $destination2 != '0.0') {
  $waypoints[] = array("title" => "Trip End", "lat" => $destination1, "lng" => $destination2, "description" => $description);
}

function GetDrivingDistance($conns,$lat1, $lat2, $long1, $long2)
{

  if(isset($_REQUEST['submit_report']) == true){
      $db = $_SESSION['dbase'];
      $user_code = $_SESSION['userid'];

      mysqli_query($conns,"INSERT INTO `client_maps_usage`(`dbname`, `empcode`, `api_name`, `page`) VALUES ('$db','$user_code','Distance API','superv_routemaps')");
  }

  $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyAxw0Y5X9qtkFf4nc75Tmne2u6SquRv-AA&origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);
  curl_close($ch);
  $response_a = json_decode($response, true);
  $dist = str_replace(',', '.', $response_a['rows'][0]['elements'][0]['distance']['text']);
  $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

  // return array('distance' => $dist, 'time' => $time);
  return $dist;
}

function haversine($lat1, $lon1, $lat2, $lon2)
{
  $R = 6371; // Earth radius in kilometers
  $dLat = deg2rad($lat2 - $lat1);
  $dLon = deg2rad($lon2 - $lon1);
  $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
  $distance = $R * $c;
  return $distance;
}

function distance($lat1, $lon1, $lat2, $lon2, $unit)
{

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return round(($miles * 1.609344), 2) . " km";
  } else if ($unit == "N") {
    return ($miles * 0.8684);
  } else {
    return $miles;
  }
}

//var_dump($waypoints);
define("API_KEY", "AIzaSyAxw0Y5X9qtkFf4nc75Tmne2u6SquRv-AA")
?>
<html>

<head>
  <title>Poulsoft Solutions</title>

  <!-- jQuery Library -->
  <script src="../../col/jquery-3.5.1.js"></script>

  <!-- Datatable JS -->
  <script src="../../col/jquery.dataTables.min.js"></script>

  <script>
    var exptype = '<?php echo $excel_type; ?>';
    var url = '<?php echo $url; ?>';
    if (exptype.match("excel")) {
      window.open(url, "_BLANK");
    }
  </script>

  <link href="../datepicker/jquery-ui.css" rel="stylesheet">
  <style>
    .col-md-6 {
      position: relative;
      left: 200px;
      max-width: 0%;
    }

    .col-md-5 {
      position: relative;
      left: 200px;

    }

    div.dataTables_wrapper div.dataTables_filter {

      text-align: left;
    }

    table thead,
    table tfoot {
      position: sticky;
    }

    table thead {
      inset-block-start: 0;
      /* "top" */
    }

    table tfoot {
      inset-block-end: 0;
      /* "bottom" */
    }
  </style>


  <?php
  if ($excel_type == "print") {
    echo '<style>body { padding:10px;text-align:center; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead2_empty_row { display:none; }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
  } else {
    echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
            table.tbl { left:0;margin-right: auto;visibility:visible; }
            table.tbl2 { left:0;margin-right: auto; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
  }

  ?>
</head>
<style>
  body {
    font-family: Arial;
  }

  #map-layer {
    margin: 20px 20px;
    max-width: 1500px;
    min-height: 600px;
  }

  #btnAction {
    background: #3878c7;
    padding: 10px 40px;
    border: #3672bb 1px solid;
    border-radius: 2px;
    color: #FFF;
    font-size: 0.9em;
    cursor: pointer;
    display: block;
  }

  #btnAction:disabled {
    background: #6c99d2;
  }

  input[type=text] {
    width: 100%;
    padding: 5px 5px;
    margin: 8px 0;
    box-sizing: border-box;
  }
</style>

<body>

  <h1>Supervisor Route Map</h1>
  <div class="row">
    <div class="m-2 form-group">
      <label>From Date</label>
      <input type="text" name="date" id="date" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y", strtotime($date)); ?>" />
    </div>
    <div class="m-2 form-group">
      <label>Supervisor</label>
      <select name="supervisors" id="supervisors" class="form-control select2" >
        <option value="select" <?php if ($supervisors == "select") {
                                  echo "selected";
                                } ?>>-Select-</option>
        <?php foreach ($supervisor_code as $scode) {
          if ($supervisor_name[$scode] != "") { ?>
            <option value="<?php echo $scode; ?>" <?php if ($supervisors_code == $scode) {
                                                    echo "selected";
                                                  } ?>><?php echo $supervisor_name[$scode]; ?></option>
        <?php }
        } ?>
      </select>
    </div>
    <div class="m-2 form-group">
      <label>Km From Trip Sheet</label>
      <input type="text" name="date" id="date" class="form-control" style="width:110px;" readonly value="<?php echo $total_km; ?>" />
    </div>
    <div class="m-2 form-group">
    <input style="height:50px; width:150px;color: #ffffff;background-color: #04AA6D;" type="submit" name="submit_report" id="submit_report" onclick="reload_page()" >
    </div>
  </div>
  <div id="map-layer"></div>
  <ul class="location-list" id="locationList"></ul>

  <div class="row">
    <?php
    for ($a = 0; $a < count($waypoints); $a++) {

      if ($a != 0) {
        $b = $a - 1;
        //$diff_dis = GetDrivingDistance($conns,$waypoints[$b]['lat'],$waypoints[$a]['lat'],$waypoints[$b]['lng'],$waypoints[$a]['lng']);
        $diff_dis = distance($waypoints[$b]['lat'], $waypoints[$b]['lng'], $waypoints[$a]['lat'], $waypoints[$a]['lng'], "K");
      } else {
        $diff_dis = 0;
      }


      $diff_dis_km = substr($diff_dis, 0, -3);

      $tot_diff_dis_km += (float)$diff_dis_km;




      $display_values = $a + "1" . ") " . $waypoints[$a]['title'] . " - ";

    ?>

      <h6 style="border:orange; border-width:3px; border-style:solid;padding: 10px;margin: 10px;"><span id="<?php echo $a; ?> " ><?php echo $display_values; ?></span><a href="https://broiler.poulsoft.net/records/ShowDirection.php?lat1=<?php echo $waypoints[$b]['lat']; ?>&lng1=<?php echo $waypoints[$b]['lng']; ?>&lat2=<?php echo $waypoints[$a]['lat']; ?>&lng2=<?php echo $waypoints[$a]['lng']; ?>&farm_name=<?php echo "Diff Km From" . $waypoints[$b]['title'] . " To " . $waypoints[$a]['title']; ?>" target="_blank"><span><?php echo $diff_dis; ?></span></a></h6>

    <?php
    }
    ?>
  </div>
  <div class="row">
    <div class="m-2 form-group">
      <label>Km From Maps</label>
      <input type="text" name="date" id="date" class="form-control" style="width:110px;" readonly value="<?php echo $tot_diff_dis_km; ?>" />
    </div>
    <div class="m-2 form-group">
      <label>Difference Km between Maps and Tripsheet</label>
      <input type="text" name="date" id="date" class="form-control" style="width:110px;" readonly value="<?php echo $total_km - $tot_diff_dis_km; ?>" />
    </div>
   
  </div>
  <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo API_KEY; ?>&callback=LoadMap" async defer></script>
  <script type="text/javascript">
    function show_km_maps() {

      alert('<?php echo $tot_diff_dis_km; ?>');

    }

    function reload_page() {
      var supervisors = document.getElementById("supervisors").value;
      var date = document.getElementById("date").value;
      var ccid = '<?php echo $ccid; ?>';
      window.location.href = 'superv_routemaps.php?ccid=' + ccid + '&supervisors=' + supervisors + '&date=' + date+ '&submit_report=true';
    }


    //Array of JSON objects.
    var markers = <?php echo json_encode($waypoints); ?>;
    var markers_click = [];
    window.onload = function() {
      LoadMap();
    }

    function LoadMap() {
      var locationList = document.getElementById('locationList');
      var directionsService = new google.maps.DirectionsService();
      var directionsDisplay = new google.maps.DirectionsRenderer({
        suppressMarkers: true
      });

      var mapOptions = {
        center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      var infoWindow = new google.maps.InfoWindow();
      var latlngbounds = new google.maps.LatLngBounds();
      var map = new google.maps.Map(document.getElementById("map-layer"), mapOptions);

      directionsDisplay.setMap(map);

      var cout = markers.length - 1;
      var waypts = [];
      var a;
      var c;
      var f;
      for (var i = 0; i < markers.length; i++) {
        var J = i + 1;

        var data = markers[i]

        if (i == 0) {
          a = data.lat;
          c = data.lng;
        } else if (i == cout) {
          d = data.lat;
          f = data.lng;

        } else {
          waypts.push({
            location: new google.maps.LatLng(data.lat, data.lng),
            stopover: true
          });
        }


        var myLatlng = new google.maps.LatLng(data.lat, data.lng);

        var marker = new google.maps.Marker({
          // icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + J + '|BF42F0|FFFFFF',
          position: myLatlng,
          map: map,
          title: data.title,
          label: {
            text: J.toString(),
            color: "white",
          }
        });
        (function(marker, data) {
          google.maps.event.addListener(marker, "click", function(e) {
            infoWindow.setContent("<div style = 'width:200px;min-height:20px'>" + data.title + "</div><div style = 'width:200px;min-height:40px'>" + data.description + "</div>");
            infoWindow.open(map, marker);
           // highlightMarker(marker);
          });
        })(marker, data);

        markers_click.push(marker);
      }


      var request = {
        origin: new google.maps.LatLng(a, c),
        destination: new google.maps.LatLng(d, f),
        waypoints: waypts,
        travelMode: google.maps.DirectionsTravelMode.DRIVING

      };


      directionsService.route(request, function(response, status) {

        if (status == google.maps.DirectionsStatus.OK) {
          var markerCounter = 1;
          directionsDisplay.setDirections(response);

        }
      });
    }


    function highlightMarker(marker) {
      // Reset all markers to default icon
      for (var i = 0; i < markers_click.length; i++) {
        markers_click[i].setIcon(null);
      }
      // Set clicked marker to a different icon to highlight it
      marker.setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png');
    }
  </script>
  <script src="../datepicker/jquery/jquery.js"></script>
  <script src="../datepicker/jquery-ui.js"></script>
</body>

</html>
<?php
include "header_foot.php";
?>