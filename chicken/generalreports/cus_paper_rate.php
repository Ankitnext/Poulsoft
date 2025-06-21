<?php
//chicken_vehiclewise_summary1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "cus_paper_rate.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_vehiclewise_ledger1.php?db=".$db;
}
include "number_format_ind.php";

function decimal_adjustments($a,$b){
    if($a == ""){ $a = 0; }
    $a = round($a,$b);
    $c = explode(".",$a);
    $ed = "";
    $iv = 0;
    if($c[1] == ""){ $iv = 1; }
    else{ $iv = strlen($c[1]); }
    if($iv == 0){ $iv = 1; }
    for($d = $iv;$d < $b;$d++){ if($ed == ""){ $ed = "0"; } else{ $ed .= "0"; } }
    return $a."".$ed;
}
$file_name = "Customer Paper Rate Report";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $loc_access = ""; $adm_aflag = 0;
while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; if((int)$row['supadmin_access'] == 1 || (int)$row['admin_access'] == 1){ $adm_aflag = 1; } }

//Sector Access Filter
if($loc_access == "" || $loc_access == "all"){ $sec_fltr = ""; }
else{
    $loc1 = explode(",",$loc_access); $loc_list = "";
    foreach($loc1 as $loc2){ if($loc_list = ""){ $loc_list = $loc2; } else{ $loc_list = $loc_list."','".$loc2; } }
    $sec_fltr = " AND `code` IN ('$loc_list')";
}
//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }


//print_r($ven_name);
$sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `description` LIKE '%Birds%' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $csup_alist = $carea_alist = array();
while($row = mysqli_fetch_assoc($query)){ $csup_alist[$row['supr_code']] = $row['supr_code']; $carea_alist[$row['area_code']] = $row['area_code']; }

//Area Details
$area_list = implode("','",$carea_alist);
$sql = "SELECT * FROM `main_areas` WHERE `code` IN ('$area_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";$query = mysqli_query($conn,$sql); $area_code = $area_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $area_code[$row['code']] = $row['code']; $area_name[$row['code']] = $row['description']; }

//paper rate

//Supervisor Details
$supv_list = implode("','",$csup_alist);
$sql = "SELECT * FROM `chicken_employee` WHERE `code` IN ('$supv_list') AND `dflag`= '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $csupr_code = $csupr_name = array();
while($row = mysqli_fetch_assoc($query)){ $csupr_code[$row['code']] = $row['code']; $csupr_name[$row['code']] = $row['name']; }


//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

// Logo Flag
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query);
if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `description` LIKE '%Birds%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); //`category` IN ('$cat_codes') AND 
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $shortc[$row['code']] = $row['short_name']; }


$fdate = $date = $tdate = date("Y-m-d"); $supervisor = $itemc = "all"; $fstyles = $fsizes = "default"; $exports = "display"; $area_fltr = "";
if(isset($_POST['submit']) == true){

    $supervisor = $_POST['supervisor'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];
    $itemc = $_POST['itemc'];

    $area_acode = array(); foreach($_POST['areas'] as $t1){ $area_acode[$t1] = $t1; if($t1 == "all" || $t1 == ""){ $area_acode["all"] = "all"; $aa_flag = 1; } }

    if($aa_flag == 0){ $area_list = implode("','",$area_acode); $area_fltr = " AND `area_code` IN ('$area_list')"; }

     //Customer Details
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `supr_code` = '$supervisor'".$area_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
  //  echo $sql;
    $query = mysqli_query($conn,$sql); $cus_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_alist[$row['code']] = $row['code']; $cusname[$row['code']] = $row['name']; }
    $cus_list = implode("','",$cus_alist);
   // echo $cus_list;
}

