<?php
//chicken_edit_supplierpayment2.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $mode_code = $mode_name = array();
    while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $method_code = $method_name = $method_type = array();
    while($row = mysqli_fetch_assoc($query)){ $method_code[$row['code']] = $row['code']; $method_name[$row['code']] = $row['description']; $method_type[$row['code']] = $row['ctype']; }

    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Display TCDS Calculations' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dtcds_flag = mysqli_num_rows($query);

     $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Hide Dcno and Sector' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $hdcsec_flag = mysqli_num_rows($query);

    //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `pur_payments` WHERE `trnum` = '$ids'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $ccode = $row['ccode'];
                $dcno = $row['docno'];
                $mode = $row['mode'];
                $method = $row['method'];
                $type = $row['type'];
                $amount1 = round($row['amount1'],2);
                $tcds_per = round($row['tcds_per'],2);
                $tcds_amt = round($row['tcds_amt'],2);
                $amount = round($row['amount'],2);
                $vtype = $row['vtype'];
                $warehouse = $row['warehouse'];
                $remarks = $row['remarks'];

                $finaltotal = round($row['finaltotal'],2);
                $remarks = $row['remarks'];
            }

            $sql = "SELECT * FROM `main_crdrnote` WHERE `active` = '1' AND `link_trnum` = '$ids'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $tdsamt = $row['amount'];
            }
            if($mode == "MOD-001"){ $mtype = "Cash"; } else{ $mtype = "Bank"; }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Payments</div>
                <form action="chicken_modify_supplierpayment2.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Supplier<b style="color:red;">&nbsp;*</b></th>
                                        <th>Mode<b style="color:red;">&nbsp;*</b></th>
                                        <th>Code<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>TDS</th>
                                        <th>AMT</th>
                        
                                        <th>Remarks</th>
                                        <th style="visibility:hidden;">T%</th>
                                        <th style="visibility:hidden;">TA</th>
                                        <th style="visibility:hidden;">FA</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per();" readonly /></td>
                                        <td><select name="ccode" id="ccode" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($ccode == $scode){ echo "selected"; } ?>><?php echo $sup_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="mode" id="mode" class="form-control select2" style="width:180px;" onchange="update_coa_method();"><option value="select">-select-</option><?php foreach($mode_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($mode == $scode){ echo "selected"; } ?>><?php echo $mode_name[$scode]; ?></option><?php } ?></select></td>
                                        <td>
                                            <select name="code" id="code" class="form-control select2" style="width:180px;">
                                                <option value="select">-select-</option>
                                                <?php foreach($method_code as $scode){ if($mtype == $method_type[$scode]){ ?><option value="<?php echo $scode; ?>" <?php if($method == $scode){ echo "selected"; } ?>><?php echo $method_name[$scode]; ?></option><?php } } ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="amount1" id="amount1" class="form-control text-right" value="<?php echo $amount1; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt();" onchange="validate_amount(this.id);" /></td>

                                        <td><input type="checkbox" name="tcds_chk" id="tcds_chk" <?php if($tcds_amt > 0){ echo "checked"; } ?> onchange="calculate_row_amt();" /></td>

                                        <td><input type="text" name="tcds_chk_val" id="tcds_chk_val" class="form-control text-right" style="width:90px;" readonly/></td>

                                        
                                        <td><textarea name="remarks" id="remarks" class="form-control" style="height: 23px;"><?php echo $remarks; ?></textarea></td>
                                        <td style="visibility:visible;"><input type="text" name="tcds_per" id="tcds_per" value="<?php echo $tcds_per; ?>" class="form-control text-right" style="width:60px;" readonly /></td>
                                        <td style="visibility:visible;"><input type="text" name="tcds_amt" id="tcds_amt" value="<?php echo $tcds_amt; ?>" class="form-control text-right" style="width:60px;" readonly /></td>
                                        <td style="visibility:visible;"><input type="text" name="amount" id="amount" value="<?php echo $amount; ?>" class="form-control text-right" style="width:60px;" readonly /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                function return_back(){
                    window.location.href = "chicken_display_supplierpayment2.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true; var date = ccode = mode = code = sector = ""; var c = amount1 = amount = 0;
                    
                    date = document.getElementById("date").value;
                    ccode = document.getElementById("ccode").value;
                    mode = document.getElementById("mode").value;
                    code = document.getElementById("code").value;
                    amount1 = document.getElementById("amount1").value; if(amount1 == ""){ amount1 = 0; }
                    sector = document.getElementById("sector").value;
                    amount = document.getElementById("amount").value; if(amount == ""){ amount = 0; }

                    if(date == ""){
                        alert("Please select Date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(ccode == "select"){
                        alert("Please select Supplier");
                        document.getElementById("ccode").focus();
                        l = false;
                    }
                    else if(mode == "select"){
                        alert("Please select Mode");
                        document.getElementById("mode").focus();
                        l = false;
                    }
                    else if(code == "select"){
                        alert("Please select Code");
                        document.getElementById("code").focus();
                        l = false;
                    }
                    else if(parseFloat(amount1) == 0){
                        alert("Please enter Amount");
                        document.getElementById("amount1").focus();
                        l = false;
                    }
                    else if(parseFloat(amount) == 0){
                        alert("Please enter Amount");
                        document.getElementById("amount1").focus();
                        l = false;
                    }
                    else{ }
                    
                    if(l == true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                function fetch_tcds_per(){
                    var date = document.getElementById("date").value;
                    var tdsper = new XMLHttpRequest();
                    var method = "GET";
                    var url = "main_gettcdsvalue.php?type=TDS&cdate="+date;
                    //window.open(url);
                    var asynchronous = true;
                    tdsper.open(method, url, asynchronous);
                    tdsper.send();
                    tdsper.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var tcds_per = this.responseText;
                            if(tcds_per != ""){
                                document.getElementById("tcds_per").value = tcds_per;
                                calculate_row_amt();
                            }
                        }
                    }
                } fetch_tcds_per();
                function calculate_row_amt(){
                    var amount1 = document.getElementById("amount1").value; if(amount1 == ""){ amount1 = 0; }
                    var tcds_chk = document.getElementById("tcds_chk");
                    var tcds_per = tcds_amt = 0;
                    if(tcds_chk.checked == true){
                        tcds_per = document.getElementById("tcds_per").value; if(tcds_per == ""){ tcds_per = 0; }
                        tcds_amt = (parseFloat(amount1) * (parseFloat(tcds_per) / 100));
                        //tcds_chk_val
                        
                    }
                    document.getElementById("tcds_chk_val").value = tcds_amt;
                    document.getElementById("tcds_amt").value = parseFloat(tcds_amt).toFixed(2);

                    var amount = parseFloat(amount1) - parseFloat(tcds_amt);
                    document.getElementById("amount").value = parseFloat(amount).toFixed(2);
                }
                function update_coa_method(){
                    var b = document.getElementById("mode").value;

                    removeAllOptions(document.getElementById("code"));
                    myselect = document.getElementById("code"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);

                    if(b.match("MOD-001")){
                        <?php
                        $sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                    else {
                        <?php
                        $sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Bank' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                }
			    function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
            <script src="handle_ebtn_as_tbtn.js"></script>
            <script>
                //Date Range selection
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            </script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
