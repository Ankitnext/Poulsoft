<?php
//broiler_tripsheet.php
$requested_data = json_decode(file_get_contents('php://input'),true);

session_start();
    
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){

    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Doctor Visit Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}else{

    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Doctor Visit Report";
    include "header_head.php";
    $user_code = $_GET['userid'];
}




$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1'  ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code'];
    $farm_ccode[$row['code']] = $row['farm_code'];
    $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code'];
    $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code'];
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }

$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; }

$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

//$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
//while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }
$fdate = $tdate = date("Y-m-d"); $supervisors = "all"; $excel_type = "display"; //$branches = $lines = 
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    //$branches = $_POST['branches'];
    $farm = $_POST['farm'];
    $supervisors = $_POST['supervisors'];

    if($farm != 'all'){
        $cod = " AND farm = '$farm'";
    }else{
        $cod = "";
    }

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/SalesReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&supervisors=".$supervisors."&branches=".$branches."&lines=".$lines;
}
else{
    $url = "";
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
        .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
 
			}
         
        </style>
       
        <?php
          if($excel_type == "print"){
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center"><?php echo $row['cdetails']; ?><h5>Doctor Visit Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_doctor_visit.php" method="post">
                  <?php } else { ?>
                <form action="broiler_doctor_visit.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="12">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farm" id="farm" class="form-control select2">
                                        <option value="all" <?php if($farm == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $farms){ if($farm_code[$farms] != ""){ ?>
                                        <option value="<?php echo $farms; ?>" <?php if($farm == $farms){ echo "selected"; } ?>><?php echo $farm_name[$farms]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                               
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <thead class="thead3" align="center">
                <tr align="center">
                <th id="order_num">S.No</th>
                <th id="order_date">Date</th>
                    <th id='order'>Farm</th>
                    <th id='order_date'>Placement Date</th>
                    <th id='order_num'>Age</th>
                    <th id='order_num'>Bl.Birds</th>
                    <th id='order'>Farm Condition</th>
                    <th id='order'>Birds Condition</th>
                    <th id='order'>Running Medication</th>
                    <th id='order'>Suggestions</th>
                    <th id='order'>Images</th>
                    <th id='order'>Remarks</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1" id="tbody1">
                <?php
                $age = array();
                $sql_record = "SELECT * FROM `broiler_doctorvisit` WHERE `date` >= '$fdate' AND `date` <= '$tdate' and dflag = 0 $cod ORDER BY `date`,`trnum`,`id` ASC";
                $client = $_SESSION['client'];
                $query = mysqli_query($conn,$sql_record); $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $mort_image = $row['images'];

                    if(!empty($mort_image)){

                        $mort_img_list = "";
                        $mort_img_arr = explode(",",$mort_image);
                        $mia_size = sizeof($mort_img_arr);
                        foreach($mort_img_arr as $mia){
                            if($mort_img_list == ""){
                                //$mort_img_list = "window.open('../AndroidApp_API/clientimages/".$client."/doctorimages/".$mia."');";
                                if( $row['addedtime'] < "2024-04-22 07:47:54" ){
                                    $mort_img_list = "window.open('../AndroidApp_API/clientimages/".$client."/doctorimages/".$mia."');";
                                    
                                }else{
                                    $mort_img_list = "window.open('..".$mia."');";
                                }
                            }
                            else{
                                //$mort_img_list = $mort_img_list."window.open('../AndroidApp_API/clientimages/".$client."/doctorimages/".$mia."');";
                                if( $row['addedtime'] < "2024-04-22 07:47:54" ){
                                    $mort_img_list = $mort_img_list."window.open('../AndroidApp_API/clientimages/".$client."/doctorimages/".$mia."');";
                                    
                                }else{
                                    $mort_img_list = $mort_img_list."window.open('..".$mia."');";
                                }
                            }
                            
                        }

                       // $display_mortimage = "../AndroidApp_API/clientimages/".$client."/doctorimages/".$mort_image;
                       if( $row['addedtime'] < "2024-04-22 07:47:54" ){
                            $display_mortimage = "../AndroidApp_API/clientimages/".$client."/doctorimages/".$mort_image;
                            
                        }else{
                            $display_mortimage = ".." . $mort_image;
                        }
                    }
                    else{
                        $display_mortimage = "";
                        $mort_img_list = "";
                    }
                    $trip_key = date("d.m.Y",strtotime($row['date']))."@".$row['trnum'];
                    $fi_code[$trip_key] = $trip_key;
                    $date[$trip_key] = $row['date'];
                    $farm_code[$trip_key] = $row['farm_code'];
                    $trnum[$trip_key] = $row['trnum'];
                    $farm_condition[$trip_key] = $row['farm_condition'];
                    $birds_condition[$trip_key] = $row['birds_condition'];
                    $running_mediction[$trip_key] = $row['running_mediction'];
                    $suggestion[$trip_key] = $row['suggestion'];
                    $remarks[$trip_key] = $row['remarks'];
                    $images[$trip_key] = $mort_img_list;
                    $placement[$trip_key] = date("d.m.Y",strtotime($row['placed_date']));
                    $age[$trip_key] = $row['age'];
                    $bl_birds[$trip_key] = $row['bl_birds'];
                 
                }
                
                
                foreach($fi_code as $po){ $slno++; $i++;
                          
                   

                        ?>
                        <tr>
                            <td style="text-align:left;"><?php echo $slno; ?></td>
                            <td style="text-align:left;"><?php $t1 = array(); $t1 = explode("@",$po); echo $t1[0]; ?></td>
                            <td style="text-align:left;"><?php echo $farm_name[$farm_code[$po]] ; ?></td>
                            <td style="text-align:left;"><?php echo $placement[$po]; ?></td>
                            <td style="text-align:left;"><?php echo $age[$po]; ?></td>
                            <td style="text-align:left;"><?php echo $bl_birds[$po]; ?></td>
                            <td style="text-align:left;"><?php echo $farm_condition[$po]; ?></td>
                            <td style="text-align:left;"><?php echo $birds_condition[$po]; ?></td>
                            <td style="text-align:left;"><?php echo $running_mediction[$po]; ?></td>
                            <td style="text-align:left;"><?php echo $suggestion[$po]; ?></td>
                            <?php
                            if(!empty($images[$po])){
                            ?>
                            <td style="text-align:right;" title="Doctor Image"><a href="javascript:void(0)" onclick="<?php echo $images[$po]; ?>" title="<?php echo $images[$po]; ?>"><?php echo "mortImage-". $i; ?></a></td>
                            <?php
                            }
                            else{
                            ?>
                            <td title="Doctor Image"></td>
                            <?php
                            }
                            ?>
                            <td style="text-align:left;"><?php echo $remarks[$po]; ?></td>
                            
                        </tr>
                    <?php
                      
                    }
                    
                    
                
            ?>
            </tbody>
           
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
            function fetch_row_height(){
                var table_elements = document.querySelector("table>tbody");
                var i; var max_height = 0;
                for(i = 1; i <= table_elements.rows.length; i++){
                    var row_selector = "table>tbody>tr:nth-child(" + [i] + ")";
                    var table_row = document.querySelector(row_selector);
                    var vertical_spacing = window.getComputedStyle(table_row).getPropertyValue("-webkit-border-vertical-spacing");
                    var margin_top = window.getComputedStyle(table_row).getPropertyValue("margin-top");
                    var margin_bottom = window.getComputedStyle(table_row).getPropertyValue("margin-bottom");
                    var row_height= parseInt(vertical_spacing, 10)+parseInt(margin_top, 10)+parseInt(margin_bottom, 10)+table_row.offsetHeight;
                    if(max_height <= row_height){
                        max_height = row_height;
                    }
                }
                //alert("The height is: "+max_height+"px");
                document.getElementById("thead2_empty_row").style.height = max_height+"px";
            }
            fetch_row_height();
        </script>
        <script>
            function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
            function table_sort3() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
                
            }
            function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>