<?php include "includes/header.php"; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Lihat laporan Pesanan (Harian) <small> Check laporan per hari</small>
			</h1>
			<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
				<li <?php if ($this->uri->segment(3) == 'report_per_day') { echo 'class="active"'; } ?>><a href="<?=base_url()?>administrator/main/report_per_day" ><b>Laporan Pesanan Harian</b></a></li>
				<li><a href="<?=base_url()?>administrator/main/report_per_month" ><b>Laporan Pesanan Bulanan</b></a></li>
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
					<?=form_open('administrator/main/report_per_day_process', array('class' => 'form-inline')) ?>
					<div class="form-group">
						<label>Tanggal</label>
						<input type="text" name="date" class="datepicker form-control" autocomplete="off" data-date-format="yyyy-mm-dd"/>
					</div>
					<div class="form-group">
						<label>Jenis Customer</label>
						<select name="jenis_customer" class="form-control" style="padding:6px;">
							<option value="All">Semua Customer</option>
							<option value="Lokal">Customer Lokal</option>
							<option value="Luar"> Customer Luar</option>
						</select>
					</div>
					<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> LIHAT LAPORAN</button>
					<?=form_close() ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include "includes/footer.php"; ?>

