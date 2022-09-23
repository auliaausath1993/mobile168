<head>
	<link href="<?=base_url()?>application/views/administrator/assets/css/bootstrap.css" rel="stylesheet">
</head>

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
				<td>
				<?php if ($jenis_laporan == 'category') { ?>
					<?= $product['total'] ?>
				<?php } ?>
				</td>
			</tr>
			<?php if ($jenis_laporan == 'product') {
				foreach ($product['list_variant'] as $variant) { ?>
					<tr>
						<td></td>
						<td><?= '- '.$variant['name'] ?></td>
						<td class="text-center"><?= $variant['qty'] ?></td>
						<td>
							<?= $variant['total'] ?>
						</td>
					</tr>	
				<?php }
				}
			} ?>
			<tr>
				<th colspan="2" class="text-center">TOTAL</th>
				<th class="text-center"><?= $all_qty ?></th>
				<th>
					<?= $all_modal ?>
				</th>
			</tr>
		<?php } ?>
	</tbody>
</table>