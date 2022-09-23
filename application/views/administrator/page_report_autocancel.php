<?php include 'includes/header.php' ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Laporan Auto Cancel <small> Mencari Informasi Pesanan Cancel</small>
				</h1>
			</div>
		</div>
		<div class="panel">
			<div react-component="ReportAutocancel" class="panel-body"></div>
		</div>
	</div>
</div>
<script type='text/javascript'>
	$(document).on('click', '#exsport-master-data', function() {
		$('#flex1').tableExport({
			type: 'excel',
			escape:'false',
		});
	});

	$(document).on('click', '#print-master-data', function() {
		var divToPrint = document.getElementById('flex1');
		var htmlToPrint = '' +
		'<link href="<?= base_url('application/views/administrator/assets/css/bootstrap.css') ?>" rel="stylesheet">' +
		'<style type="text/css">' +
		'table th, table td {' +
		'border:1px solid #666;' +
		'padding:5px;' +
		'}' +
		'table th{' +
		'background-color: #999; color: #fff;' +
		'}' +
		'</style>';
		htmlToPrint += divToPrint.outerHTML;
		newWin = window.open("");
		newWin.document.write(htmlToPrint);
		setTimeout(function() {
			newWin.print();
			newWin.close();
		}, 500);
	});

</script>
<?php include 'includes/footer.php' ?>