?>
<html>
	<head>
        <title><?php echo $file_name; ?></title>
        <?php include "header_head2.php"; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <style>
            .main-table { white-space: nowrap; }
            .tbody1{
                color: black;
            }
        </style>
	</head>
	<body>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table <?php if($exports == "print") { echo ' class="main-table"'; } else{ echo ' class="table-sm table-hover main-table2"'; } ?>>
                        <thead class="thead1">
                            <?php
                            if($dlogo_flag > 0) { ?>
                                <tr>
                                    <td rowspan="2" colspan="4"><img src="../<?php echo $logo1; ?>" height="150px"/></td>
                                    
                                    <td colspan="15" align="center">
                                        <h6><?php echo $file_name; ?></h6>
                                    </td>
                                </tr>
                                <tr>
                                    <!-- <td colspan="15" align="center">
                                        <h6>STATEMENT FROM DATE <?php echo date("d.m.Y",strtotime($fdate)); ?> - TO DATE <?php echo date("d.m.Y",strtotime($tdate)); ?></h6>
                                    </td> -->
                                </tr>
                            <?php }
                            else{ 
                            ?>
                            <tr>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                
                                <td colspan="15" align="center">
                                    <h3><?php echo $file_name; ?></h3>
                                </td>
                            </tr>
                            <?php } ?>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                     
                                         <div class="form-group" style="width:190px;">
                                            <label for="supervisor">Item</label>
                                            <select name="itemc" id="itemc" class="form-control select2" style="width:180px;" >
                                                <!-- <option value="all" <?php if($itemc == "all"){ echo "selected"; } ?>>-All-</option> -->
											    <?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($itemc == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                       

                                        <div class="form-group" style="width:190px;">
                                            <label for="supervisor">Supervisor</label>
                                            <select name="supervisor" id="supervisor" class="form-control select2" style="width:180px;" onchange="fetch_careas();">
                                                <option value="all" <?php if($supervisor == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($csupr_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($supervisor == $scode){ echo "selected"; } ?>><?php echo $csupr_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group" style="width:190px;">
                                            <label>Area</label>
                                            <select name="areas" id="areas" class="form-control select2" style="width:180px;" multiple >
                                                <option value="all" <?php foreach($area_acode as $areas){ if($areas == "all"){ echo "selected"; } } ?>>-All-</option>
                                                <?php foreach($area_code as $gcode){ if($area_name[$gcode] != ""){ ?>
                                                <option value="<?php echo $gcode; ?>" <?php foreach($area_acode as $areas){ if($areas == $gcode){ echo "selected"; } } ?>><?php echo $area_name[$gcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>


                                        <?php if((int)$font_fflag == 1){ ?>
                                        <div class="form-group" style="width:190px;">
                                            <label for="fstyles">Font-Family</label>
                                            <select name="fstyles" id="fstyles" class="form-control select2" style="width:180px;">
                                                <option value="default" <?php if($fstyles == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_id as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fstyles == $scode){ echo "selected"; } ?>><?php echo $font_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:70px;">
                                            <label for="fsizes">Font-Size</label>
                                            <select name="fsizes" id="fsizes" class="form-control select2" style="width:60px;">
                                                <option value="default" <?php if($fsizes == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_sizes as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fsizes == $scode){ echo "selected"; } ?>><?php echo $font_sizes[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group" style="width:150px;">
                                            <label>Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                        </div>
                                    </div>
								</td>
							</tr>
						</thead>
                    <?php if($exports == "display" || $exports == "exportpdf"){ ?>
                    </table>
                    <table class="main-table table-sm table-hover" id="main_table">
                    <?php } ?>
						<?php
                        }
                        if(isset($_POST['submit']) == true){
                            $html = '';
                            
                            $html .= '<tr class="thead2">';
                            $html .= '<th style="text-align:center;">S.No.</th>';
                            $html .= '<th style="text-align:center;">Customer Name</th>';
                            foreach($item_code as $bic){
                                 $html .= '<th style="text-align:center;">'.$shortc[$bic].'</th>';
                            }

                            $html .= '</tr>';
                              

                                $data = $type = array();
                                $sql = "SELECT * FROM `customer_price` WHERE `ccode` IN ('$cus_list') AND `itemcode` = '$itemc' AND `active` = '1' AND `dflag` = '0' AND `date` = ( SELECT MAX(date) FROM `customer_price` WHERE `ccode` IN ('$cus_list') AND `itemcode` = '$itemc' AND `active` = '1' AND `dflag` = '0' );";
                               // echo $sql;
                                $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){
                                    $key = $row['itemcode'];
                                    $cusprate[$key] = $row['value'];
                                    $cuspt[$key] = $row['price_type'];
                                    $customerc[$row['ccode']] = $key;

                                    $data[$row['ccode']][$key] = $row['value'];
                                    $type[$row['ccode']][$key] = $row['price_type'];
                                    
                                }
                                // print_r($data);
                                // print_r($type);
                                 $c = 1;
                                foreach($cus_alist as $cust){
                                   
                                     $item = array();
                                     $ivalue = $data[$cust];
                                    //  print_r($ivalue);
                                     $html .= '<tr>';
                                    $html .= '<td style="text-align:center">'.$c.'</td>';
                                    $html .= '<td style="text-align:left;">'.$cusname[$cust].'</td>';
                                     foreach($item_code as $icode){
                                          $price = $data[$cust][$icode];
                                          $ptype =  $type[$cust][$icode];
                                          if($price == ""){
                                             $p = "0";
                                          }else{
                                             if($ptype == "A"){
                                            $p = $price;
                                          }else{
                                            $p = "-".$price;
                                          }
                                          }
                                         
                                          $item[$icode] = $price ;
                                           $html .= '<td style="text-align:right;">'.$p.'</td>';
                                     }

                                  
                                    $html .= '</tr>';
                                  $c++;
                                }
                              


                             echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>

            function fetch_careas(){
                var supervisors = document.getElementById("supervisor").value;
                removeAllOptions(document.getElementById("areas"));
                //if(supervisors == "" || supervisors == "select"){ } else{}
                var fetch_areas = new XMLHttpRequest();
                var method = "GET";
                var url = "chicken_fetch_customer_areas.php?supervisors="+supervisors+"&type=from_emp";
                //window.open(url);
                var asynchronous = true;
                fetch_areas.open(method, url, asynchronous);
                fetch_areas.send();
                fetch_areas.onreadystatechange = function(){
                    if (this.readyState == 4 && this.status == 200) {
                        var area_list = this.responseText;
                        $('#areas').append(area_list);
                    }
                }
            }
             
            function checkval() {
                var users = document.getElementById("users").value;
                var sectors = document.getElementById("sectors").value;
                var l = true;
                if(users == "select"){
                    alert("Kindly select User");
                    l = false;
                }
                else if(sectors == "select"){
                    alert("Kindly select Shop/Outlet");
                    l = false;
                }
                
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script src="searchbox.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();
                }
                else{ }
            }
            function cdate_format1() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split(".");
                    cell.textContent = adate[2]+"-"+adate[1]+"-"+adate[0];
                });
            }
            function cdate_format2() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split("-");
                    cell.textContent = adate[2]+"."+adate[1]+"."+adate[0];
                });
            }

            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
