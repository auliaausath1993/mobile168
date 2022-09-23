<?php include 'includes/header.php'; ?>
<style>
	.autocomplete-suggestion:hover {
		background: #F0F0F0;
	}
	.accumulation {
		margin-top: 10px;
	}

	.accumulation .col-md-6 {
		margin-bottom: 10px;
	}
</style>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Buat Pesanan Toko</h1>
				<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
					<!-- <li class="active"><a href="<?=base_url()?>administrator/main/create_order" ><b>Pesanan Pelanggan</b></a></li> -->
					<?php if ($order_non_pelanggan == 'on') { ?>
						<li><a href="<?=base_url()?>administrator/main/create_order_tamu" ><b>Pesanan Non Pelanggan</b></a></li>
					<?php } ?>
				</ul>
			</div>
			<?=validation_errors()?>
		</div>
		<?= $this->session->flashdata('message') ?>
		<form name="form1" id="form1" method="post" action="create_order_process">
			<div react-component="CreateOrder"></div>
			<hr>
			<p><b>Status Pesanan</b></p>
			<p>Pilih salah satu</p>
			<div class="well">
				<div class="row">
					<div class="col-md-6">
						<label class="radio-inline">
							<input type="radio" name="status_pesanan" id="inlineRadio1" class="check_status_pesanan" value="Keep" checked>
							<strong>Pesanan Dalam Proses (Keep)</strong><br>
							<small>Pesanan barang tersimpan di List Pesanan Dalam Proses</small>
						</label>
					</div>
					<div class="col-md-6">
						<label class="radio-inline">
							<input type="radio" name="status_pesanan" id="inlineRadio2" class="check_status_pesanan" value="Dropship_Unpaid">
							<strong>Pesanan Dropship (Belum Lunas)</strong><br>
							<small>Pesanan telah memiliki Nota / ID Pesanan, dengan metode Pengiriman Alamat, namun Belum Lunas</small>
						</label>
					</div>
					<div class="col-md-6">
						<label class="radio-inline">
							<input type="radio" name="status_pesanan" id="inlineRadio3" class="check_status_pesanan" value="Keep_Paid">
							<strong>Pesanan Bayar Ditempat (Lunas)</strong><br>
							<small>Pesanan telah memiliki Nota / ID Pesanan dan Lunas</small>
						</label>
					</div>
					<div class="col-md-6">
						<label class="radio-inline">
							<input type="radio" name="status_pesanan" id="inlineRadio4" class="check_status_pesanan" value="Dropship_Paid">
							<strong>Pesanan Dropship (Lunas)</strong><br>
							<small>Pesanan telah memiliki Nota / ID Pesanan, dengan metode Pengiriman Alamat, dan telah Lunas</small>
						</label>
					</div>
					<div class="col-md-6">
						<label class="radio-inline">
							<input type="radio" name="status_pesanan" id="inlineRadio5" class="check_status_pesanan" value="Rekap_Unpaid">
							<strong>Pesanan Belum Lunas (Rekap / COD)</strong><br>
							<small>Pesanan belum lunas status Rekap / COD</small>
						</label>
					</div>
					<div class="col-md-6">
						<label class="radio-inline">
							<input type="radio" name="status_pesanan" id="inlineRadio6" class="check_status_pesanan" value="Piutang_Unpaid">
							<strong>Pesanan Piutang (Belum Lunas)</strong><br>
							<small>Pesanan telah memiliki Nota / ID Pesanan dan Belum Lunas dan akan masuk kedalam Laporan Piutang</small>
						</label>
					</div>
					<div class="col-md-6">
						<label class="radio-inline">
							<input type="radio" name="status_pesanan" id="inlineRadio7" class="check_status_pesanan" value="Cash_Paid">
							<strong>Pesanan Bayar Cash (Lunas)</strong><br>
							<small>Pesanan telah memiliki Nota / ID Pesanan dan Lunas</small>
						</label>
					</div>
				</div>
			</div>
			<button type="submit" class="btn btn-success" name="go">Buat Pesanan</button>
			<hr>
		</form>
	</div>
</div>
<?php include 'includes/footer.php'; ?>

