<?php include 'includes/header.php' ?>
<style>
	.sort  {
		cursor: pointer;
	}
	.asc:after {
		font-family: 'FontAwesome';
		content: "\f0d8";
		float: right;
		color: grey;
	}
	.desc:after {
		font-family: 'FontAwesome';
		content: "\f0d7";
		float: right;
		color: grey;
	}

	.bg-success.desc:after, .bg-success.asc:after, .bg-danger.desc:after, .bg-danger.asc:after {
		color: #fff;
	}

	.bg-success {
		background-color: #5cb85c !important;
	}

	.bg-danger {
		background-color: #ff5f5f !important;
	}
</style>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Laporan Autocancel
				</h1>
			</div>
		</div>
		<div class="panel">
			<div react-component="LaporanAutocancel" class="panel-body"></div>
		</div>
	</div>
</div>
<?php include 'includes/footer.php' ?>