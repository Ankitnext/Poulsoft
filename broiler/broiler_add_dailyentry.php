<?php
//broiler_add_dailyentry.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['dailyentry'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
    else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; }
    else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
    else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
    else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }


    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND  `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $icat_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($icat_code == ""){ $icat_code = $row['code']; } else{ $icat_code = $icat_code."','".$row['code']; } }

        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_code') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
				
        $sql = "SELECT * FROM `item_details` WHERE `description` LIKE 'Broiler Chicks' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_name = $row['description']; }
		$farms = array();
		$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' AND `gc_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code']; }
        $farm_list = implode("','", $farms);
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; $farm_supervisor[$row['code']] = $row['supervisor_code']; }
        
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

		$sql = "SELECT * FROM `feed_bagcapacity` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bag_flag = mysqli_num_rows($query);
		$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Daily Entry' AND `field_function` LIKE 'Bags' AND `flag` = 1"; $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

		$sql = "SELECT * FROM `extra_access`"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
            if($row['field_name'] == "Day Entry" && $row['field_function'] == "2nd feed entry"){ $two_feed_flag = $row['flag']; }
            if($row['field_name'] == "Day Record" && $row['field_function'] == "Stock Check"){ $stockcheck_flag = $row['flag']; }
            if($row['field_name'] == "Day Record" && $row['field_function'] == "Auto Avg Price"){ $autoavgprice_flag = $row['flag']; }
        }
        if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }
        if($autoavgprice_flag == "" || $autoavgprice_flag == 0){ $autoavgprice_flag = 0; }
        $ip_addr = $_SERVER['REMOTE_ADDR'];
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Day Record</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_dailyentry.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group" style="width:170px;">
                                            <label>Supervisor<b style="color:red;">&nbsp;*</b></label>
							                <select name="supervisor_code" id="supervisor_code" class="form-control select2" style="width:160px;" onchange="fetch_farm_details()">
                                                <option value="select">select</option>
                                                <?php foreach($emp_code as $driver_code){ ?><option value="<?php echo $driver_code; ?>"><?php echo $emp_name[$driver_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="width:170px;">
                                            <label>Farm<b style="color:red;">&nbsp;*</b></label>
							                <select name="farm_code[]" id="farm_code[0]" class="form-control select2" style="width:160px;" onchange="fetch_farm_batch(this.id);">
                                                <option value="select">select</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Batch</label>
							                <input type="text" name="batch_code[]" id="batch_code[0]" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Age</label>
							                <input type="text" name="brood_age[]" id="brood_age[0]" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
							                <input type="text" name="date[]" id="date[0]" class="form-control" style="width:100px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Mortality</label>
							                <input type="text" name="mortality[]" id="mortality[0]" class="form-control" style="width:80px;" onkeyup="validatenum(this.id)" />
                                        </div>
                                        <div class="form-group">
                                            <label>Culls</label>
							                <input type="text" name="culls[]" id="culls[0]" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" />
                                        </div>
                                        <div class="form-group" style="width:120px;">
                                            <label>Feed 1</label>
							                <select name="item_code1[]" id="item_code1[0]" class="form-control select2" style="width:110px;" onchange="fetch_item_stock1(this.id);">
                                                <option value="select">select</option>
                                                
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock-1</label>
							                <input type="text" name="available_stock_1[]" id="available_stock_1[0]" class="form-control" style="width:60px;" readonly />
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>-</label>
							                <input type="text" name="available_price_1[]" id="available_price_1[0]" class="form-control" style="width:5px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label><?php if($bag_flag > 0 && $bag_access_flag > 0){ echo "Bags"; } else{ echo "Kgs"; } ?></label>
							                <input type="text" name="kgs1[]" id="kgs1[0]" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                        </div>
                                        <?php if($two_feed_flag == 1){ ?>
                                        <div class="form-group" style="width:120px;">
                                            <label>Feed 2</label>
							                <select name="item_code2[]" id="item_code2[0]" class="form-control select2" style="width:110px;" onchange="fetch_item_stock2(this.id);">
                                                <option value="select">select</option>
                                                
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock-2</label>
							                <input type="text" name="available_stock_2[]" id="available_stock_2[0]" class="form-control" style="width:60px;" readonly />
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>-</label>
							                <input type="text" name="available_price_2[]" id="available_price_2[0]" class="form-control" style="width:5px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label><?php if($bag_flag > 0 && $bag_access_flag > 0){ echo "Bags"; } else{ echo "Kgs"; } ?></label>
							                <input type="text" name="kgs2[]" id="kgs2[0]" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                        </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <label>Avg. Wt.</label>
							                <input type="text" name="avg_wt[]" id="avg_wt[0]" class="form-control" style="width:60px;" onkeyup="validatenum2(this.id)" onchange="validateamount2(this.id)" />
                                        </div>
                                        <div class="form-group">
                                            <label>Remarks</label>
							                <textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:120px;height:25px;"></textarea>
                                        </div>
                                        <div class="form-group" id="action[0]" style="padding-top: 12px;"><br/>
                                            <a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <div class="p-0 col-md-12" id="row_body">

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_dailyentry.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var sup_code = farm_code = batch_code = item_code1 = item_code2 = ""; var c = as_qty1 = kgs1 = as_qty2 = kgs2 = 0; var l = true;
                var a = document.getElementById("incr").value;
                sup_code = document.getElementById("supervisor_code").value;
                var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                
                if(sup_code.match("select")){
                    alert("Please select Supervisor Name");
                    document.getElementById("supervisor_code").focus();
                    l = false;
                }
                else{
                    for(var b = 0;b <= a;b++){
                        c = b + 1;
                        farm_code = document.getElementById("farm_code["+b+"]").value;
                        batch_code = document.getElementById("batch_code["+b+"]").value;
                        as_qty1 = document.getElementById("available_stock_1["+b+"]").value;
                        kgs1 = document.getElementById("kgs1["+b+"]").value;
                        item_code1 = document.getElementById("item_code1["+b+"]").value;
                        as_qty2 = document.getElementById("available_stock_2["+b+"]").value;
                        kgs2 = document.getElementById("kgs2["+b+"]").value;
                        item_code2 = document.getElementById("item_code2["+b+"]").value;
                        if(l == true){
                            if(farm_code.match("select")){
                                alert("Kindly select Farm");
                                document.getElementById("farm_code["+b+"]").focus();
                                l = false;
                            }
                            else if(batch_code.length == 0 || batch_code == 0 || batch_code == "" || batch_code == "0.00" || batch_code == "0" || batch_code == 0.00){
                                alert("Kindly select Appropriate Farm to fet Batch Details");
                                document.getElementById("batch_code["+b+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(kgs1) > 0 && item_code1 == "select"){
                                alert("Kindly select Appropriate Item 1 in row: "+c);
                                document.getElementById("item_code1["+b+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(kgs2) > 0 && item_code2 == "select"){
                                alert("Kindly select Appropriate Item 2 in row: "+c);
                                document.getElementById("item_code2["+b+"]").focus();
                                l = false;
                            }
                            else if(stockcheck_flag == 1){
                                if(parseFloat(kgs1) > parseFloat(as_qty1)){
                                    alert("Stock not available in row: "+c);
                                    document.getElementById("kgs1["+b+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(kgs2) > parseFloat(as_qty2)){
                                    alert("Stock not available in row: "+c);
                                    document.getElementById("kgs2["+b+"]").focus();
                                    l = false;
                                }
                                else{ }
                            }
                            else{ }
                        }
                    }
                }
                
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";

                //var ip_addr = '<?php //echo $ip_addr; ?>';
                //if(ip_addr == "49.205.130.10"){
                    var fields = document.getElementById("farm_code["+d+"]");
                    var fcode = fields.value;
                    var fname = fields.options[fields.selectedIndex].text;
                    removeAllOptions(document.getElementById("farm_code["+d+"]"));

                    myselect = document.getElementById("farm_code["+d+"]");
                    theOption1=document.createElement("OPTION");
                    theText1=document.createTextNode(fname);
                    theOption1.value = fcode;
                    theOption1.setAttribute('selected', true);
                    theOption1.appendChild(theText1);
                    myselect.appendChild(theOption1);
                //}
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<div class="row" id="row_no['+d+']">';
                html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">Farm<b style="color:red;">&nbsp;*</b></label><select name="farm_code[]" id="farm_code['+d+']" class="form-control select2" style="width:160px;" onchange="fetch_farm_batch(this.id);"><option value="select">select</option></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Batch</label><input type="text" name="batch_code[]" id="batch_code['+d+']" class="form-control" style="width:120px;" readonly /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Age</label><input type="text" name="brood_age[]" id="brood_age['+d+']" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" readonly /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Date</label><input type="text" name="date[]" id="date['+d+']" class="form-control" style="width:100px;" readonly /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Mortality</label><input type="text" name="mortality[]" id="mortality['+d+']" class="form-control" style="width:80px;" onkeyup="validatenum(this.id)" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Culls</label><input type="text" name="culls[]" id="culls['+d+']" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" /></div>';
                html += '<div class="form-group" style="width:120px;"><label class="labelrow" style="display:none;">Feed 1</label><select name="item_code1[]" id="item_code1['+d+']" class="form-control select2" style="width:110px;" onchange="fetch_item_stock1(this.id);"><option value="select">select</option></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Stock-1</label><input type="text" name="available_stock_1[]" id="available_stock_1['+d+']" class="form-control" style="width:60px;" readonly /></div>';
                html += '<div class="form-group" style="visibility:hidden;"><label class="labelrow" style="display:none;">-</label><input type="text" name="available_price_1[]" id="available_price_1['+d+']" class="form-control" style="width:5px;" readonly /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Kgs</label><input type="text" name="kgs1[]" id="kgs1['+d+']" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" /></div>';
                html += '<?php if($two_feed_flag == 1){ ?>';
                html += '<div class="form-group" style="width:120px;"><label class="labelrow" style="display:none;">Feed 2</label><select name="item_code2[]" id="item_code2['+d+']" class="form-control select2" style="width:110px;" onchange="fetch_item_stock2(this.id);"><option value="select">select</option></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Stock-2</label><input type="text" name="available_stock_2[]" id="available_stock_2['+d+']" class="form-control" style="width:60px;" readonly /></div>';
                html += '<div class="form-group" style="visibility:hidden;"><label class="labelrow" style="display:none;">Stock-2</label><input type="text" name="available_price_2[]" id="available_price_2['+d+']" class="form-control" style="width:5px;" readonly /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Kgs</label><input type="text" name="kgs2[]" id="kgs2['+d+']" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" /></div>';
                html += '<?php } ?>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Avg. Wt.</label><input type="text" name="avg_wt[]" id="avg_wt['+d+']" class="form-control" style="width:60px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Remarks</label><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:120px;height:25px;"></textarea></div>';
                html += '<div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '</div>';
                html += '<hr class="labelrow" style="display:none;" />';
                $('#row_body').append(html); $('.select2').select2(); fetch_farm_details();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function fetch_farm_details(){
                var sup_code = document.getElementById("supervisor_code").value;
                var d = document.getElementById("incr").value;
                removeAllOptions(document.getElementById("farm_code["+d+"]"));
                myselect = document.getElementById("farm_code["+d+"]"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                <?php
                    foreach($farm_code as $fcode){
                        $fscode = $farm_supervisor[$fcode];
                        echo "if(sup_code == '$fscode'){";
                ?>
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $farm_name[$fcode]; ?>"); theOption1.value = "<?php echo $fcode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                <?php
                        echo "}";
                    }
                ?>

            }
            function fetch_farm_batch(a){
                var incr = document.getElementById("incr").value;
                var b = a.split("["); var c = b[1].split("]"); var d = c[0]; var count = 0; var fcode = "";
                var farm_code = document.getElementById(a).value;
                for(var incrs = 0;incrs < incr;incrs++){
                    fcode = document.getElementById("farm_code["+incrs+"]").value;
                    if(fcode == farm_code){
                        count++;
                    }
                }
                if(!farm_code.match("select")){
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_batchdetails.php?fcode="+farm_code+"&count="+count+"&type=dayrecord";
                    //window.open(url);
					var asynchronous = true;
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var bbal = this.responseText;
							if(bbal == "1" || bbal == 1) {
								alert("Upto date Daily entries are processed");
							}
							else if(bbal == "") {
								alert("Details not found check and try again");
							}
							else {
								var batch_details = bbal.split("@");
                                if(batch_details[1] == "" || batch_details[2] == ""){
                                    alert("Chick purchase / Transfer Details are not available for this Batch \n Kindly check and try again");
                                    document.getElementById("batch_code["+d+"]").value = "";
                                    document.getElementById("brood_age["+d+"]").value = "";
                                    document.getElementById("date["+d+"]").value = "";
                                }
                                else{
                                    document.getElementById("batch_code["+d+"]").value = batch_details[0];
                                    document.getElementById("brood_age["+d+"]").value = batch_details[1];
                                    document.getElementById("date["+d+"]").value = batch_details[2];
                                    fetch_available_item_list("farm_code["+d+"]");
                                }
							}
						}
					}
				}
            }
            function fetch_available_item_list(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var batch_code = document.getElementById("batch_code["+d+"]").value;
                var date = document.getElementById("date["+d+"]").value;
                var farm_code = document.getElementById(a).value;
                if(!farm_code.match("select") && batch_code != "" && date != ""){
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_available_items_stock.php?fcode="+farm_code+"&batch_code="+batch_code+"&date="+date+"&row_no="+d;
                    //window.open(url);
					var asynchronous = true;
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var bbal = this.responseText;
							if(bbal == "" || bbal == "@") {
								alert("Details not found check and try again");
							}
							else {
								var id1 = bbal.split("@"); var rowno = id1[1];
                                removeAllOptions(document.getElementById("item_code1["+rowno+"]"));
                                removeAllOptions(document.getElementById("item_code2["+rowno+"]"));

                                myselect1 = document.getElementById("item_code1["+rowno+"]");
                                theOption1=document.createElement("OPTION");
                                theText1=document.createTextNode("select");
                                theOption1.value = "select";
                                theOption1.appendChild(theText1);
                                myselect1.appendChild(theOption1);

                                myselect2 = document.getElementById("item_code2["+rowno+"]");
                                theOption2=document.createElement("OPTION");
                                theText2=document.createTextNode("select");
                                theOption2.value = "select";
                                theOption2.appendChild(theText2);
                                myselect2.appendChild(theOption2);

                                var id2 = id1[0].split("&");
                                var id3 = new Array();
                                for(var i = 0;i <= id2.length;i++){
                                    if(id2[i] != ""){
                                        id3 = id2[i].split(":");

                                        theOption1=document.createElement("OPTION");
                                        theText1=document.createTextNode(id3[1]);
                                        theOption1.value = id3[0];
                                        theOption1.appendChild(theText1);
                                        myselect1.appendChild(theOption1);

                                        theOption2=document.createElement("OPTION");
                                        theText2=document.createTextNode(id3[1]);
                                        theOption2.value = id3[0];
                                        theOption2.appendChild(theText2);
                                        myselect2.appendChild(theOption2);
                                    }
                                }
							}
						}
					}
				}
            }
            function fetch_item_stock1(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var farm_code = document.getElementById("farm_code["+d+"]").value;
                var date = document.getElementById("date["+d+"]").value;
                var item_code = document.getElementById(a).value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+farm_code+"&item_code="+item_code+"&date="+date+"&row_count="+d+"&etype=dentry";
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        if(item_price.length > 0){
                            var item_details = item_price.split("@");

                            var incr = document.getElementById("incr").value;
                            var fcode = icode1 = icode2 = ""; var uqty1 = uqty2 = tqty = 0;
                            for(var i = 0;i <= incr;i++){
                                fcode = document.getElementById("farm_code["+i+"]").value;
                                icode1 = document.getElementById("item_code1["+i+"]").value;
                                uqty1 = document.getElementById("kgs1["+i+"]").value;                if(uqty1 == "" || uqty1 == 0){ uqty1 = 0; }
                                if(fcode == farm_code && icode1 == item_code){
                                    tqty = parseFloat(tqty) + parseFloat(uqty1);
                                }
                                
                                icode2 = document.getElementById("item_code2["+i+"]").value;
                                uqty2 = document.getElementById("kgs2["+i+"]").value;                if(uqty2 == "" || uqty2 == 0){ uqty2 = 0; }
                                if(fcode == farm_code && icode2 == item_code){
                                    tqty = parseFloat(tqty) + parseFloat(uqty2);
                                }
                            }
                            var stk_qty = parseFloat(item_details[0]) - parseFloat(tqty);
                            var stk_prc = parseFloat(item_details[1]);
                            document.getElementById("available_stock_1["+item_details[3]+"]").value = parseFloat(stk_qty).toFixed(2);
                            document.getElementById("available_price_1["+item_details[3]+"]").value = parseFloat(stk_prc).toFixed(5);
                        }
                        else{
                            alert("Item Stock not available, Kindly check before saving ...!");
                            document.getElementById("available_stock_1["+d+"]").value = 0;
                            document.getElementById("available_price_1["+d+"]").value = 0;
                        }
                    }
                }
            }
            function fetch_item_stock2(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var farm_code = document.getElementById("farm_code["+d+"]").value;
                var date = document.getElementById("date["+d+"]").value;
                var item_code = document.getElementById(a).value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+farm_code+"&item_code="+item_code+"&date="+date+"&row_count="+d+"&etype=dentry";
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        if(item_price.length > 0){
                            var item_details = item_price.split("@");
                            
                            var incr = document.getElementById("incr").value;
                            var fcode = icode1 = icode2 = ""; var uqty1 = uqty2 = tqty = 0;
                            for(var i = 0;i <= incr;i++){
                                fcode = document.getElementById("farm_code["+i+"]").value;
                                icode1 = document.getElementById("item_code1["+i+"]").value;
                                uqty1 = document.getElementById("kgs1["+i+"]").value;                if(uqty1 == "" || uqty1 == 0){ uqty1 = 0; }
                                if(fcode == farm_code && icode1 == item_code){
                                    tqty = parseFloat(tqty) + parseFloat(uqty1);
                                }
                                
                                icode2 = document.getElementById("item_code2["+i+"]").value;
                                uqty2 = document.getElementById("kgs2["+i+"]").value;                if(uqty2 == "" || uqty2 == 0){ uqty2 = 0; }
                                if(fcode == farm_code && icode2 == item_code){
                                    tqty = parseFloat(tqty) + parseFloat(uqty2);
                                }
                            }
                            var stk_qty = parseFloat(item_details[0]) - parseFloat(tqty);
                            var stk_prc = parseFloat(item_details[1]);
                            document.getElementById("available_stock_2["+item_details[3]+"]").value = parseFloat(stk_qty).toFixed(2);
                            document.getElementById("available_price_2["+item_details[3]+"]").value = parseFloat(stk_prc).toFixed(5);
                        }
                        else{
                            alert("Item Stock not available, Kindly check before saving ...!");
                            document.getElementById("available_stock_2["+d+"]").value = 0;
                            document.getElementById("available_price_2["+d+"]").value = 0;
                        }
                    }
                }
            }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                if(window.screen.availWidth <= 400){
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; }
                }
                else{
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; }
                }
            }, 1000);
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function validatenum2(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount2(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(0); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>