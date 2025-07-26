<?php
	// Start the session.
	session_start();
	if(!isset($_SESSION['user'])) header('location: login.php');
	
	$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Reports - Inventory Management System</title>
	<?php include('partials/app-header-scripts.php'); ?>
</head>
<body>
	<div id="dashboardMainContainer">
		<?php include('partials/app-sidebar.php') ?>
		<div class="dasboard_content_container" id="dasboard_content_container">
			<?php include('partials/app-topnav.php') ?>
			
			<?php if(in_array('report_view', $user['permissions'])) { ?>
			<div class="dashboard_content">
				<div class="dashboard_content_main">
					<div class="row">
						<div class="column column-12">
							<h1 class="section_header"><i class="fa fa-file-text"></i> Reports</h1>
							<div id="reportsContainer">
								<div class="reportTypeContainer">
								  <div class="reportType">
								  	<p>Export Products</p>
								  	<div class="alignRight">
								  		<a href="database/report_csv.php?report=product" class="reportExportBtn">Excel</a>
								  		<a href="database/report_pdf.php?report=product" target="_blank" class="reportExportBtn">PDF</a>
								  	</div>
								  </div>
								  <div class="reportType">
								  	<p>Export Suppliers</p>
								  	<div class="alignRight">
								  		<a href="database/report_csv.php?report=supplier" class="reportExportBtn">Excel</a>
								  		<a href="database/report_pdf.php?report=supplier" target="_blank" class="reportExportBtn">PDF</a>
								  	</div>
								  </div>
								</div>				
								<div class="reportTypeContainer">
								  <div class="reportType">
								  	<p>Export Deliveries</p>
								  	<div class="alignRight">
								  		<a href="database/report_csv.php?report=delivery" class="reportExportBtn">Excel</a>
								  		<a href="database/report_pdf.php?report=delivery" target="_blank" class="reportExportBtn">PDF</a>
								  	</div>
								  </div>
								  <div class="reportType">
								  	<p>Export Purchase Orders</p>
								  	<div class="alignRight">
								  		<a href="database/report_csv.php?report=purchase_orders" class="reportExportBtn">Excel</a>
								  		<a href="database/report_pdf.php?report=purchase_orders" target="_blank" class="reportExportBtn">PDF</a>
								  	</div>
								  </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } else { ?>
				<div class="dashboard_content">
					<div id="errorMessage"> Access denied.</div>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php include('partials/app-scripts.php'); ?>
</body>
</html>
