<?php
include 'includes/header.php';
$jenis_laporan = $this->session->userdata('jenis_laporan'); ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Laporan Assets <small> Mencari Informasi Assets</small>
				</h1>
			</div>
		</div>
		<form method="post" action="<?=base_url()?>administrator/main/report_assets">
			<div class="form-group">
				<label class="col-sm-2">Jenis Laporan</label>
				<div class="col-sm-10">
					<select class="form-control" name="jenis_laporan" id="jenis_laporan">
						<option value="">- Pilih Jenis Laporan -</option>
						<option value="product" <?= $jenis_laporan == 'product' ? 'selected' : '' ?>>By Product</option>
						<option value="category" <?= $jenis_laporan == 'category' ? 'selected' : '' ?>>By Category</option>
					</select><br>
				</div>
			</div>

			<input type="hidden" name="cari" value="<?= "cari";?>" >

			<div class="form-group button-cari">
				<div class="col-sm-12">
					<button class="btn btn-primary"  style="margin-bottom:10px;"><i class="fa fa-fw fa-search"> </i>CARI DATA </button>
					<a href="<?=base_url() ?>administrator/main/reset_report_assets" class="btn btn-danger" style="margin-bottom:10px;">RESET</a>
				</div>
			</div>

		</form>

		<div class="container col-sm-12" style="margin-bottom: 10px;">
			<h4 class="page-header">
				Filter Search <small></small>
			</h4>
			<?= $this->session->flashdata('message') ?>
			<div class="text-right" style="margin-bottom: 20px;">
				<form action="<?= base_url('administrator/main/report_asset_export') ?>" method="POST" id="form-export" target="_blank">
					<input type="hidden" name="jenis_laporan" value="<?= $jenis_laporan ?>">
				</form>
				<a id="exsport-master-data" class="btn btn-success" href="#"><i class="fa fa-file-excel-o"></i> Export Excel</a>
				<a id="print-master-data" class="btn btn-primary" href="#"><i class="fa fa-print"></i> Cetak</a>
			</div>
			<div class="panel">
				<div class="panel-body">
					<div class="table-responsive">
						<table id="flex1" class="table table-bordered">
							<thead>
								<tr class="btn-info">
									<th class="text-center">No</th>
									<th class="text-center">Product <?= $jenis_laporan == 'category' ? 'Category' : '' ?></th>
									<th class="text-center">Jumlah Qty</th>
									<th class="text-center">Asset / Nominal Modal</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if (!empty($products)) {
									$i = 1; foreach ($products as $product) { ?>
									<tr>
										<td class="text-center"><?= $i++ ?></td>
										<td><?= $product['name'] ?></td>
										<td class="text-center"><?= $jenis_laporan == 'category' ? $product['qty'] : '' ?></td>
										<td class="row">
										<?php if ($jenis_laporan == 'category') { ?>
											<div class="col-md-6">Rp. </div>
											<div class="col-md-6 text-right">
												<?= number_format($product['total'], 0, '.', ',') ?>
											</div>
										<?php } ?>
										</td>
									</tr>
									<?php if ($jenis_laporan == 'product') {
										foreach ($product['list_variant'] as $variant) { ?>
											<tr>
												<td></td>
												<td><?= '- '.$variant['name'] ?></td>
												<td class="text-center"><?= $variant['qty'] ?></td>
												<td class="row">
													<div class="col-md-6">Rp. </div>
													<div class="col-md-6 text-right">
														<?= number_format($variant['total'], 0, '.', ',') ?>
													</div>
												</td>
											</tr>
										<?php }
										}
									} ?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" class="text-center">TOTAL</th>
										<th class="text-center"><?= $all_qty ?></th>
										<th class="row">
											<div class="col-md-6">Rp. </div>
											<div class="col-md-6 text-right">
												<?= number_format($all_modal, 0, '.', ',') ?>
											</div>
										</th>
									</tr>
								<?php } ?>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<br>
		<br>
	</div>
</div>
<!-- AUTOCOMPLETE -->
<script type='text/javascript'>
	var site = '<?= site_url();?>';

	$('#exsport-master-data' ).click(function() {
		// $('#flex1').tableExport({type:'excel',tableName:'download_table', escape:'false'});
		$('#form-export').submit();
	});

	function printData() {
		var divToPrint = document.getElementById('flex1');
		var htmlToPrint = '' +
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
		newWin.print();
		newWin.close();
	}

	$('#print-master-data').on('click',function(){
		printData();
	})

</script>
<?php include 'includes/footer.php' ?>
<script src="<?=  base_url('application/views/administrator/assets/select2/dist/js/select2.min.js') ?>"></script>
<script src="<?=  base_url('application/views/administrator/assets/select2/dist/js/i18n/id.js') ?>"></script>