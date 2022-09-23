<head>
	<link href="<?=base_url()?>application/views/administrator/assets/css/bootstrap.css" rel="stylesheet">
</head>

<div class="container">
	<h3 align="center"><?=$header['value']?></h3>
	<h4>Laporan <?=date('d-M-Y',strtotime($this_date)) ?></h4>

	<div class="table-responsive">
		<table id="table-laporan" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>No</th>
					<th>No Nota</th>
					<th>Pembeli</th>
					<th>Tanggal Pembayaran</th>
					<th width="10%">Metode Pembayaran</th>
					<th>Total Modal</th>
					<th>Subtotal</th>
					<th>Diskon</th>
					<th>Biaya Pengiriman</th>
					<th>Total Penjualan</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$i = 1;
			foreach ($reports as $report) { ?>
				<tr>
					<td><?=$i ?></td>
					<td>#<?=$report->id?></td>
					<td>
					<?php if ($report->customer_id > 0) {
						echo $report->customer . ' (' . $report->customer_id . ')';
					} else { ?>
						<span style="color: #e23427">
							<?= $report->name_customer ?> - <strong>(Guest)</strong>
						</span>
					<?php } ?>
					</td>
					<td>
						<?=date("D, d-M-Y H:i:s",strtotime($report->date_payment)) ?>
					</td>
					<td><?= $report->payment_method ?: '-' ?></td>
					<td>Rp. <?= numberformat($report->total_modal)?></td>
					<td>Rp. <?= numberformat($report->subtotal)?></td>
					<td>Rp. <?= numberformat($report->diskon)?></td>
					<td>Rp. <?= numberformat($report->shipping_fee)?></td>
					<td>Rp. <?= numberformat($report->total)?></td>
				</tr>
			<?php
			$i++;
			} ?>
			<tr>
				<td colspan="5"><strong>TOTAL</strong></td>
				<td>Rp. <?= numberformat($summary['total_modal']) ?></td>
				<td>Rp. <?= numberformat($summary['subtotal']) ?></td>
				<td>Rp. <?= numberformat($summary['total_diskon']) ?></td>
				<td>Rp. <?= numberformat($summary['total_shipping']) ?></td>
				<td>Rp. <?= numberformat($summary['subtotal'] - $summary['total_diskon'] + $summary['total_shipping']) ?></td>
			</tr>
		</tbody>
		</table>
	</div>
</div>

<script>
	window.print();
	window.onafterprint = function() {
		window.location = "<?=base_url()?>administrator/main/report_pembayaran_perday";
	}
</script>