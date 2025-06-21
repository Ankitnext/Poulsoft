<?php
//chicken_add_saleorder.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Closing Stock"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $fdate = date("Y-m-d");
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
				while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

				$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Warehouse%' AND `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $office_alist = array();
				while($row = mysqli_fetch_assoc($query)){ $office_alist[$row['code']] = $row['code']; }

                $office_list = implode("','",$office_alist);
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `type` IN ('$office_list') ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
				while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $spzflag = $row['spzflag']; $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; $pst_prate_flag = $row['pst_prate_flag']; }
				if($spzflag == "" || $spzflag == 0 || $spzflag == NULL){ $spzflag = 0; } else{ }

				$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
                $query = mysqli_query($conn,$sql); $cus_code = $cus_name = $sup_code = $sup_name = array();
				while($row = mysqli_fetch_assoc($query)){
					if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; } else{ }
					if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; } else{ }
				}

				// $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
				// $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
				// $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
				// $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcsper = $row['tcds']; }
				 $idisplay = ''; $ndisplay = 'style="display:none;';

                $group_details = array();
                $sql = "select code,description from main_groups WHERE gtype LIKE '%C%'";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $group_details[$row['code']] = $row['description'];
                }

    
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body >
            <div class="card border-secondary mb-3" style="padding-left:50px">
                <div class="card-header">Add Sales Order</div>
                    <form action="chicken_save_saleorder.php" method="post" role="form" onsubmit="return checkval()">
						<div class="row">
							<div class="form-group col-md-1">
								<label>Date<b style="color:red;">&nbsp;*</b></label>
								<input type="text" style="width:100px;" class="form-control datepickers" name="pdate" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" id="pdate" readonly>
							</div>
							<div class="form-group col-md-2">
								<label>Warehouse<b style="color:red;">&nbsp;*</b></label>
								<select name="wcodes" id="wcodes" class="form-control select2" style="width: 100%;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $sector_code[$it]; ?>"><?php echo $sector_name[$it]; ?></option><?php } ?></select>
							</div>
							<div class="form-group col-md-2">
								<label>Items<b style="color:red;">&nbsp;*</b></label>
								<select name="scat" id="scat" class="form-control select2" style="width:100%;" onchange=""><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select>
							</div>
							
							<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
								<label>incr<b style="color:red;">&nbsp;*</b></label>
								<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
							</div>
							
							<div class="form-group col-md-1" style="visibility:hidden;">
								<label>ECount<b style="color:red;">&nbsp;*</b></label>
								<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
							</div>
						</div>
						
							
						<div class="col-md-18 row_body2">
							<table style="width:auto;line-height:30px;" id="tab3">
								<tr style="line-height:30px;">
									<th style="text-align:center;"><label>Customer Name<b style="color:red;">&nbsp;*</b></label></th>
									<th style="text-align:center;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
									<th style="text-align:center;"><label>Supplier Name<b style="color:red;">&nbsp;*</b></label></th>
									<th style="text-align:center;"><label>Place</label></th>
									<th style="text-align:center;"><label>Supervisor No.</label></th>
									<th style="text-align:center;"><label>Vehicle No.</label></th>
									<th style="text-align:center;"><label>Remarks</label></th>
									<th style="text-align:center;"></th>
								</tr>
								<tbody id="bodytab">
								<tr id="tblrow[0]
                                " style="margin:5px 0px 2px 0px;">
									<td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames[0]" class="form-control select2" style="width: 150px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
									<td><input type="text" name="cus_qty[]" id="cus_qty[0]" onkeyup="validatenum(this.id);" onchange="validateamount(this.id)" class="form-control amount-format"></td>
									<td style="width: 150px;padding-right:5px;"><select name="snames[]" id="snames[0]" class="form-control select2" style="width: 250px;" ><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]; ?>"><?php echo $sup_name[$cc]; ?></option><?php } ?></select></td>
									<td><input type="text" name="place[]" id="place[0]" class="form-control amount-format" ></td>
									<td><input type="text" name="sv_no[]" id="sv_no[0]" class="form-control amount-format" ></td>
									<td><input type="text" name="v_no[]" id="v_no[0]" class="form-control" style="width:130px;" ></td>
									<td style="width: auto;"><textarea name="narr[]" id="narr[0]" class="form-control" style="height:23px;"></textarea></td>
									<td style="width: 60px;"><a href="JavaScript:void(0);" name="addval[]" id="addval[0]" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:void(0);" name="rmval[]" id="rmval[0]" onclick="removerow(this.id)" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
								</tr>
								</tbody>
							</table><br/>
							
							<div class="box-body" align="center">
									<button type="submittrans" name="submittrans" id="submit" value="addpage" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
					                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
							</div>
						</div>
					</form>
            </div>

            <script>
                function return_back(){
                    window.location.href = "chicken_display_saleorder.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("pdate").value;
                    var wcodes = document.getElementById("wcodes").value;
                    var scat = document.getElementById("scat").value;

                    if(date == ""){
                        alert("Please select Date ");
                        document.getElementById("pdate").focus();
                        l = false;
                    }
                    else if(wcodes == ""){
                        alert("Please select Warehouse ");
                        document.getElementById("wcodes").focus();
                        l = false;
                    }
                    else if(scat == ""){
                        alert("Please select Item Category ");
                        document.getElementById("scat").focus();
                        l = false;
                    } else {
                    var cnames = snames = place = sv_no = v_no = ""; var cus_qty = 0;
                    var incr = parseInt(document.getElementById("incr").value);
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            cnames = document.getElementById("cnames["+d+"]").value;
                            cus_qty = document.getElementById("cus_qty["+d+"]").value; if(cus_qty == ""){ cus_qty = 0; }
                            snames = document.getElementById("snames["+d+"]").value;
                            place = document.getElementById("place["+d+"]").value;
                            sv_no = document.getElementById("sv_no["+d+"]").value;
                            v_no = document.getElementById("v_no["+d+"]").value;
                            
                            if(cnames == "select"){
                                alert("Please select Customer names in row: "+c);
                                document.getElementById("cnames["+d+"]").focus();
                                l = false;
                                break;
                            }
                            else if(parseFloat(cus_qty) == 0){
                                alert("Please enter Quantity in row: "+c);
                                document.getElementById("cus_qty["+d+"]").focus();
                                l = false;
                                break;
                            }
                             else if(snames == "select"){
                                alert("Please select Supplier names in row: "+c);
                                document.getElementById("snames["+d+"]").focus();
                                l = false;
                                break;
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
                function rowgen(a){
                  
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				document.getElementById("addval["+d+"]").style.visibility = "hidden";
				document.getElementById("rmval["+d+"]").style.visibility = "hidden";
				d++; var e = d; document.getElementById("incr").value = e;
				html = '';
				html+= '<tr style="margin:5px 0px 5px 0px;" id="tblrow['+e+']">';
				html+= '<td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames['+e+']" class="form-control select" style="width: 150px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>';
				html+= '<td><input type="text" name="cus_qty[]" id="cus_qty['+e+']" onkeyup="validatenum(this.id);" onchange="validateamount(this.id)" class="form-control amount-format"></td>';
				html+= '<td style="width: 150px;padding-right:5px;"><select name="snames[]" id="snames['+e+']" class="form-control select" style="width: 250px;"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]; ?>"><?php echo $sup_name[$cc]; ?></option><?php } ?></select></td>';
				html+= '<td><input type="text" name="place[]" id="place['+e+']" class="form-control amount-format" ></td>';
				html+= '<td><input type="text" name="sv_no[]" id="sv_no['+e+']" class="form-control" style="" ></td>';
				html+= '<td><input type="text" name="v_no[]" id="v_no['+e+']" class="form-control" style="width:130px;" ></td>';
				html+= '<td style="width: auto;"><textarea name="narr[]" id="narr['+e+']" class="form-control" style="height:23px;"></textarea></td>';
				html+= '<td style="width: 60px;"><a href="JavaScript:void(0);" name="addval[]" id="addval['+e+']" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="JavaScript:void(0);" name="rmval[]" id="rmval['+e+']" onclick="removerow(this.id)" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				html+= '</tr>';
				$('#bodytab').append(html); $('.select').select2();
				document.getElementById("addval["+e+"]").style.visibility = "visible";
				document.getElementById("rmval["+e+"]").style.visibility = "visible";
                // checkitemtype(); add_supplier_prices2(e); add_customer_prices2(e);
                 var x = "addval["+d+"]";
                  // fetch_group_customer(x);
			}

           
                function removerow(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				document.getElementById('tblrow['+d+']').remove();
				d--; var e = d; document.getElementById("incr").value = e;
				if(d > 0){
					document.getElementById("addval["+e+"]").style.visibility = "visible";
					document.getElementById("rmval["+e+"]").style.visibility = "visible";
				}
				else{
					document.getElementById("addval["+e+"]").style.visibility = "visible";
				}
			}
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
                }

            function fetch_group_customer(a) {
				var selectedCode = document.getElementById(a).value;
				console.log("Selected Code:", selectedCode);
				var incr = document.getElementById("incr").value;
				
				if (selectedCode != "selected") {
					var fetch_items = new XMLHttpRequest();
					var method = "GET";
					var url = "chicken_fetch_customer_bycode.php?scode=" + selectedCode;
					var asynchronous = true;

					fetch_items.open(method, url, asynchronous);
					fetch_items.send();

					fetch_items.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							var customerData = JSON.parse(this.responseText);
							console.log("Customer Data:", customerData);
								
							if (customerData.length > 0) {
								for(var d = 0;d <= incr;d++){
								removeAllOptions(document.getElementById("cnames["+d+"]"));
									myselect = document.getElementById("cnames["+d+"]");
									theOption1=document.createElement("OPTION");
									theText1=document.createTextNode("-select-");
									theOption1.value = "select";
									theOption1.appendChild(theText1);
									myselect.appendChild(theOption1);
									
									customerData.forEach(function(customer) {
										var theOption1 = document.createElement("OPTION");
										var theText1 = document.createTextNode(customer.name);
										theOption1.value = customer.code;
										theOption1.appendChild(theText1);
										myselect.appendChild(theOption1);
									});
								}	

							} else {
								for(var d = 0;d <= incr;d++){
									removeAllOptions(document.getElementById("cnames["+d+"]"));
									myselect = document.getElementById("cnames["+d+"]");
									theOption1=document.createElement("OPTION");
									theText1=document.createTextNode("-select-");
									theOption1.value = "select";
									theOption1.appendChild(theText1);
									myselect.appendChild(theOption1);
									
								}
                                        alert("No customers found for the selected code.");
                            }
						}
					};
				}
            }
            function fetch_sup_branches(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var scode = document.getElementById("snames["+d+"]").value;

                removeAllOptions(document.getElementById("supbrh_code["+d+"]"));
                myselect1 = document.getElementById("supbrh_code["+d+"]");
                theOption1 = document.createElement("OPTION");
                theText1 = document.createTextNode("select");
                theOption1.value = "select";
                theOption1.appendChild(theText1);
                myselect1.appendChild(theOption1);
				 
                if(scode != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "chicken_fetch_supplier_branch.php?scode="+scode+"&row_count="+d;
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var brh_dt1 = this.responseText;
                            var brh_dt2 = brh_dt1.split("[@$&]");
                            var count = brh_dt2[0];
                            var rows = brh_dt2[1];
                            var brh_lt1 = brh_dt2[2];
                            if(parseInt(count) > 0){
                                var obj = JSON.parse(brh_lt1);
                                var i = 0; oval = ovl = "";
                                for(i = 0;i < count;i++){
                                    theOption1=document.createElement("OPTION");
                                    theText1=document.createTextNode(obj[i].name);
                                    theOption1.value = obj[i].code;
                                    theOption1.appendChild(theText1);
                                    myselect1.appendChild(theOption1);
                                }
                            }
                            else{
                                alert("Branch details not available, please check and try again.");
                            }
                        }
                    }
                }
            }

                document.addEventListener("keydown", (e) => { var key_search = document.activeElement.id.includes("["); if(key_search == true){ var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0]; document.getElementById("incrs").value = d; } if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function () { $('#submittrans').click(); }); } } else{ } });
				function validate_count(x){ expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
				function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
				function validateamount(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }

                
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
