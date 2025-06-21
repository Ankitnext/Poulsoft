<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;


foreach ($_SERVER as $key => $value) {
    if ($key == "HTTP_TOKEN") {
        $token = $value;
    }
}

$requested_data = json_decode(file_get_contents('php://input'), true);


$db =  $requested_data['db'];


include "APIconfig.php";
include "number_format_ind.php";

$fdate = date("Y-m-d", strtotime($requested_data['fromDate']));
$tdate = date("Y-m-d", strtotime($requested_data['toDate']));

if ($db == '') {
    $result = 'Fail';
    $post_data = array(
        'status' => $result,
        'message' => 'Please Provide DataBase Name!!',
        'data' => array()
    );

    echo json_encode($post_data);
} else {


    $resultFlag = 0;
    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $imagename = $row['imagename'];
        $cdetails = $row['cdetails'];
        $company_name = $row['cname'];
        $qr_img_path = $row['qr_img_path'];
        $logopath = $row['logopath'];
    };
    $html .= '<head><link rel="stylesheet" type="text/css" href="reportstyle.css">
    <style>
        body{
            font-size: 15px;
            font-weight: bold;
            color: black;
        }
        .thead2,.tbody1 {
            font-size: 15px;
            font-weight: bold;
            padding: 1px;
            color: black;
        }
        .formcontrol {
            font-size: 15px;
            font-weight: bold;
            color: black;
            height: 23px;
            border: 0.1vh solid gray;
        }
        .formcontrol:focus {
            color: black;
            height: 23px;
            border: 0.1vh solid gray;
            outline: none;
        }
        .tbody1 td {
            font-size: 15px;
            font-weight: bold;
            color: black;
            padding-right: 5px;
			padding-left: 5px;
            text-align: right;
			text-transform: uppercase;
        }
        .reportselectionlabel{
            font-size: 15px;
        }
        .table-bordered {
            border: 2px solid black;
            border-collapse: collapse;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid black;
            padding: 5px;
        }
		.table1,.table1 thead,.table1 tr,.table1 td {
			border-collapse:collapse;
			border: 1px solid black;
			text-transform: uppercase;
		}
		.thead2 th {
			text-align:center;
			border-collapse:collapse;
			border: 1px solid black;
			text-transform: uppercase;
		}
		.reportheaderlabel td {
			text-transform: uppercase;
		}
		
    </style>
</head>';
    $html .= '<table align="center" class="reportheadermenu">
				<tr>';

    if ($logopath != '') {
        $html .= '<td><img src="https://' . $_SERVER['SERVER_NAME'] . '/' . $logopath . '" height="150px"/></td>';
    } else {
        $html .= '<td><img src="" height="150px"/></td>';
    }
    $html .= '<td>' . $cdetails . '</td> <?php } ?>
					<td align="center">
						<h3>Daily Rates Report</h3>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;' . date("d.m.Y", strtotime($fdate)) . '</label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;' . date("d.m.Y", strtotime($tdate)) . '</label><br/>';

    $html .= '</td><td></td></tr></table>';


    $html .= '<table class="table1" style="min-width:100%;line-height:23px;">';
    $html .= '<thead class="thead2" style="background-color: #98fb98;">';
    $html .= '<th>Date</th>';
    $html .= '<th>BLL</th>';
    $html .= '<th>CUT</th>';
    $html .= '<th>BLL FARM</th>';
    $html .= '<th>BHL</th>';
    $html .= '<th>CUT</th>';
    $html .= '<th>BHL FARM</th>';
    $html .= '</thead>';
    $html .= '<tbody class="tbody1" style="background-color: #f4f0ec;">';


    $sql = "SELECT * FROM `broiler_daily_rates` WHERE date BETWEEN '$fdate' AND '$tdate' AND active = 1 AND dflag = 0 ORDER BY `date` ASC";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $resultFlag = 1;
        $html .= "<tr>";
        $html .= "<td>" . date("d.m.Y", strtotime($row['date'])) . "</td>";
        $html .= "<td>" . number_format_ind($row['bll_dr']) . "</td>";
        $html .= "<td>" . number_format_ind($row['bll_max_cut']) . "</td>";
        $html .= "<td>" . number_format_ind($row['bll_dr'] - $row['bll_max_cut']) . "</td>";
        $html .= "<td>" . number_format_ind($row['bhl_dr']) . "</td>";
        $html .= "<td>" . number_format_ind($row['bhl_max_cut']) . "</td>";
        $html .= "<td>" . number_format_ind($row['bhl_dr'] - $row['bhl_max_cut']) . "</td>";
        $html .= "</tr>";
    }
    $html .= '</tbody>';
    $html .= '</table>';

    if ($resultFlag) {
        $result = 'Success';
        $post_data = array(
            'status' => $result,
            'message' => 'Found Some Daily Rates List!!',
            'html' => $html
        );

        echo json_encode($post_data);
    } else {
        $result = 'Fail';
        $post_data = array(
            'status' => $result,
            'message' => 'No  Daily Rates Found !!!!',
            'data' => array()
        );

        echo json_encode($post_data);
    }
}
