<?php
    //CustomerLedgerWithTdsPDF5.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
        
    $db = $_SESSION['db'] = $_GET['db'];
    if($db == ''){
        include "../config.php";
        //include "header_head.php";
        include "number_format_ind.php";
        $form_reload_page = "../printformatlibrary/Examples/cus_sendcusledgerreport5.php";
    }
    else{
		$_SESSION['dbase'] = $db;
        include "APIconfig.php";
        include "number_format_ind.php";
        //include "header_head.php";

        $users_code = $_GET['emp_code'];
        $client = $_GET['client'];
        $form_reload_page = "../printformatlibrary/Examples/cus_sendcusledgerreport5.php?db=".$db."&client=".$client."&emp_code=".$users_code;
    }

    $fdate = $tdate = date("Y-m-d");
    $sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn, $sql); $grp_code = $grp_name = array();
    while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

	// Logo Flag
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }
  
    $sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'PDF' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
    while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }

    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Receipt Report' OR `type` = 'All' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
    while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; }

?>
<html>
	<head>
        <?php include "header_head2.php"; ?>
		<link rel="stylesheet" type="text/css" href="reportstyle.css">
		<link rel="stylesheet" type="text/css" href="loading_screen.css">
		<style>
			.thead2 th {
				top: 0;
				position: sticky;
				background-color: #98fb98;
			}

			.thead2,
			.tbody1 {
				padding: 1px;
				font-size: 12px;
			}

			.formcontrol {
				height: 23px;
				border: 0.1vh solid gray;
			}

			.formcontrol:focus {
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}

			.tbody1 td {
				padding-right: 5px;
				text-align: right;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<table align="center">
			<tr>
				<?php
				if($dlogo_flag > 0) { ?>
					<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
				<?php }
				else{ 
					?>
				<td><img src="<?php echo "../".$logopath; ?>" height="100px"/></td>
				<td><?php echo $cdetails; }?></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<h3>Customer Ledger Send WhatsApp</h3>
				</td>
			</tr>
		</table>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table class="main-table table-sm table-hover" id="main_table">
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fromdate">From Date</label>
                                            <input type="text" name="fromdate" id="fromdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="todate">To Date</label>
                                            <input type="text" name="todate" id="todate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:290px;">
                                            <label for="groups">Group</label>
                                            <select name="groups" id="groups" class="form-control select2" style="width:280px;" onchange="fetch_customer_details()">
                                                <option value="all">All</option>
											    <?php foreach($grp_code as $gcode){ ?><option value="<?php echo $gcode; ?>"><?php echo $grp_name[$gcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="today_sale"><br/>
                                            <input type="checkbox" name="today_sale" id="today_sale" class="form-control1" onclick="update_status();" />&nbsp;Today Sale</label>
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <br/><button type="button" class="btn btn-warning btn-sm" name="fetch_details" id="fetch_details" onclick="fetch_customer_details();">Fetch Details</button>
                                        </div>
                                        <div class="form-group" style="width:60px;visibility:hidden;">
                                            <label for="submit_status">Status</label>
                                            <input type="text" name="submit_status" id="submit_status" class="form-control" value="0" style="padding:0;padding-left:2px;width:50px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;visibility:hidden;">
                                            <label for="Incr">IN</label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="padding:0;padding-left:2px;width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;visibility:hidden;">
                                            <label for="ebtncount">EB</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;padding-left:2px;width:20px;" readonly />
                                        </div>
                                    </div>
								</td>
							</tr>
						</thead>
						<thead class="thead2">
							<tr>
								<th>Sl.No.</th>
								<th>Selection</th>
								<th>Customer Name</th>
								<th>Customer Mobile</th>
							</tr>
						</thead>
						<tbody class="tbody1" id="tbody1">
							
						</tbody>
					</table>
				</form>
			</div>
			<div class="ring"><?php echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div>
		</section>
        <script>
			function checkedall() {
				var a = document.getElementById("cus_select").value;
				if(!a.match("select")){
					var cdlt = a.split("-");
					var sval = cdlt[0];
					var eval = cdlt[1];
					var b = document.querySelectorAll('input[type=checkbox]');
					for(var c = 1; c <= b.length; c++){
						if(c >= sval && c <= eval){
							b[c].checked = true;
						}
                        else{
							b[c].checked = false;
						}
					}
				}
                else{
					var b = document.querySelectorAll('input[type=checkbox]');
					for(var c = 1; c <= b.length; c++){
						b[c].checked = false;
					}
				}
			}
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("sendsms").style.visibility = "hidden";
                var l = true;
                var groups = document.getElementById('groups').value;
                var checkboxes = document.querySelectorAll('input[name="c_det[]"]');
                var chk_count = 0;
                for(var i = 0; i < checkboxes.length; i++){ if(checkboxes[i].checked == true){ chk_count++; } }
                if(parseInt(chk_count) == 0){
                    alert("select atleast one Checkbox to proceed");
                    l = false;
                }
                else if(groups == ""){
                    alert("Please select Group");
                    document.getElementById('groups').focus();
                    l = false;
                }
                else{ }

                if(l == true){
					document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					document.getElementById("disp_val").innerHTML = '<?php echo $loading_stitle; ?>';
                    return true;
                }
                else{
					document.getElementsByClassName("ring")[0].style.display = "none";
					document.getElementsByClassName("ring_status")[0].style.display = "none";
                    document.getElementById("sendsms").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
			function fetch_customer_details(){
                var tsale_flag = 0;
				var fdate = document.getElementById('fromdate').value;
				var tdate = document.getElementById('todate').value;
				var groups = document.getElementById('groups').value;
				var ts_id = document.getElementById('today_sale');
                document.getElementById("tbody1").innerHTML = "";
				if(ts_id.checked == true){ tsale_flag = 1; }
                var inv_items = new XMLHttpRequest();
				var method = "GET";
				var url = "chicken_fetch_wapp_customer_details.php?fdate="+fdate+"&tdate="+tdate+"&groups="+groups+"&tsale_flag="+tsale_flag;
                //window.open(url);
				var asynchronous = true;
				inv_items.open(method, url, asynchronous);
				inv_items.send();
				inv_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var cus_dt1 = this.responseText;
                        var cus_dt2 = cus_dt1.split("[@$%&]");
                        if(parseInt(cus_dt2[1]) > 0){
                            document.getElementById("tbody1").innerHTML = cus_dt2[0];
                            document.getElementById("incr").value = cus_dt2[1];
                            document.getElementById('submit_status').value = 1;
                            $('.select').select2();
                        }
                        else{
                            alert("Customer Details not found. Please check and try again.");
                        }
                    }
                }
			}
            function update_status(){
                document.getElementById('submit_status').value = 0;
                fetch_customer_details();
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#sendsms').click(); }); } } else{ } });
            
        </script>
        <script src="../loading_page_out.js"></script>
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
		<?php if($exports == "displaypage" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
