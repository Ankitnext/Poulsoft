<?php
//chicken_add_vehexp1.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d");
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = "";
    $trno_dt1 = generate_transaction_details($date,"vehexp1","GSIN","display",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

    $tcds_per = 0;
    $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
    
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql); $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; $tweight_flag = $row['tweight_flag']; $eweight_flag = $row['eweight_flag']; }
    if($jals_flag == ""){ $jals_flag = 0; } if($birds_flag == ""){ $birds_flag = 0; } if($tweight_flag == ""){ $tweight_flag = 0; } if($eweight_flag == ""){ $eweight_flag = 0; }

    $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'chicken_display_vehexp1.php' AND `field_function` LIKE 'Show Item Short Name and Item Name Together' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $disn_flag = mysqli_num_rows($query);

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Item Master' AND `field_function` LIKE 'Short-Name' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $sname_flag = mysqli_num_rows($query);

    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; if((int)$disn_flag > 0 && (int)$sname_flag > 0){ $item_name[$row['code']] = $row['short_name'].". ".$row['description']; } else{ $item_name[$row['code']] = $row['description']; } }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `transport_flag` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $tport_code = $tport_name = array();
    while($row = mysqli_fetch_assoc($query)){ $tport_code[$row['code']] = $row['code']; $tport_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cash_code = $cash_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cash_code[$row['code']] = $row['code']; $cash_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $acode[$row['code']] = $row['code'];
        $adesc[$row['code']] = $row['description'];
    }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $bank_code = $bank_name = array();
    while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $desc_code = $desc_name = array();
    while($row = mysqli_fetch_assoc($query)){ $desc_code[$row['code']] = $row['code']; $desc_name[$row['code']] = $row['description']; }

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Vehicle Expence</span>
                    <button type="button" class="btn btn-success btn-sm" onclick="open_new();"><i class="fa-solid fa-plus"></i></button>
                </div>
                <form action="chicken_save_vehexp1.php" method="post"  enctype="multipart/form-data"  onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="" readonly />
                            </div>
                            <div class="form-group" style="width:290px;">
                                <label for="warehouse">Vehicle No.<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:280px;" onchange="">
                                    <option value="select">-select-</option>
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:290px;">
                                <label for="mode">Mode<b style="color:red;">&nbsp;*</b></label>
                                <select name="mode" id="mode" class="form-control select2" style="width:280px;" onchange="updatecode();">
                                    <option value="select">-select-</option>
                                    <?php foreach($acode as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $adesc[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:290px;">
                                <label for="code">Cash/Bank<b style="color:red;">&nbsp;*</b></label>
                                <select name="code" id="code" class="form-control select2" >
                                     <option value="select">select</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Description<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th style="width:70px;"></th>
                                        <th style="width:20px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="descs[]" id="descs[0]" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><?php foreach($desc_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $desc_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id)" onchange="validate_amount(this.id);" /></td>
                                        <td><textarea name="remark[]" id="remark[0]" class="form-control text-right" style="width:90px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                    </tr>
                                </tbody>
                               
                            </table>
                        </div><br/>
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:190px;">
                                <label for="date">Doc-1</label>
                                <input type="file" name="doc1" id="doc1" class="form-control" onchange="show_delete_btn(this.id,'clearButton')" style="width:180px;" placeholder="select Document" />
                                <i class="fa fa-close" style="color:red;visibility:hidden;" title="delete" id="clearButton" onclick="clear_file(this.id, 'doc1')"></i>
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="date">Doc-2</label>
                                <input type="file" name="doc2" id="doc2" class="form-control" onchange="show_delete_btn(this.id,'clearButton1')" style="width:180px;" placeholder="select Document" />
                                <i class='fa fa-close' style='color:red; visibility: hidden;' title='delete' id="clearButton1" onclick="clear_file(this.id,'doc2')"></i>
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="date">Doc-3</label>
                                <input type="file" name="doc3" id="doc3" class="form-control" onchange="show_delete_btn(this.id,'clearButton2')" style="width:180px;" placeholder="select Document" />
                                <i class='fa fa-close' style='color:red; visibility: hidden;' title='delete' id="clearButton2" onclick="clear_file(this.id,'doc3')"></i>
                            </div>
                        </div>
                       
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <!-- <button type="submit" name="sub_pt" id="sub_pt" class="btn btn-sm text-white bg-success">Submit & Print</button>&ensp; -->
                                <button type="submit" name="submit" id="submit" value="addpage" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                function return_back(){
                    window.location.href = "chicken_display_vehexp1.php";
                }
                function clear_file(a,b) { 
                    document.getElementById(b).value = '';
                    document.getElementById(a).style.visibility = 'hidden';
                }
                function open_new(){
                    window.open('chicken_add_vehexp1.php', '_blank');
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1";
                    document.getElementById("submit").style.visibility = "hidden";
                   // document.getElementById("sub_pt").style.visibility = "hidden";
                    var date = document.getElementById("date").value;
                    var mode = document.getElementById("mode").value;
                    var code = document.getElementById("code").value;
                    var warehouse = document.getElementById("warehouse").value;

                    var l = true;

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(warehouse == "select"){
                        alert("Please select Vehicle No.");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(mode == "select"){
                        alert("Please select Mode of Payment");
                        document.getElementById("mode").focus();
                        l = false;
                    }
                    else if(code == "select"){
                        alert("Please select Cash/Bank");
                        document.getElementById("code").focus();
                        l = false;
                    }
                    else{
                        var descs = ""; var c = amount = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                descs = document.getElementById("descs["+d+"]").value;
                                amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                                

                                if(descs == "select"){
                                    alert("Please select Description in row: "+c);
                                    document.getElementById("descs["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(amount) == 0){
                                    alert("Please enter Amount in row: "+c);
                                    document.getElementById("amount["+d+"]").focus();
                                    l = false;
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
                       // document.getElementById("sub_pt").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                function show_delete_btn(a,b) {
                    var selected_file = document.getElementById(a);
                    var hidedeletebutton = document.getElementById(b);

                    if (selected_file.files.length > 0) {
                    hidedeletebutton.style.visibility = 'visible'; 
                    } else {
                    hidedeletebutton.style.visibility = 'hidden'; 
                    }
                }
                function updatecode(a){
				// var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var mode = document.getElementById("mode").value;
				removeAllOptions(document.getElementById("code"));
				
				myselect = document.getElementById("code"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    if(mode.match("MOD-001")){
                        <?php
                        $sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Cash%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                    else {
                        <?php
                        $sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Bank%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                }
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="descs[]" id="descs['+d+']" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><?php foreach($desc_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $desc_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><textarea name="remark[]" id="remark['+d+']" class="form-control text-right" style="width:90px; "></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                   
                }
                function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
