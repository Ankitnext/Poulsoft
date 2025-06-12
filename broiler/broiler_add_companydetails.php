<?php
//broiler_add_companydetails.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['companydetails'];
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
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <style>
        body{
            overflow: hidden;
        }
        .form-control{
            font-size: 13px;
        }
        .cke_notifications_area {
            display: none !important;
        }

    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Company Profile</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_companydetails.php" method="post" enctype="multipart/form-data" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="image">Add Logo</label>
                                                <input type="file" name="logo_image" id="image" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            
                                            <div class="form-group">
                                                <label for="tdate">Choose Display Type</label>
                                                <select name="ctype" id="ctype" class="form-control select2">
													<option value="all">All</option>
													<option value="Company Profile">Company Profile</option>
													<option value="Purchase Invoice">Purchase Invoice</option>
													<option value="Sales Invoice">Sales Invoice</option>
													<option value="Purchase Report">Purchase Report</option>
													<option value="Sales Report">Sales Report</option>
													<option value="Other Transactions">Other Transactions</option>
													<option value="Other Report">Other Report</option>
												</select>
                                            </div>
                                        </div>
                                        <div class="col-md-4"> </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="image">Add Company Details</label>
                                            <textarea id="editor" name="editor" rows="10" cols="80"></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                            </div>
                                        </div>
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
        <!--<script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>-->
        <script src="//cdn.ckeditor.com/4.19.1/full/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('editor');
        </script>
        <script>
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var ctype = document.getElementById("ctype").value;
                var editor = document.getElementById("editor").value;
                var l = true;
                if(ctype.match("select")){
                    alert("Select Display Type");
                    document.getElementById("ctype").focus();
                    l = false;
                }
                else if(editor.length == 0){
                    alert("Enter Company Name");
                    document.getElementById("editor").focus();
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_companydetails.php?ccid'+ccid;
            }

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