<?php
//broiler_farmerdetail_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Farm Report Master";
    include "header_head.php";
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Farm Report Master";
    include "header_head.php";
}


/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); $user_code = $_SESSION['userid'];
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = "";
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
            }
            else{ }
        }
        $col_count = $row2['column_count'];
    }
}

$sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_code_array[$row['farmer_code']] = $row['farm_code']; $farm_desc_array[$row['farmer_code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farmergroup`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $fmr_grp_code[$row['code']] = $row['code']; $fmr_grp_name[$row['code']] = $row['description']; }

$farmer_group = $status = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $farmer_group = $_POST['farmer_group'];
    $status = $_POST['status'];
    if($farmer_group == "all"){ $frmgrp_filter = ""; } else{ $frmgrp_filter = " AND `farmer_group` = '$farmer_group'"; }
    if($status == "all"){ $status_filter = ""; } else{ $status_filter = " AND `active` = '$status'"; }

	$excel_type = $_POST['export'];
     $filename = "Farm Report";
     if ( $farmer_group == "" || $farmer_group == "all") { $export_farmergroup = "All"; }
     else { $export_farmergroup = $fmr_grp_name[$farmer_group]; }
     if( $status == "all" || $status == "") { $export_status = "All"; }
     else if($status == "1") { $export_status = "Active"; }
     else if($status == "0") { $export_status = "In-active"; }
	//$url = "../PHPExcel/Examples/FarmerMasterDetails-Excel.php?farmer_group=".$farmer_group;
}
$tblcol_size = sizeof($act_col_numbs);
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <!-- Datatable CSS 
        <link href='../../col/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>-->

        <!-- jQuery Library -->
        <script src="../../col/jquery-3.5.1.js"></script>
        
        <!-- Datatable JS -->
        <script src="../../col/jquery.dataTables.min.js"></script>
       
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
      
        <?php
            if($excel_type == "print"){
                
                include "headerstyle_wprint.php";         
            }
            else{
                include "headerstyle_woprint.php";     
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" align="center" <?php if($excel_type == "print"){ echo ' id="mine"'; } else{ echo 'width="1300px"'; } ?>>
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $img_logo = $row['imagename']; $cdetails = $row['cdetails']; }
            ?>
            <thead class="thead3" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($img_logo); ?>" height="110px"/></th>
                    <th colspan="<?php echo $tblcol_size - 2; ?>" align="center"><?php echo $cdetails; ?><h5>Farm Report</h5></th>
                </tr>
            <?php if($excel_type == "print"){ } else{ ?></thead>
            <?php if($db == ''){?>
            <form action="broiler_farmerdetail_masterreport.php" method="post">
            <?php } else { ?>
            <form action="broiler_farmerdetail_masterreport.php?db=<?php echo $db; ?>" method="post">
            <?php } ?>
            <form action="broiler_farmerdetail_masterreport.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                        <th colspan="<?php echo $tblcol_size; ?>">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Farmer Group</label>
                                    <select name="farmer_group" id="farmer_group" class="form-control select2">
                                        <option value="all" <?php if($farmer_group == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($fmr_grp_code as $fgcode){ if($fmr_grp_name[$fgcode] != ""){ ?>
                                        <option value="<?php echo $fgcode; ?>" <?php if($farmer_group == $fgcode){ echo "selected"; } ?>><?php echo $fmr_grp_name[$fgcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="all" <?php if($status == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="1" <?php if($status == "1"){ echo "selected"; } ?>>Active</option>
                                        <option value="0" <?php if($status == "0"){ echo "selected"; } ?>>In-active</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('mine', 'Farm Report','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
        </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
            <td colspan="<?php echo $tblcol_size; ?>">
                <div id='control_sh'>
                    <?php
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if($act_col_numbs[$key_id] == "farmer_name" || $nac_col_numbs[$key_id1] == "farmer_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Name</span>'; }
                            else if($act_col_numbs[$key_id] == "mobile_no1" || $nac_col_numbs[$key_id1] == "mobile_no1"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mobile_no1" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mobile-1</span>'; }
                            else if($act_col_numbs[$key_id] == "mobile_no2" || $nac_col_numbs[$key_id1] == "mobile_no2"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mobile_no2" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mobile-2</span>'; }
                            else if($act_col_numbs[$key_id] == "address" || $nac_col_numbs[$key_id1] == "address"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="address" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Address</span>'; }
                            else if($act_col_numbs[$key_id] == "pan_no" || $nac_col_numbs[$key_id1] == "pan_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="pan_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>PAN No</span>'; }
                            else if($act_col_numbs[$key_id] == "aadhar_no" || $nac_col_numbs[$key_id1] == "aadhar_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="aadhar_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Aadhar No</span>'; }
                            else if($act_col_numbs[$key_id] == "national_id" || $nac_col_numbs[$key_id1] == "national_id"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="national_id" onclick="update_masterreport_status(this.id);" '.$checked.'><span>National ID</span>'; }
                            else if($act_col_numbs[$key_id] == "usc" || $nac_col_numbs[$key_id1] == "usc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="usc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>USC</span>'; }
                            else if($act_col_numbs[$key_id] == "service_no" || $nac_col_numbs[$key_id1] == "service_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="service_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Service No</span>'; }
                            else if($act_col_numbs[$key_id] == "farmer_group" || $nac_col_numbs[$key_id1] == "farmer_group"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_group" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Group</span>'; }
                            else if($act_col_numbs[$key_id] == "tcds_per" || $nac_col_numbs[$key_id1] == "tcds_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="tcds_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>TDS %</span>'; }
                            else if($act_col_numbs[$key_id] == "bank_name" || $nac_col_numbs[$key_id1] == "bank_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="bank_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Acc No</span>'; }
                            else if($act_col_numbs[$key_id] == "bank_branch" || $nac_col_numbs[$key_id1] == "bank_branch"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="bank_branch" onclick="update_masterreport_status(this.id);" '.$checked.'><span>IFSC Code</span>'; }
                            else if($act_col_numbs[$key_id] == "bank_ifsc_code" || $nac_col_numbs[$key_id1] == "bank_ifsc_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="bank_ifsc_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Bank Name</span>'; }
                            else if($act_col_numbs[$key_id] == "bank_account_no" || $nac_col_numbs[$key_id1] == "bank_account_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="bank_account_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Branch</span>'; }
                            else if($act_col_numbs[$key_id] == "farm_name" || $nac_col_numbs[$key_id1] == "farm_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Name</span>'; }
                            else if($act_col_numbs[$key_id] == "farm_name" || $nac_col_numbs[$key_id1] == "farm_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Code</span>'; }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <table id="mine" class="tbl" align="center"  style="width:1300px;">
        <thead class="thead1" align="center" style="width:1212px;  display:none; ">
        <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; 
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){  $cdetails = $row['cdetails']; 
            ?>
           
                <tr align="center">
                    <th colspan="<?php echo $tblcol_size; ?>" align="center"><?php echo $cdetails; ?><h5>Farm Report</h5></th>
                </tr>
         <?php } ?>
         <tr>
                       
                       <th  colspan="<?php echo $tblcol_size; ?>">
                                        <div class="m-2 form-group">
                                           <label>Farmer Group: <?php echo $export_farmergroup; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Status: <?php echo $export_status; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label><br/></label>
                   
                                       </div>
                                       
                               </th>
                           
                       </tr>
       

        </thead>
        <thead class="thead3" align="center" style="width:1212px;"><?php } ?>
                <tr align="center">
                <?php
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if($act_col_numbs[$key_id] == "farmer_name"){ echo "<th>Farmer Name</th>"; }
                    else if($act_col_numbs[$key_id] == "mobile_no1"){ echo "<th>Mobile-1</th>"; }
                    else if($act_col_numbs[$key_id] == "mobile_no2"){ echo "<th>Mobile-2</th>"; }
                    else if($act_col_numbs[$key_id] == "address"){ echo "<th>Address</th>"; }
                    else if($act_col_numbs[$key_id] == "pan_no"){ echo "<th>PAN No</th>"; }
                    else if($act_col_numbs[$key_id] == "aadhar_no"){ echo "<th>Aadhar No</th>"; }
                    else if($act_col_numbs[$key_id] == "national_id"){ echo "<th>National ID</th>"; }
                    else if($act_col_numbs[$key_id] == "usc"){ echo "<th>USC</th>"; }
                    else if($act_col_numbs[$key_id] == "service_no"){ echo "<th>Service No</th>"; }
                    else if($act_col_numbs[$key_id] == "farmer_group"){ echo "<th>Farmer Group</th>"; }
                    else if($act_col_numbs[$key_id] == "tcds_per"){ echo "<th>TDS %</th>"; }
                    else if($act_col_numbs[$key_id] == "bank_name"){ echo "<th>Acc No</th>"; }
                    else if($act_col_numbs[$key_id] == "bank_branch"){ echo "<th>IFSC Code</th>"; }
                    else if($act_col_numbs[$key_id] == "bank_ifsc_code"){ echo "<th>Bank Name</th>"; }
                    else if($act_col_numbs[$key_id] == "bank_account_no"){ echo "<th>Branch</th>"; }
                    else if($act_col_numbs[$key_id] == "farm_name"){ echo "<th>Farm Name</th>"; }
                    else if($act_col_numbs[$key_id] == "farm_ccode"){ echo "<th>Farm Code</th>"; }
                }
                ?>
                </tr>
            </thead>
            
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $sql_record = "SELECT * FROM `broiler_farmer` WHERE `dflag` = '0'".$frmgrp_filter."".$status_filter." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    echo "<tr>";
                    for($i = 1;$i <= $col_count;$i++){
                        $key_id = "A:1:".$i;
                        if($act_col_numbs[$key_id] == "farmer_name"){ echo "<td title='Farmer Name'>".$row['name']."</td>"; }
                        else if($act_col_numbs[$key_id] == "mobile_no1"){ echo "<td title='Mobile-1'>".$row['mobile1']."</td>"; }
                        else if($act_col_numbs[$key_id] == "mobile_no2"){ echo "<td title='Mobile-2'>".$row['mobile2']."</td>"; }
                        else if($act_col_numbs[$key_id] == "address"){ echo "<td title='Address' style='white-space: normal;'>".$row['address']."</td>"; }
                        else if($act_col_numbs[$key_id] == "pan_no"){ echo "<td title='PAN No'>".$row['panno']."</td>"; }
                        else if($act_col_numbs[$key_id] == "aadhar_no"){ echo "<td title='Aadhar No'>".$row['aadharno']."</td>"; }
                        else if($act_col_numbs[$key_id] == "national_id"){ echo "<td title='National ID'>".$row['nationalidno']."</td>"; }
                        else if($act_col_numbs[$key_id] == "usc"){ echo "<td title='USC'>".$row['usc']."</td>"; }
                        else if($act_col_numbs[$key_id] == "service_no"){ echo "<td title='Service No'>".$row['serviceno']."</td>"; }
                        else if($act_col_numbs[$key_id] == "farmer_group"){ echo "<td title='Farmer Group'>".$fmr_grp_name[$row['farmer_group']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "tcds_per"){ echo "<td title='TDS %'>".$row['tds_per']."</td>"; }
                        else if($act_col_numbs[$key_id] == "bank_name"){ echo "<td title='Acc No'>".$row['accountno']."</td>"; }
                        else if($act_col_numbs[$key_id] == "bank_branch"){ echo "<td title='IFSC Code'>".$row['ifsc_code']."</td>"; }
                        else if($act_col_numbs[$key_id] == "bank_ifsc_code"){ echo "<td title='Bank Name'>".$row['bank_name']."</td>"; }
                        else if($act_col_numbs[$key_id] == "bank_account_no"){ echo "<td title='Branch'>".$row['branch_code']."</td>"; }
                        else if($act_col_numbs[$key_id] == "farm_name"){ echo "<td title='Farm Name'>".$farm_desc_array[$row['code']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "farm_ccode"){ echo "<td title='Farm Code'>".$farm_code_array[$row['code']]."</td>"; }
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script>
            function update_masterreport_status(a){
                var file_url = '<?php echo $field_href[0]; ?>';
                var user_code = '<?php echo $user_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var item_list = this.responseText;
                        if(item_list == 0){
                            //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
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
            //$('#mine tfoot th').each( function () {
                //var title = $(this).text();
                //$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //} );

            $('#myInput').keyup( function() {
                    table.draw();
                } );
                $('input.column_filter').on( 'keyup click', function () {
                    filterColumn( $(this).parents('tr').attr('data-column') );
                });
            
            });
        </script>
                  <script type="text/javascript">
             function tableToExcel(table, name, filename, chosen){ 
              
              var uri = 'data:application/vnd.ms-excel;base64,'
                , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
          //  return function(table, name, filename, chosen) {
                if (chosen === 'excel') { 
                if (!table.nodeType) table = document.getElementById(table)
                var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                //window.location.href = uri + base64(format(template, ctx))
                var link = document.createElement("a");
                                link.download = filename+".xls";
                                link.href = uri + base64(format(template, ctx));
                                link.click();
                }
            //}
        }
        </script>
       
    </body>
</html>
<?php
include "header_foot.php";
?>