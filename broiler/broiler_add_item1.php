<?php
//broiler_add_item1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['item1'];
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
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        //Offals selection Flag
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Item Master' AND `field_function` LIKE 'Plant Offals selection Flag' AND (`user_access` = '$user_code' OR `user_access` = 'all') AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $offals_sflag = mysqli_num_rows($query);

        //Sub-Category Access Flag
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Item Master' AND `field_function` = 'Sub-Category Access Flag' AND (`user_access` = '$user_code' OR `user_access` = 'all') AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $scat_aflag = mysqli_num_rows($query);
        if((int)$scat_aflag == 1){
            $sql = "SELECT * FROM `item_subcategory` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $iscat_code = $iscat_name = array();
            while($row = mysqli_fetch_assoc($query)){ $iscat_code[$row['code']] = $row['code']; $iscat_name[$row['code']] = $row['description']; }
        }

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'E-Invoices' AND `field_function` = 'Generate Auto E-Invoices' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $einv_gflag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'broiler_display_item1.php' AND `field_function` = 'Pounds Only' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $pound_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `broiler_ebill_item_units` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $eiunit_code = $eiunit_name = array();
        while($row = mysqli_fetch_assoc($query)){ $eiunit_code[$row['code']] = $row['code']; $eiunit_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` LIKE 'Breeder Daily Entry' AND `field_function` LIKE 'Fetch male and Female Items Based on selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfm_fflag = mysqli_num_rows($query);
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
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Item</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 d-flex flex-wrap justify-content-center align-items-center">
                                <form action="broiler_save_item1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row d-flex flex-wrap justify-content-center align-items-center">
                                        <!-- <div class="col-md-3">
                                            <div class="form-group">
                                                <label>HSN Code</label>
                                                <input type="text" name="hsn_code" id="hsn_code" class="form-control" onkeyup="validatename(this.id)">
                                            </div> 
                                        </div> -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Description<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" name="idesc" id="idesc" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)" onchange="check_duplicate();">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Size<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" name="item_size" id="item_size" class="form-control text-right" onkeyup="validatenum(this.id);">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Category<b style="color:red;">&nbsp;*</b></label>
                                                <select name="icat" id="icat" class="form-control select2" style="width: 100%;" <?php if((int)$scat_aflag == 1){ echo 'onchange="fetch_item_subcategory();"'; } ?>>
                                                    <option value="select">select</option>
                                                    <?php foreach($icat_code as $icats){ ?><option value="<?php echo $icats; ?>"><?php echo $icat_name[$icats]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if((int)$scat_aflag == 1){ ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Sub-Category<b style="color:red;">&nbsp;*</b></label>
                                                <select name="sub_category" id="sub_category" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php foreach($iscat_code as $iscats){ ?><option value="<?php echo $iscats; ?>"><?php echo $iscat_name[$iscats]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } else{ echo '<div class="col-md-2"></div>'; } ?>
                                        <?php if((int)$einv_gflag == 1){ ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>E-Units<b style="color:red;">&nbsp;*</b></label>
                                                <select name="einv_units" id="einv_units" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php foreach($eiunit_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $eiunit_name[$ucode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } else{ echo '<div class="col-md-2"></div>'; } ?>
                                        <?php if((int)$bfm_fflag == 1){ ?>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Breeder F&M Feed</label>
                                                <input type="checkbox" name="bfamf_flag" id="bfamf_flag" class="form-control" style="height:20px;" />
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row d-flex flex-wrap justify-content-center align-items-center">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Bag Capacity<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" name="bag_size" id="bag_size" class="form-control" onkeyup="validatenum(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Consumption Unit</label>
                                                <select name="icunit" id="icunit" class="form-control select2" style="width: 100%;" onchange="">
                                                    <option value="select">select</option>
                                                    <?php
                                                        $sql = "SELECT DISTINCT sunits as sunits FROM `item_units` ORDER BY `sunits` ASC"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                            <option value="<?php echo $row['sunits']; ?>"><?php echo $row['sunits']; ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-3">
                                             <div class="form-group">
                                                <label>Consumption Unit<b style="color:red;">&nbsp;*</b></label>
                                                <select name="icunit" id="icunit" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                </select>
                                            </div> 
                                        </div> -->
                                        <!-- <div class="col-md-2">
                                             <div class="form-group">
                                                <label>GST Value<b style="color:red;">&nbsp;*</b></label>
                                                <select name="gst_code" id="gst_code" class="form-control select2" style="width: 100%;">
                                                    <option value="">select</option>
                                                    <?php
                                                        $sql = "SELECT * FROM `tax_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `gst_type` ASC"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                            <option value="<?php echo $row['code']; ?>"><?php echo $row['gst_type']; ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div> 
                                        </div> -->
                                    </div>
                                    <div class="row d-flex flex-wrap justify-content-center align-items-center">
                                        <?php if((int)$offals_sflag == 1){ ?>
                                        <div class="col-md-2" align="center">
                                            <div class="form-group">
                                                <label for="offals_flag">Offals Item</label>
                                                <input type="checkbox" name="offals_flag" id="offals_flag" class="form-control" style="height:20px;">
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-2" align="center">
                                            <div class="form-group">
                                                <label for="lsflag">Low Stock Alert</label>
                                                <input type="checkbox" name="lsflag" id="lsflag" class="form-control" style="height:20px;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Low Stock Unit</label>
                                                <input type="text" name="lsqty" id="lsqty" class="form-control" placeholder="0" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row d-flex flex-wrap justify-content-center align-items-center">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Sector Access</label>
                                                <select name="sector_access[]" id="sector_access" class="form-control select2m" style="width: 250px;" multiple>
                                                    <option value="all" selected>-All-</option>
                                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row d-flex flex-wrap justify-content-center align-items-center" style="visibility:hidden;">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Opening Stock</label>
                                                <input type="text" name="ob_stock" id="ob_stock" class="form-control" placeholder="0" onkeyup="validatenum(this.id);calculatevalue(this.id);" onchange="validateamount(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Stock Price</label>
                                                <input type="text" name="price" id="price" class="form-control" placeholder="0" onkeyup="validatenum(this.id);calculatevalue(this.id);" onchange="validateamount(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Stock Amount</label>
                                                <input type="text" name="amount" id="amount" class="form-control" placeholder="0" onkeyup="validatenum(this.id);calculatevalue(this.id);" onchange="validateamount(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>As on Date</label>
                                                <input type="text" name="ob_date" id="ob_date" class="form-control datepicker" value="<?php echo date("d.m.Y"); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                            </div>
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>D-Flag<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="dupflag" id="dupflag" value="0">
                                            </div>
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
                window.location.href = 'broiler_display_item1.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = document.getElementById("idesc").value;
                var b = document.getElementById("icat").value;
                var d = document.getElementById("istored").value;
                var e = document.getElementById("icunit").value;
                var dupflag = document.getElementById("dupflag").value;
                var bag_size = document.getElementById("bag_size").value;

                var sel1 = document.getElementById("icat");
                var icat_name = sel1.options[sel1.selectedIndex].text;
                
                var l = true;
                if(parseFloat(dupflag) > 0){
                    alert("Item name already Exist \n Kindly check and try again ..!");
                    document.getElementById("idesc").focus();
                    l = false;
                }
                else if(a.length == 0){
                    alert("Enter Description ..!");
                    document.getElementById("idesc").focus();
                    l = false;
                }
                else if(b.match("select")){
                    alert("Select Category ..!");
                    l = false;
                }
                else if(icat_name.match("Broiler Feed") && bag_size == "" || icat_name.match("Bag") && bag_size == ""){
                    alert("Enter "+icat_name+": "+a+" Bag Size ..!");
                    document.getElementById("bag_size").focus();
                    l = false;
                }
                else if(d.match("select")){
                    alert("Select Storage unit ..!");
                    l = false;
                }
                else if(e.match("select")){
                    alert("Select Consumption unit ..!");
                    l = false;
                }
                else { }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function calculatevalue(x){
                if(x.match("amount")){ document.getElementById("price").value = ""; } else{ }
                var qty = document.getElementById("ob_stock").value;
                var price = document.getElementById("price").value;
                var amount = document.getElementById("amount").value;
                if(qty.length == 0 || qty == 0){ qty = 0; }
                if(price.length == 0 || price == 0){ price = 0; }
                if(amount.length == 0 || amount == 0){ amount = 0; }
                if(qty > 0 && price > 0){
                    amount = parseFloat(qty) * parseFloat(price);
                    document.getElementById("amount").value = amount.toFixed(2);
                }
                else if(qty > 0 && amount > 0){
                    price = parseFloat(amount) / parseFloat(qty);
                    document.getElementById("price").value = price.toFixed(2);
                }
                else{

                }
            }
            function validatename(x) {
                expr = /^[a-zA-Z0-9 (.&)_-]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
                }
                document.getElementById(x).value = a;
            }
            function validatenum(x) {
                expr = /^[0-9.]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^0-9.]/g, '');
                }
                document.getElementById(x).value = a;
            }
            function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            function setparentid(){
                removeAllOptions(document.getElementById("stype"));
                removeAllOptions(document.getElementById("ctype"));
                
                myselect1 = document.getElementById("stype"); 
                theOption1=document.createElement("OPTION"); 
                theText1=document.createTextNode("Select"); 
                theOption1.value = "select"; 
                theOption1.appendChild(theText1); 
                myselect1.appendChild(theOption1);
                
                myselect2 = document.getElementById("ctype"); 
                theOption2=document.createElement("OPTION"); 
                theText2=document.createTextNode("Select"); 
                theOption2.value = "select"; 
                theOption2.appendChild(theText2); 
                myselect2.appendChild(theOption2);
                
                
                var stypes = document.getElementById("type").value;
                <?php
                    $sql="SELECT * FROM `acc_schedules` WHERE `flag` = '1' AND `active` = '1' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ echo "if(stypes == '$row[subtype]'){"; ?> 
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("<?php echo $row['description']; ?>"); 
                        theOption1.value = "<?php echo $row['code']; ?>"; 
                        theOption1.appendChild(theText1); myselect1.appendChild(theOption1);	
                    <?php echo "}"; } ?>
                    
                    <?php
                    $sql="SELECT * FROM `acc_controltype` ORDER BY `controltype` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ echo "if(stypes == '$row[type]'){"; ?> 
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("<?php echo $row['controltype']; ?>"); 
                        theOption2.value = "<?php echo $row['controltype']; ?>"; 
                        theOption2.appendChild(theText2); myselect2.appendChild(theOption2);	
                    <?php echo "}"; } ?>
                    
                    
            }
            function setcunits(){
                removeAllOptions(document.getElementById("icunit"));
                
                myselect1 = document.getElementById("icunit"); 
                theOption1=document.createElement("OPTION"); 
                theText1=document.createTextNode("Select"); 
                theOption1.value = "select"; 
                theOption1.appendChild(theText1); 
                myselect1.appendChild(theOption1);
                
                var stypes = document.getElementById("istored").value;
                <?php
                    $sql="SELECT * FROM `item_units` ORDER BY `cunits` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ echo "if(stypes == '$row[sunits]'){"; ?> 
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("<?php echo $row['cunits']; ?>"); 
                        theOption1.value = "<?php echo $row['cunits']; ?>"; 
                        theOption1.appendChild(theText1); myselect1.appendChild(theOption1);	
                    <?php echo "}"; } ?>
                
            }
			function check_duplicate(){
				var b = document.getElementById("idesc").value;
				var c = "add";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_item_duplicates.php?cname="+b+"&type="+c;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Item Details are available with the same name.\n Kindly change the name");
								document.getElementById("dupflag"). value = 1;
							}
							else {
								document.getElementById("dupflag"). value = 0;
							}
						}
					}
				}
				else { }
			}
            function fetch_item_subcategory(){
				var icat = document.getElementById("icat").value;
                removeAllOptions(document.getElementById("sub_category"));
                
				if(icat != "select"){
					var subcats = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_itemsubcategory.php?category="+icat;
                    //window.open(url);
					var asynchronous = true;
					subcats.open(method, url, asynchronous);
					subcats.send();
					subcats.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var scat_list = this.responseText;
							$('#sub_category').append(scat_list);
						}
					}
				}
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
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