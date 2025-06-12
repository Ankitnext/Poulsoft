<?php
//broiler_placementplanning_details.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

    include "header_head.php";
}else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
}
$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; }

?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script src="../../col/jquery-3.5.1.js"></script>
        <script src="../../col/jquery.dataTables.min.js"></script>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            .col-md-6 {
                position: relative;  left: 200px;
            max-width: 0%;
}
.col-md-5{
                position: relative;  left: 200px;
            
}
            div.dataTables_wrapper div.dataTables_filter {
                
                text-align: left;
            }
            table thead,
            table tfoot {
  position: sticky;
}
table thead {
  inset-block-start: 0; /* "top" */
}
table tfoot {
  inset-block-end: 0; /* "bottom" */
}

        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
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
    <body align="center">
        <table class="tbl" align="center" width="800px">
        <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
        ?>
            <thead class="thead1" align="center" width="800px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="8" align="center"><?php echo $row['cdetails']; ?>
                        <h5>Placement Planning Report</h5>
                        <?php
                            $trnum = $_GET['id'];
                            $sql_record = "SELECT * FROM `broiler_placementplan` WHERE `trnum` = '$trnum' AND `dflag` = '0' ORDER BY `id` ASC"; $query = mysqli_query($conn,$sql_record);
                            while($row = mysqli_fetch_assoc($query)){ $week_no = $row['week_no']; $from_date = date("d.m.Y",strtotime($row['from_date'])); $to_date = date("d.m.Y",strtotime($row['to_date'])); }
                        ?>
                        <label style="font-size: 15px;">Week No: <b style="color:green;"><?php echo $week_no; ?></b></label>
                        <label style="font-size: 15px;">From Date: <b style="color:green;"><?php echo $from_date; ?></b></label>
                        <label style="font-size: 15px;">To Date: <b style="color:green;"><?php echo $to_date; ?></b></label>
                    </th>
                </tr>
            </thead>
            <?php } ?>
        <!--<table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr>
                <td>
                    <div id='control_sh'>
                        <input type="checkbox" class="hide_show"><span>Transaction No.</span>
                        <input type="checkbox" class="hide_show"><span>Entry Date</span>
                        <input type="checkbox" class="hide_show"><span>Farm</span>
                        <input type="checkbox" class="hide_show"><span>Branch</span>
                        <input type="checkbox" class="hide_show"><span>Village</span>
                        <input type="checkbox" class="hide_show"><span>Sq. Feet</span>
                        <input type="checkbox" class="hide_show"><span>Line Name</span>
                        <input type="checkbox" class="hide_show"><span>Supervisor Name</span>
                        <input type="checkbox" class="hide_show"><span>Batch Code</span>
                        <input type="checkbox" class="hide_show"><span>FCR</span>
                        <input type="checkbox" class="hide_show"><span>Mort%</span>
                        <input type="checkbox" class="hide_show"><span>Avg BodyWt</span>
                        <input type="checkbox" class="hide_show"><span>Mean Age</span>
                        <input type="checkbox" class="hide_show"><span>Batch Code</span>
                        <input type="checkbox" class="hide_show"><span>FCR</span>
                        <input type="checkbox" class="hide_show"><span>Mort%</span>
                        <input type="checkbox" class="hide_show"><span>Avg BodyWt</span>
                        <input type="checkbox" class="hide_show"><span>Mean Age</span>
                        <input type="checkbox" class="hide_show"><span>Remarks</span>
                    </div>
                </td>
            </tr>
        </table>-->
            <thead class="thead3" align="center" style="width:800px;">
                <tr style="text-align:center;">
                    <th rowspan="3">Transaction No.</th>
                    <th rowspan="3">Placement Date</th>
                    <th rowspan="3">Farm</th>
                    <th rowspan="3">Branch</th>
                    <th rowspan="3">Village</th>
                    <th rowspan="3">Sq. Feet</th>
                    <th rowspan="3">Line Name</th>
                    <th rowspan="3">Supervisor Name</th>
                    <th rowspan="3">Chicks Placement</th>
                   
                    <th rowspan="3">Remarks</th>
                    <!-- <th>&nbsp;</th> -->
                </tr>
                <!-- <tr style="text-align:center;">
                    <th>&nbsp;</th>
                </tr> -->
                
            </thead>
            <tbody class="tbody1">
                <?php
                $trnum = $_GET['id'];
                $sql_record = "SELECT * FROM `broiler_placementplan` WHERE `trnum` = '$trnum' AND `dflag` = '0' ORDER BY `id` ASC"; $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title="Transaction No."><?php echo $row['trnum']; ?></td>
                    <td title="Entry Date"><?php echo $row['date']; ?></td>
                    <td title="Farm"><?php echo $farm_name[$row['farm_code']]; ?></td>
                    <td title="Branch"><?php echo $branch_name[$row['branch_code']]; ?></td>
                    <td title="Village"><?php echo $row['village_code']; ?></td>
                    <td title="Sq. Feet" style="text-align: right;"><?php echo $row['sq_feet']; ?></td>
                    <td title="Line Name"><?php echo $line_name[$row['line_code']]; ?></td>
                    <td title="Supervisor Name"><?php echo $emp_name[$row['supervisor_code']]; ?></td>
                    <td title="Chicks Placement" style="text-align: right;"><?php echo $row['chicks_place']; ?></td>
                    <td title="Remarks"><?php echo $row['remarks']; ?></td> 

                    <!-- <td title="Batch Code"><?php echo $batch_name[$row['lb_batch_code']]; ?></td>
                    <td title="FCR"><?php echo $row['lb_fcr']; ?></td>
                    <td title="Mort%"><?php echo $row['lb_mort']; ?></td>
                    <td title="Avg BodyWt"><?php echo $row['lb_avg_bodywt']; ?></td>
                    <td title="Mean Age"><?php echo $row['lb_mean_age']; ?></td>
                    <td title="Batch Code"><?php echo $batch_name[$row['blb_batch_code']]; ?></td>
                    <td title="FCR"><?php echo $row['blb_fcr']; ?></td>
                    <td title="Mort%"><?php echo $row['blb_mort']; ?></td>
                    <td title="Avg BodyWt"><?php echo $row['blb_avg_bodywt']; ?></td>
                    <td title="Mean Age"><?php echo $row['blb_mean_age']; ?></td> -->
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
<script>
  $(document).ready(function(){
    var table =  $('#mine').DataTable({       
        paging: false,
    });
	
	$("#hide_show_all").on("change",function(){
	var hide = $(this).is(":checked");
	$(".hide_show").prop("checked", hide);

	if(hide){
		$('#mine tr th').hide(100);
		$('#mine tr td').hide(100);
	}else{
		$('#mine tr th').show(100);
		$('#mine tr td').show(100);
	}
});

$(".hide_show").on("change",function(){
	var hide = $(this).is(":checked");
	
	var all_ch = $(".hide_show:checked").length == $(".hide_show").length;

	$("#hide_show_all").prop("checked", all_ch);
	
	var ti = $(this).index(".hide_show");
	
$('#mine tr').each(function(){
	if(hide){
		$('td:eq(' + ti + ')',this).hide(100);
		$('th:eq(' + ti + ')',this).hide(100);
	}else{
		$('td:eq(' + ti + ')',this).show(100);
		$('th:eq(' + ti + ')',this).show(100);
	}
});

});
$('#mine tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

$('#myInput').keyup( function() {
        table.draw();
    } );
    $('input.column_filter').on( 'keyup click', function () {
           filterColumn( $(this).parents('tr').attr('data-column') );
       });
	   
       });
</script>
    </body>
</html>
<?php
include "header_foot.php";
?>