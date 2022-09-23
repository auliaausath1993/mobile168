<?php include 'includes/header.php' ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Laporan Pengeluaran
				</h1>
				<?= validation_errors('<div class="alert alert-danger">', '</div>') ?>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<form method="post" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-1" style="margin-top: 10px">Pilih</label>
						<div class="col-sm-1">
							<label class="radio-inline">
								<input type="radio" name="date_type" class="date_type" value="date" checked>
								Tanggal
							</label>
						</div>
						<div class="col-sm-10 form-inline">
							<input type="text" name="dateFrom" autocomplete="off" class="datepicker form-control dateFrom" data-date-format="yyyy-mm-dd" placeholder="Pilih Tanggal" required> <b>&ndash;</b> <input type="text" name="dateTo" autocomplete="off" class="datepicker form-control dateTo" data-date-format="yyyy-mm-dd" placeholder="Pilih Tanggal" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-1"></label>
						<div class="col-sm-1">
							<div class="radio">
								<label >
									<input type="radio" name="date_type" class="date_type" value="month">
									Bulan
								</label>
							</div>
						</div>
						<div class="col-sm-10 form-inline">
							<input type="text" name="month" disabled autocomplete="off" class="datepicker form-control month" data-date-format="yyyy-mm" data-date-viewmode="years" data-date-minviewmode="months" placeholder="Pilih Bulan">
						</div>
					</div>
					<div class="col-sm-12" style="margin-top: 20px; margin-bottom: 20px">
						<button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-search"></i> CARI DATA</button>
						<button type="reset" class="btn btn-danger">RESET</button>
					</div>
				</form>
				<div class="container col-sm-12" style="margin-bottom: 10px">
					<div class="row">
						<div class="col-md-9 text-center">
							<?php if ($date_format) { ?>
								<h3 style="margin-top: 0">Laporan Pengeluaran</h3>
								<h4>Per <?= $date_type == 'month' ? 'Bulan' : 'Tanggal' ?></h4>
								<h4><?= $date_format ?></h4>
							<?php } ?>
						</div>
						<div class="text-right col-md-3">
							<form method="post" style="display: inline-block;" target="_blank" action="<?= base_url('administrator/main/export_report_pengeluaran') ?>">
								<input type="hidden" name="date_type" value="<?= set_value('date_type') ?>">
								<input type="hidden" name="dateFrom" value="<?= set_value('dateFrom') ?>">
								<input type="hidden" name="dateTo" value="<?= set_value('dateTo') ?>">
								<input type="hidden" name="month" value="<?= set_value('month') ?>">
								<button type="submit" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export PDF</button>
							</form>
							<form method="post" style="display: inline-block;" target="_blank" action="<?= base_url('administrator/main/print_report_pengeluaran') ?>">
								<input type="hidden" name="date_type" value="<?= set_value('date_type') ?>">
								<input type="hidden" name="dateFrom" value="<?= set_value('dateFrom') ?>">
								<input type="hidden" name="dateTo" value="<?= set_value('dateTo') ?>">
								<input type="hidden" name="month" value="<?= set_value('month') ?>">
								<button type="submit" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
							</form>
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th class="text-center">No.</th>
									<th class="text-center">Tanggal</th>
									<th class="text-center">Jenis Biaya</th>
									<th class="text-center">Catatan</th>
									<th class="text-center">Nominal</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$total = 0;
								if (!empty($data_pengeluaran)) {
									foreach ($data_pengeluaran as $item) { ?>
										<tr>
											<td class="text-center"><?= $i++ ?></td>
											<td class="text-center"><?= $item->tanggal ?></td>
											<td><?= $item->jenis_biaya ?></td>
											<td><?= $item->catatan ?></td>
											<td><?= 'Rp. ' . number_format($item->nominal, 0, '.', '.') ?></td>
										</tr>
									<?php $total += $item->nominal;
									}
								} else { ?>
									<tr>
										<td colSpan="5" class="text-center">
											Laporan Pengeluaran Kosong
										</td>
									</tr>
								<?php } ?>
							</tbody>
							<?php if ($total > 0) { ?>
								<tfoot>
									<tr>
										<th colspan="4" class="text-right">TOTAL</th>
										<th><?= 'Rp. ' . number_format($total, 0, '.', '.') ?></th>
									</tr>
								</tfoot>
							<?php } ?>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'includes/footer.php' ?>
<script type="text/javascript">
	$('.date_type').click(function() {
		var value = $(this).val();
		if (value == 'date') {
			$('.month').removeAttr('required');
			$('.month').attr('disabled', 'disabled');
			$('.dateFrom').attr('required', 'required');
			$('.dateTo').attr('required', 'required');
			$('.dateFrom').removeAttr('disabled');
			$('.dateTo').removeAttr('disabled');
		} else {
			$('.month').attr('required', 'required');
			$('.month').removeAttr('disabled');
			$('.dateFrom').attr('disabled', 'disabled');
			$('.dateTo').attr('disabled', 'disabled');
			$('.dateFrom').removeAttr('required');
			$('.dateTo').removeAttr('required');
		}
	});
</script>