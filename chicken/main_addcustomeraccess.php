<?php

//main_addcustomeraccess.php

session_start();

include "newConfig.php";

include "header_head.php";

$dbname = $_SESSION['dbase'];

$emp_code = $_SESSION['cusacc'];

$sql = "SELECT * FROM `common_customeraccess` WHERE `db_name` = '$dbname' ORDER BY `ccode` ASC";
$query = mysqli_query($conns, $sql);
$ccount = mysqli_num_rows($query);

$ucode = "";

if ($ccount > 0) {

	while ($row = mysqli_fetch_assoc($query)) {

		if ($ucode == "") {

			$ucode = $row['ccode'];
		} else {

			$ucode = $ucode . "','" . $row['ccode'];
		}
	}
}

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `code` NOT IN ('$ucode') AND `active` = '1' ORDER BY `name` ASC";
$query = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($query)) {

	$cus_code[$row['code']] = $row['code'];

	$cus_name[$row['code']] = $row['name'];

	$cus_mobl[$row['code']] = $row['mobileno'];

	$cus_ctype[$row['code']] = $row['contacttype'];
}



?>

<html>

<body class="hold-transition skin-blue sidebar-mini">

	<section class="content-header">

		<h1>Select all required fields</h1>

		<ol class="breadcrumb">

			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

			<li><a href="#">Profile</a></li>

			<li class="active">Customer-Access</li>

			<li class="active">Add</li>

		</ol>

	</section>

	<section class="content">

		<div class="box box-default">

			<div class="box-body">

				<div class="row">

					<div class="col-md-12">

						<form action="main_updatedcustomeraccess.php" method="post" onsubmit="return checkval()" name="form_name" id="form_id">

							<div class="col-md-12">

								<div class="form-group col-md-1"></div>

								<div class="form-group col-md-10">

									<table class="table">

										<thead>

											<th style="text-align:center;">Sl.No</th>

											<th style="text-align:center;">Customer Name</th>

											<th style="text-align:center;">Mobile</th>

											<th style="text-align:center;">Type</th>

											<th style="text-align:center;">Accesses All<br />

												<input type="checkbox" name="slsodr" id="slsodr" value="slsodr" onclick="checkall(this.id)" />Sales Order&ensp;

												<input type="checkbox" name="salesall" id="salesall" value="sales" onclick="checkall(this.id)" />Sales&ensp;

												<input type="checkbox" name="receiptsall" id="receiptsall" value="receipts" onclick="checkall(this.id)" />Receipt&ensp;

												<input type="checkbox" name="sorder" id="sorder" value="sorder" onclick="checkall(this.id)" />SO&ensp;

												<input type="checkbox" name="ledgerall" id="ledgerall" value="ledger" onclick="checkall(this.id)" />Ledger&ensp;

												<input type="checkbox" name="ledgerall_new" id="ledgerall_new" value="ledger_new" onclick="checkall(this.id)" />Ledger New

											</th>

											<th style="text-align:center;">Active Status<br /><input type="checkbox" name="activeall" id="activeall" value="active" onclick="checkall(this.id)" /></th>

										</thead>

										<tbody>

											<?php

											$c = 0;

											foreach ($cus_code as $ccode) {
												$c = $c + 1;

											?>

												<tr>

													<td style="text-align:center;"><?php echo $c; ?></td>

													<td><?php echo $cus_name[$ccode]; ?></td>

													<td style="text-align:center;"><input type="text" name="<?php echo $ccode; ?>" id="<?php echo $ccode; ?>" class="form-control" style="height: 25px;width:150px;" value="<?php echo $cus_mobl[$ccode]; ?>" /></td>

													<td style="text-align:center;"><?php echo $cus_ctype[$ccode]; ?></td>

													<td style="text-align:center;">

														<input type="checkbox" name="slsodr<?php echo $ccode; ?>" id="slsodr[<?php echo $c; ?>]" />Sales Order&ensp;

														<input type="checkbox" name="sales<?php echo $ccode; ?>" id="sales[<?php echo $c; ?>]" />Sales&ensp;

														<input type="checkbox" name="receipts<?php echo $ccode; ?>" id="receipts[<?php echo $c; ?>]" />Receipt&ensp;

														<input type="checkbox" name="sorder<?php echo $ccode; ?>" id="sorder[<?php echo $c; ?>]" />SO&ensp;

														<input type="checkbox" name="ledger<?php echo $ccode; ?>" id="ledger[<?php echo $c; ?>]" />Ledger&ensp;

														<input type="checkbox" name="ledger_new<?php echo $ccode; ?>" id="ledger_new[<?php echo $c; ?>]" />Ledger New
													</td>

													<td style="text-align:center;"><input type="checkbox" name="active[]" id="active[<?php echo $c; ?>]" value="<?php echo $ccode; ?>" /></td>

												</tr>

											<?php

											}

											?>

										</tbody>

									</table>

								</div>

							</div>

							<div class="box-body" align="center">

								<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">

									<i class="fa fa-save"></i> Save

								</button>&ensp;&ensp;&ensp;&ensp;

								<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">

									<i class="fa fa-trash"></i> Cancel

								</button>

							</div>

						</form>

					</div>

				</div>

			</div>

		</div>

	</section>

	<script>
		function redirection_page() {

			window.location.href = "main_displaycustomeraccess.php";

		}

		function validatename(x) {

			expr = /^[a-zA-Z0-9 (.&)_-]*$/;

			var a = document.getElementById(x).value;

			if (a.length > 50) {

				a = a.substr(0, a.length - 1);

			}

			if (!a.match(expr)) {

				a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');

			}

			document.getElementById(x).value = a;

		}

		function checkall(a) {

			
			var b = '<?php echo $c; ?>';

			var c = document.getElementById(a).value;



			var selectallbox = document.getElementById(a);

			if (a.match("sales")) {

				if (selectallbox.checked == true) {

					for (var i = 1; i <= b; i++) {

						if (i <= 100) {

							document.getElementById("sales[" + i + "]").checked = true;

						}

					}

				} else {

					for (var i = 1; i <= b; i++) {

						document.getElementById("sales[" + i + "]").checked = false;

					}

				}

			} else if (a.match("receipts")) {

				if (selectallbox.checked == true) {

					for (var i = 1; i <= b; i++) {

						if (i <= 100) {

							document.getElementById("receipts[" + i + "]").checked = true;

						}

					}

				} else {

					for (var i = 1; i <= b; i++) {

						document.getElementById("receipts[" + i + "]").checked = false;

					}

				}

			} else if (a.match("ledgerall_new")) {

				if (selectallbox.checked == true) {

					for (var i = 1; i <= b; i++) {

						if (i <= 100) {

							document.getElementById("ledger_new[" + i + "]").checked = true;

						}

					}

				} else {

					for (var i = 1; i <= b; i++) {

						document.getElementById("ledger_new[" + i + "]").checked = false;

					}

				}

			} else if (a.match("ledger")) {

				if (selectallbox.checked == true) {

					for (var i = 1; i <= b; i++) {

						if (i <= 100) {

							document.getElementById("ledger[" + i + "]").checked = true;

						}

					}

				} else {

					for (var i = 1; i <= b; i++) {

						document.getElementById("ledger[" + i + "]").checked = false;

					}

				}

			} else if (a.match("slsodr")) {

				if (selectallbox.checked == true) {

					for (var i = 1; i <= b; i++) {

						if (i <= 100) {

							document.getElementById("slsodr[" + i + "]").checked = true;

						}

					}

				} else {

					for (var i = 1; i <= b; i++) {

						document.getElementById("slsodr[" + i + "]").checked = false;

					}

				}

			} else if (a.match("sorder")) {

				if (selectallbox.checked == true) {

					for (var i = 1; i <= b; i++) {

						if (i <= 100) {

							document.getElementById("sorder[" + i + "]").checked = true;

						}

					}

				} else {

					for (var i = 1; i <= b; i++) {

						document.getElementById("sorder[" + i + "]").checked = false;

					}

				}

			} else if (a.match("active")) {

				if (selectallbox.checked == true) {

					for (var i = 1; i <= b; i++) {

						if (i <= 100) {

							document.getElementById("active[" + i + "]").checked = true;

						}

					}

				} else {

					for (var i = 1; i <= b; i++) {

						document.getElementById("active[" + i + "]").checked = false;

					}

				}

			} else {



			}

		}

		function checkval() {

			var a = '<?php echo $c; ?>';

			var b = c = d = e = f = "";

			var l = true;

			var checkboxes = document.querySelectorAll('input[name="active[]"]:checked');

			if (checkboxes.length == 0) {

				alert("Please select atleast one Customer to Activate");

				l = false;

			} else {

				for (var x = 1; x <= a; x++) {

					if (l == true) {

						b = document.getElementById("active[" + x + "]");

						c = document.getElementById("sales[" + x + "]");

						d = document.getElementById("receipts[" + x + "]");

						e = document.getElementById("sorder[" + x + "]");

						f = document.getElementById("ledger[" + x + "]");

						g = document.getElementById("slsodr[" + x + "]");

						h = document.getElementById("ledger_new[" + x + "]");

						if (b.checked == true) {

							if (c.checked == false && d.checked == false && e.checked == false && f.checked == false && g.checked == false && h.checked == false) {

								alert("Please select atleast one transaction to provide access in row: " + x);

								l = false;

							} else {

								l = true;

							}

						}

					} else {

						l = false;

					}

				}

			}

			if (l == true) {

				return true;

			} else {

				return false;

			}

		}



		document.getElementById("form_id").onkeypress = function(e) {

			var key = e.charCode || e.keyCode || 0;

			if (key == 13) {

				//alert("No Enter!");

				e.preventDefault();

			}

		}
	</script>

	<?php include "header_foot.php"; ?>

</body>

</html>