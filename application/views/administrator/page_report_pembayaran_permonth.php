<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Lihat laporan Pembayaran (Bulanan) <small> Check laporan per bulan</small>
				</h1>
				<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
					<li><a href="<?=base_url()?>administrator/main/report_pembayaran_perday" ><b>Laporan Pembayaran Harian</b></a></li>
					<li <?php if ($this->uri->segment(3) == 'report_pembayaran_permonth') { echo 'class="active"'; } ?>><a href="<?=base_url()?>administrator/main/report_pembayaran_permonth" ><b>Laporan Pembayaran Bulanan</b></a></li>
				</ul>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-list"></i> Laporan Bulanan
					</li>
				</ol>
			</div>
		</div>
		<div class="panel">
			<div react-component="ReportPaymentMonth" class="panel-body"></div>
		</div>
	</div>
</div>
<?php include 'includes/footer.php'; ?>

<script>
	$(document).on('click', '.list-modal', function() {
		$('#modal-detail-report').modal('show');
	});
</script>