<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | Editors</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <section class="content-header">
      <h1>
        Company Profile
        <small>Display for all Invoices &amp; Invoices</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="#">Company Profile</a></li>
        <li class="active">Add</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header">
						<div class="box-body pad">
							<div class="col-md-12">
								<div class="col-md-2"></div>
								<div class="col-md-8">
									<form action="main_savecompanyprofile.php" method="post" enctype="multipart/form-data" name="form_name" id = "form_id" >
										<table class="table">
											<tr>
												<th>Add Logo</th>
												<td><input type="file" name="logo_image" id="image" class="form-control"/></td>
											<tr>
											<tr>
												<th>Choose Display Type</th>
												<td>
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
												</td>
											<tr>
											<tr>
												<th>Add Company Details</th>
												<td><textarea id="editor1" name="editor1" rows="10" cols="80"></textarea></td>
											<tr>
											<tr>
												<td colspan="2" align="center">
													<button type="submit" name="submitcdetails" id="submitcdetails" value="addpage" class="btn btn-flat btn-social btn-linkedin">
														<i class="fa fa-save"></i> Save
													</button>&ensp;&ensp;&ensp;&ensp;
													<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
														<i class="fa fa-trash"></i> Cancel
													</button>
												</td>
											<tr>
										</table>
										
										
										
									</form>
								</div>
								<div class="col-md-2"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </section>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- CK Editor -->
<script src="bower_components/ckeditor/ckeditor.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script>
  $(function () {
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('editor1')
    //bootstrap WYSIHTML5 - text editor
    $('.textarea').wysihtml5()
  $('.select2').select2()
  })
	function redirection_page(){
		window.location.href = "companyprofile.php";
	}
	
	document.getElementById("form_id").onkeypress = function(e) {
    			var key = e.charCode || e.keyCode || 0;     
    				if (key == 13) {
      				//alert("No Enter!");
      				e.preventDefault();
    				}
  			} 
</script>
</body>
</html>
