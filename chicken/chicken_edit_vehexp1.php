<?php
//chicken_edit_vehexp1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

$invoiceid = $_GET['trnum'];
if($access_error_flag == 0){
    $fdate = date("Y-m-d"); $tcds_per = 0;
    $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
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

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $bank_code = $bank_name = array();
    while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `acc_vouchers` WHERE `trnum` = '$ids' AND `flag` = '0' AND `tdflag` = '0' AND `pdflag` = '0' AND `trlink` = 'chicken_display_vehexp1.php'";
            $query = mysqli_query($conn,$sql); $c = 0;
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $trnum = $row['trnum'];
                $mode = $row['mode'];
                $fcoa = $row['fcoa'];
                $warehouse = $row['warehouse'];
                $tcoa[$c] = $row['tcoa'];
                $remarks[$c] = $row['remarks'];
                $amount[$c] = round($row['amount'],5);
                $existing_image_path = $row['doc1_path'];
                $existing_image_path2 = $row['doc2_path'];
                $existing_image_path3 = $row['doc3_path'];
                
                $c++;
            } $c = $c - 1;

            //Fetch Account Modes
            $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $cash_mode = $bank_mode = "";
            while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Cash"){ $cash_mode = $row['code']; } else if($row['description'] == "Bank"){ $bank_mode = $row['code']; } }
            
            $sql = "SELECT * FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$cash_mode' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
            $query = mysqli_query($conn,$sql); $cash_ramt = 0; $cash_trno = $cash_rcode = "";
            while($row = mysqli_fetch_assoc($query)){ $cash_trno = $row['trnum']; $cash_rcode = $row['method']; $cash_ramt = round($row['amount'],5); }

            $sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $acode[$row['code']] = $row['code'];
                $adesc[$row['code']] = $row['description'];
            }

            $sql = "SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Cash%' OR `ctype` LIKE '%Bank%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $cbcode[$row['code']] = $row['code'];
                $cbtype[$row['code']] = $row['ctype'];
                $cbdesc[$row['code']] = $row['description'];
            }
            $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $desc_code = $desc_name = array();
            while($row = mysqli_fetch_assoc($query)){ $desc_code[$row['code']] = $row['code']; $desc_name[$row['code']] = $row['description']; }        

            $sql = "SELECT * FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$bank_mode' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
            $query = mysqli_query($conn,$sql); $bank_ramt = 0; $bank_trno = $bank_rcode = "";
            while($row = mysqli_fetch_assoc($query)){ $bank_trno = $row['trnum']; $bank_rcode = $row['method']; $bank_ramt = round($row['amount'],5); }

            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Receipt</div>
                <form action="chicken_modify_vehexp1.php" method="post" enctype="multipart/form-data" onsubmit="return checkval();">
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
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($warehouse == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:290px;">
                                <label for="mode">Mode<b style="color:red;">&nbsp;*</b></label>
                                <select name="mode" id="mode" class="form-control select2" style="width:280px;" onchange="updatecode();">
                                    <option value="select">-select-</option>
                                    <?php foreach($acode as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($mode == $scode){ echo "selected"; } ?>><?php echo $adesc[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:290px;">
                                <label for="code">Cash/Bank<b style="color:red;">&nbsp;*</b></label>
                                <select name="code" id="code" class="form-control select2" >
                                    <option value="select">select</option>
                                   
                                     <?php foreach($cbcode as $coacode){ ?><option value="<?php echo $coacode; ?>" <?php if($fcoa == $coacode){ echo "selected"; } ?>><?php echo $cbdesc[$coacode]; ?></option><?php } ?>

                                    
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Description<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php $incr = $c; for($c = 0;$c <= $incr;$c++){  ?>
                                    <tr style="margin:5px 0px 5px 0px;" id="row_no[<?php echo $c; ?>]">
                                        <td><select name="descs[]" id="descs[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><?php foreach($desc_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($tcoa[$c] == $scode){ echo "selected"; } ?>><?php echo $desc_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="amount[]" id="amount[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $amount[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><textarea name="remark[]" id="remark[<?php echo $c; ?>]" class="form-control" style=""><?php echo $remarks[$c];?></textarea></td>
                                        <?php
                                        if($c == $incr){ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                        else{ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                        echo '<a href="javascript:void(0);" id="addrow['.$c.']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;';
                                        if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow['.$c.']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                        echo '</td>';
                                        ?>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:190px;">
                                <label for="date">Doc-1<b style="color:red;">&nbsp;*</b></label>
                                <input type="file" name="doc1" id="doc1" class="form-control" onchange="show_delete_btn(this.id,'clearButton')" style="width:180px;" placeholder="select Document" />
                                <i class="fa fa-close" style="color:red;visibility:hidden;" title="delete" id="clearButton" onclick="clear_file(this.id, 'doc1')"></i>
                                <span id="r1">    
                                <?php if($existing_image_path != ""){ echo "<br/><span >".basename(parse_url($existing_image_path, PHP_URL_PATH))."</span>"; ?> 
                                    <a href='javascript:void(0)' id='<?php echo $ids; ?>' value='<?php echo $ids; ?>' onclick='check_delete(this.id,"Doc-1","doc1_path","r1")'>
                                        &nbsp;&nbsp; <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                    </a>
                                <?php } ?>
                                
                                </span>
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="date">Doc-2<b style="color:red;">&nbsp;*</b></label>
                                <input type="file" name="doc2" id="doc2" class="form-control" onchange="show_delete_btn(this.id,'clearButton1')" style="width:180px;" placeholder="select Document" />
                                <i class='fa fa-close' style='color:red; visibility: hidden;' title='delete' id="clearButton1" onclick="clear_file(this.id,'doc2')"></i>
                                <span id='r2'>
                                <?php if($existing_image_path2 != ""){ echo "<br/><span >".basename(parse_url($existing_image_path2, PHP_URL_PATH))."</span>"; ?> 
                                    <a href='javascript:void(0)' id='<?php echo $ids; ?>' value='<?php echo $ids; ?>' onclick='check_delete(this.id,"Doc-2","doc2_path","r2")'>
                                        &nbsp;&nbsp; <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                    </a>
                                <?php } ?>
                                </span>
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="date">Doc-3<b style="color:red;">&nbsp;*</b></label>
                                <input type="file" name="doc3" id="doc3" class="form-control" onchange="show_delete_btn(this.id,'clearButton2')" style="width:180px;" placeholder="select Document" />
                                <i class='fa fa-close' style='color:red; visibility: hidden;' title='delete' id="clearButton2" onclick="clear_file(this.id,'doc3')"></i>
                                <span id='r3'>
                                <?php if($existing_image_path3 != ""){ echo "<br/><span>".basename(parse_url($existing_image_path3, PHP_URL_PATH))."</span>"; ?>
                                        <a href='javascript:void(0)' id='<?php echo $ids; ?>' value='<?php echo $ids; ?>' onclick='check_delete(this.id,"Doc-3","doc3_path","r3")'>
                                        &nbsp;&nbsp; <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                    </a>
                                <?php } ?>
                                </span>
                            </div>
                        </div>
                       
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $incr; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" value="editpage"  class="btn btn-sm text-white bg-success">Update</button>&ensp;
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
                function clear_file(a,b) { 
                    document.getElementById(b).value = '';
                    document.getElementById(a).style.visibility = 'hidden';
                }
                function check_delete(a,b,c,d){
                    var trnum = a;
                    var reference = b;
                    var column = c;
                    console.log(trnum);
                    console.log(reference);
                    console.log(column);
                    var confirmation = confirm("Are you sure you want to delete "+reference+" ?");
                    if (confirmation) {
                        //alert('Successfully deleted');
                        var fetch_fltrs = new XMLHttpRequest();
                        var method = "GET";
                        var url = "chicken_delete_refdoc2.php?trnum="+trnum+"&colm="+column+"&type=vehexp1"
                       // window.open(url);
                        var asynchronous = true;
                        fetch_fltrs.open(method, url, asynchronous);
                        fetch_fltrs.send();
                        fetch_fltrs.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){  
                            // var res = this.responseText; 
                            // alert(res);   
                                var spanElement = document.getElementById(d);
                                if (spanElement) {
                                    spanElement.style.display = "none";
                                }
                            }
                        }
                    } else {
                        console.log("Delete action canceled");
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
                    html += '<td><textarea name="remark[]" id="remark['+d+']" class="form-control text-right" style=" "></textarea></td>';
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
                    calculate_final_total_amount();
                }
                
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
