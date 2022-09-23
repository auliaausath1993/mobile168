<?php include 'includes/header.php' ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Laporan Neraca
				</h1>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<?=form_open('administrator/main/report_neraca_process', array('target' => 'blank', 'class' => 'form-inline')) ?>
						Bulan : <input type="text" name="month" class="datepicker form-control" data-date-format="yyyy-mm" data-date-viewmode="years" data-date-minviewmode="months" readonly autocomplete="off" required>
						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> LIHAT LAPORAN</button>
						<?= form_close() ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'includes/footer.php' ?>
