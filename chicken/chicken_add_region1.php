<?php
    //chicken_add_region1.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['region1'];
	$dbname = $_SESSION['dbase'];
    
?>
<html>
	<head>
		<style>
			.select2-container .select2-selection--single{ box-sizing:border-box; cursor:pointer; display:block; height:23px; user-select:none; -webkit-user-select:none; }
			.select2-container--default .select2-selection--single{background-color:#fff;border:1px solid #aaa;border-radius:4px}
			.select2-container--default .select2-selection--single .select2-selection__rendered{color:#444;line-height:18px}
			.select2-container--default .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold}
			.select2-container--default .select2-selection--single .select2-selection__placeholder{color:#999}
			.select2-container--default .select2-selection--single .select2-selection__arrow{height:23px;position:absolute;top:1px;right:1px;width:20px}
			.select2-container--default .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}
			.form-control { width: 85%; height: 23px; }
			label { line-height: 20px; }
			.disabledbutton{ pointer-events: none; opacity: 0.4; }
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
				<li><a href="#">Item</a></li>
				<li class="active">Region</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="col-md-12">
						<form action="chicken_save_region1.php" method="post" onsubmit="return checkval()">
							
							<div class="row">
								<div class="col-md-12" align="center">
									<table class="table1" style="line-height:1.5;width:auto;">
										<thead>
											<tr>
												<th style="text-align:center;"><label>Region Name<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>+/-</label></th>
											</tr>
										</thead>
										<tbody id="row_body">
										<tr style="margin-top: 10px;">
											<td><input type="text" name="description[]" id="description[0]" class="form-control" style="width:150px; margin-top: 10px;" onkeyup="validatename(this.id)" /></td>
											<td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px; border:none; margin-top: 10px;"><i class="fa fa-plus" style="color:green;"></i></a></td>
										</tr>
										</tbody>
									</table>
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
                                    <button type="submit" name="submit" id="submit" class="btn btn-flat btn-social btn-linkedin"><i class="fa fa-save"></i>Submit</button>&ensp;
                                    <button type="button" name="cancel" id="cancel" class="btn btn-flat btn-social btn-google" onclick="return_back()"><i class="fa fa-trash"></i>Cancel</button>
                                </div>
                            </div>
						</form>
					</div>
				</div>
			</div>
		</section>
		<script>
			function return_back(){
				var a = '<?php echo $cid; ?>';
				window.location.href = "chicken_display_region1.php?cid="+a;
			}
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
				var incr = document.getElementById("incr").value;
                var l = true;
                var c = 0; var description = "";
                
				for(var d = 0;d <= incr;d++){
                    if(l == true){
                        c = d + 1;
                        description = document.getElementById("description["+d+"]").value;
                        if(description == ""){
                            alert("Please enter Region Name in row: "+c);
                            document.getElementById("description["+d+"]").focus();
                            l = false;
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
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="description[]" id="description['+d+']" class="form-control" style="width:150px;" onkeyup="validatename(this.id)" /></td>';
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
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
		</script>
	</body>
</html>
<?php include "header_foot.php"; ?>