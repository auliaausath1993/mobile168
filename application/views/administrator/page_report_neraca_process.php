<!DOCTYPE html>
<html>
<head>
	<title>Laporan Neraca</title>
	<link href="<?= base_url('application/views/administrator/assets/css/bootstrap.css') ?>" rel="stylesheet">
	<style>
		body {
			font-size: 12px;
		}
		u {
			font-size: 13px;
		}
		.shop-name {
			margin-bottom: 20px;
			font-weight: bold;
		}
		.month {
			font-weight: bold;
		}
		table {
			margin-top: 20px;
		}
		.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
			border-top: none;
			padding: 2px;
		}

		td:nth-child(2) {
			width: 50px;
		}

		.separator {
			padding: 5px !important;
		}

		td:first-child {
			padding-left: 30px !important;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="text-right" style="margin-top: 10px" id="button">
			<button onclick="printData()" class="btn btn-primary">Print</button>
			<button onclick="exportPdf()" class="btn btn-danger">Eksport PDF</button>
		</div>
	</div>
	<div id="content" class="container" style="background-color: white">
		<div class="shop-name">
		<?= $this->config->item('tokomobile_online_shop') ?>
		</div>
		<div style="border-bottom: 1px solid #000; font-weight: bold; width: 110px;">Laporan Neraca</div>
		<span class="month"><?= 'Bulan ' . $month ?></span>
		<br>
		<table class="table">
			<tr>
				<th colspan="5"><u>1. Informasi Penjualan</u></th>
			</tr>
			<tr>
				<th colspan="5">*Penjualan Berdasarkan Customer Category</th>
			</tr>
			<tr>
				<th></th>
				<th></th>
				<th class="text-center" width="20%">Nominal</th>
				<th class="text-center" width="20%">Qty</th>
				<th class="text-center" width="15%">Persentase</th>
			</tr>
			<?php foreach ($sales as $sale) { ?>
				<tr>
					<td><?= $sale->customer_type ? $sale->customer_type : '-' ?></td>
					<td>:</td>
					<td class="text-right">
						<span class="pull-left">Rp</span>
						<?= number_format($sale->nominal, 0) ?></td>
					<td class="text-center"><?= $sale->total_qty . ' pcs' ?></td>
					<td class="text-center">
						<?= round(($sale->total_qty * 100) / $total_qty_sales) .'%' ?>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td><strong>Omset Penjualan</strong></td>
				<td>:</td>
				<th class="text-right">
					<span class="pull-left">Rp</span>
					<?= number_format($total_nominal_sales, 0) ?>
				</th>
				<th class="text-center"><?= $total_qty_sales . ' pcs' ?></th>
				<td class="text-center">100%</td>
			</tr>
			<tr><th><td class="separator" colspan="5"></td></th></tr>
			<tr>
				<td><strong>HPP</strong></td>
				<td>:</td>
				<th class="text-right">
					<span class="pull-left">Rp</span>
					<?= number_format($modal, 0) ?>
				</th>
				<th></th>
				<td></td>
			</tr>
			<tr>
				<td><strong>Laba Kotor</strong></td>
				<td>:</td>
				<th class="text-right">
					<span class="pull-left">Rp</span>
					<?= number_format($total_nominal_sales - $modal, 0) ?>
				</th>
				<th></th>
				<td></td>
			</tr>
			<tr><th><td class="separator" colspan="5"></td></th></tr>
			<tr>
				<td><strong>Persediaan Awal</strong></td>
				<td>:</td>
				<th class="text-right">
					<span class="pull-left">Rp</span>
					<?= number_format($persediaan_awal, 0) ?>
				</th>
				<th></th>
				<td></td>
			</tr>
			<tr>
				<td><strong>Pembelian</strong></td>
				<td>:</td>
				<th class="text-right">
					<span class="pull-left">Rp</span>
					<?= number_format($purchase['grand_total'], 0) ?>
				</th>
				<th class="text-center">
					<?= $purchase['total_qty'] ? $purchase['total_qty'] : 0 ?> pcs
				</th>
				<td></td>
			</tr>
			<tr>
				<td><strong>Persediaan Akhir</strong></td>
				<td>:</td>
				<th class="text-right">
					<span class="pull-left">Rp</span>
					<?= number_format($stock_end, 0) ?>
				</th>
				<th></th>
				<td></td>
			</tr>
			<tr><th><td class="separator" colspan="5"></td></th></tr>
			<tr>
				<th colspan="5"><u>2. Rincian Pengeluaran</u></th>
			</tr>
			<tr>
				<th colspan="5">*Rincian Biaya Pengeluaran</th>
			</tr>
			<?php $total_biaya = 0;
			foreach ($pengeluaran as $row) { ?>
				<tr>
					<td><?= $row->jenis_biaya ?></td>
					<td>:</td>
					<td class="text-right">
						<span class="pull-left">-Rp</span>
						<?= number_format($row->total_nominal, 0) ?>
					</td>
				</tr>
			<?php $total_biaya += $row->total_nominal;
			} ?>
			<tr>
				<td><strong>TOTAL BIAYA</strong></td>
				<td>:</td>
				<td class="text-right">
					<strong>
						<span class="pull-left">-Rp</span>
						<?= number_format($total_biaya, 0) ?>
					</strong>
				</td>
			</tr>
			<tr><th><td class="separator" colspan="5"></td></th></tr>
			<tr>
				<th colspan="5"><u>3. Rincian Utang</u></th>
			</tr>
			<tr>
				<th colspan="5">*Rincian Biaya Utang</th>
			</tr>
			<tr>
				<td>Saldo Utang Dagang</td>
				<td>:</td>
				<th class="text-right">
					<span class="pull-left">Rp</span>
					<?= number_format($hutang['grand_total'], 0) ?>
				</th>
			</tr>
			<tr>
				<td>Qty</td>
				<td>:</td>
				<th class="text-right">
					<?= $hutang['total_qty'] ? $hutang['total_qty'] : 0 ?> pcs
				</th>
			</tr>
			<tr><th><td class="separator" colspan="5"></td></th></tr>
			<tr>
				<th colspan="5"><u>4. Rincian Piutang</u></th>
			</tr>
			<tr>
				<th colspan="5">*Rincian Piutang</th>
			</tr>
			<tr>
				<td>Saldo Piutang Dagang</td>
				<td>:</td>
				<th class="text-right">
					<span class="pull-left">Rp</span>
					<?= number_format($piutang['nominal'], 0) ?>
				</th>
			</tr>
			<tr>
				<td>Qty</td>
				<td>:</td>
				<th class="text-right">
					<?= $piutang['total_qty'] ? $piutang['total_qty'] : 0 ?> pcs
				</th>
			</tr>
			<tr><th><td class="separator" colspan="5"></td></th></tr>
			<tr>
				<th colspan="3" class="text-right">
					<h5><b>LABA BERSIH<b></h5>
				</th>
				<th class="text-right">
					<h5 class="pull-left" style="display: inline-block; margin-left: 10px;">
						<b>Rp</b>
					</h5>
					<h5 style="display: inline-block;">
						<b><?= number_format($total_nominal_sales - $modal - $total_biaya, 0) ?></b>
					</h5>
				</th>
			</tr>
		</table>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
	<script>
		function printData() {
			window.print();
		}
		window.onbeforeprint = function() {
			document.getElementById('button').style.display = 'none';
		}
		window.onafterprint = function() {
			document.getElementById('button').style.display = 'block';
		}
		function exportPdf() {
			var pdf = new jsPDF('p','pt','a4');
			pdf.addHTML(document.getElementById('content'), function() {
				pdf.save('Laporan Neraca.pdf');
			});
		}
	</script>
</body>
</html>