<?php include 'includes/header.php'; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Lihat laporan Pembayaran (Harian) <small> Check laporan per hari</small>
			</h1>
			<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
				<li <?php if ($this->uri->segment(3) == 'report_pembayaran_perday') { echo 'class="active"'; } ?>><a href="<?=base_url()?>administrator/main/report_pembayaran_perday" ><b>Laporan Pembayaran Harian</b></a></li>
				<li><a href="<?=base_url()?>administrator/main/report_pembayaran_permonth" ><b>Laporan Pembayaran Bulanan</b></a></li>
			</ul>
			<ol class="breadcrumb">
				<li class="active">
					<i class="fa fa-list"></i> Laporan harian
				</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel">
				<div class="panel-body">
					<?=form_open('administrator/main/report_pembayaran_perday_process', array('class' => 'form-inline')) ?>
					<div class="form-group">
						<label>Tanggal</label>
						<input autocomplete="off" type="text" name="date" class="datepicker form-control" data-date-format="yyyy-mm-dd"/>
					</div>
					<div class="form-group">
						<label>Jenis Customer</label>
						<select name="jenis_customer" class="form-control" style="padding:6px;">
							<option value="All">Semua Customer</option>
							<?php foreach ($customer_types as $type) { ?>
								<option value="<?= $type->id ?>"><?= $type->name ?></option>
							<?php } ?>
						</select>
					</div>
					<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> LIHAT LAPORAN</button>
					<?=form_close() ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'includes/footer.php'; ?>

