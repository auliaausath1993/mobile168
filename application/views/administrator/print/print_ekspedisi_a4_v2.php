<?php

$data_info_kontak = $this->main_model->get_detail('content',array('name' => 'nota'));
$data_footer = $this->main_model->get_detail('content',array('name' => 'footer'));
$data_logo = $this->main_model->get_detail('content',array('name' => 'image_nota'));
$aktif_logo = $this->main_model->get_detail('content',array('name' => 'aktif_logo'));
$pay_id = $data_orders['payment_method_id'];

$payment_methode = $this->main_model->get_detail('payment_method',array('id' =>$pay_id));
?>

<!DOCTYPE html>

<html>

<head>

	<title>Struk Belanja</title>

	<link href="<?=base_url()?>application/views/administrator/assets/css/bootstrap.css" rel="stylesheet">


	<style>
	body {
		font-size: 10px;
		padding-right: 30px;
		padding-left: 5px;
	}
	p {
		margin: 0;
	}
	.header-nota {
		font-weight: bold;
		text-transform: uppercase;
		font-size: 12px;
		border-bottom: 1px solid #000;
		margin-bottom: 16px;
	}
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		border: none;
		vertical-align: middle;
	}

	.table {
		margin-bottom: 0;
	}

	.dash {
		border-top: 1px dashed #000;
	}

	.items {
		padding: 8px;
	}

	.item-title {
		font-size: 10px;
		margin-top: 16px;
	}
	.courier {
		text-transform: uppercase;
	}

	.shipping-info {
		border-top: 1px solid #000;
		position: relative;
		padding: 8px;
	}

	.shipping-info table {
		/*transform: rotate(90deg);
		position: absolute;
		top: 75px;
		left: -25%;*/
	}

	.shipping-info table tr:last-child {
		border-top: 1px solid #000;
		padding: 5px;
	}

	.shipping-info table tr td, .shipping-info table tr th {
		/*white-space: nowrap;*/
		/*width: 30px;*/
		padding: 2px;
		padding-left: 5px;
	}
	h5 {
		font-weight: bold;
		font-size: 13px;
		margin: 0;
	}
</style>
</head>
<body>
	<div id="wrapper">
		<div class="header-nota" style="text-align: center;">
			<?php if ($aktif_logo['value'] == 1) { ?>
				<?php if ($data_logo['value'] == null) { ?>
					<?=$data_info_kontak['value'] ?>
				<?php }else{ ?>
					<div class='content-top'>
						<div class="img">
							<img src="<?= base_url() ?>media/images/<?= $data_logo['value'] ?>" alt="<?= base_url() ?>" />
						</div>
						<div class="img">
							<?=$data_info_kontak['value'] ?>
						</div>
					</div>
				<?php } ?>
			<?php }else{ ?>
				<?=$data_info_kontak['value'] ?>
			<?php } ?>
		</div>
		<div class="row">
			<div class="col-xs-6 text-center">
				<?php if ($show_id_print_nota == 'on') { ?>
					<p>ID Pesanan</p>
					<p><strong><?= $data_orders['id'] ?></strong></p>
				<?php } ?>
				<p>
					<?php if ($data_orders['customer_id'] == 0) { ?>
						<?=$data_orders['name_customer'] ?> (Guest)
					<?php } else { ?>
						<?= $customer['name'] ?> (<?=$customer['id'] ?>)
					<?php } ?>
				</p>
			</div>
			<div class="col-xs-6 text-center">
				<p><?= date('d-m-Y') ?></p>
				<p><?= date('H:i:s') ?></p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 text-center">
				<p class="item-title">Data Item Pengiriman</p>
			</div>
		</div>
		<div class="items" id="items">
			<table class="table">
				<tr>
					<th class="text-center">ITEM</th>
					<th class="text-center">Qty</th>
					<th class="text-right">Harga</th>
				</tr>
				<?php
				$i = 1;
				$total_qty = 0;
				$nota_variant = array();
				$nota_variant_id = array();
				$nota_notes = array();

				foreach ($orders_item->result() as $orders_item) {
					if (in_array($orders_item->variant_id, $nota_variant_id) && !$orders_item->notes) {
						$nota_variant[$orders_item->variant_id]['qty'] = $nota_variant[$orders_item->variant_id]['qty'] + $orders_item->qty;
						$nota_variant[$orders_item->variant_id]['subtotal'] = $nota_variant[$orders_item->variant_id]['subtotal'] + $orders_item->subtotal;

					} else {
						if ($orders_item->notes) {
							$index = $orders_item->id . 'NOTES';
						} else {
							$index = $orders_item->variant_id;
						}
						$nota_variant_id[] = $index;
						$nota_variant[$index] = array(
							'variant_id'  => $orders_item->variant_id,
							'customer_id' => $orders_item->customer_id,
							'prod_id'     => $orders_item->prod_id,
							'qty'         => $orders_item->qty,
							'subtotal'    => $orders_item->subtotal,
							'notes'       => $orders_item->notes
						);
					}
				}

				foreach ($nota_variant as $key => $value) {
					$data_customer = $this->main_model->get_detail('customer',array('id' => $value ['customer_id']));
					$data_product = $this->main_model->get_detail('product',array('id' => $value['prod_id']));
					$data_variant = $this->main_model->get_detail('product_variant',array('id' => $value['variant_id']));
					$total_qty = $total_qty + $value['qty']; ?>
					<tr>
						<td>
							<?=$data_product['name_item'] ?>
							<br><?=$data_variant['variant']?>
							<?php if ($value['notes'] != '') { ?>
								<br>Catatan : <?= $value['notes'] ?>
							<?php } ?>
						</td>
						<td class="text-center"><?= $value['qty'] ?></td>
						<td class="text-right"><?= number_format($value['subtotal'], 0, '.', '.') ?></td>
					</tr>
				<?php
				$i++; } ?>
			</table>
			<div class="dash"></div>
			<table class="table">
				<tr>
					<td>Total Belanja</td>
					<td class="text-right">
						<?= number_format($data_orders['subtotal'], 0, '.', '.') ?>
					</td>
				</tr>
				<tr>
					<td>
						Ongkos Pengiriman
						<div class="courier">
							(<?= $data_orders['ekspedisi'] . ' - ' . $data_orders['tarif_tipe'] ?>)
						</div>
					</td>
					<td class="text-right">
						<?=number_format($data_orders['shipping_fee'], 0, '.', '.') ?>
					</td>
				</tr>
				<tr>
					<td>Total Harga</td>
					<td class="text-right">
						<?=number_format( $data_orders['total'], 0, '.', '.' ) ?>
					</td>
				</tr>
			</table>
			<div class="clear"></div>
		</div>
		<?php if ($data_orders['order_status'] == 'Dropship' && $shipping['value'] == 1) { ?>
		<div class="shipping-info">
			<table class="table">
				<?php if ($show_id_print_nota == 'on') { ?>
				<tr>
					<th>ID Pesanan</th>
					<th>:</th>
					<td><?= $data_orders['id'] ?></th>
				</tr>
				<?php } ?>
				<tr>
					<th>Penerima</th>
					<td>:</td>
					<td><?=$data_orders['shipping_to'] ?></td>
				</tr>
				<tr>
					<th>No Hp</th>
					<td>:</td>
					<td><?=$data_orders['phone_recipient'] ?></td>
				</tr>
				<tr>
					<th>Alamat</th>
					<td>:</td>
					<td><?= $data_orders['address_recipient'] ?></td>
				</tr>
				<tr>
					<th>Provinsi</th>
					<td>:</td>
					<td><?= $provinsi ?></td>
				</tr>
				<tr>
					<th>Kab/Kota</th>
					<td>:</td>
					<td><?= $kota ?></td>
				</tr>
				<tr>
					<th>Kecamatan</th>
					<td>:</td>
					<td><?= $kecamatan ?></td>
				</tr>
				<tr>
					<th>Kodepos</th>
					<td>:</td>
					<td><?=$data_orders['postal_code'] ?></td>
				</tr>
				<tr>
					<th>Pengirim</th>
					<td>:</td>
					<td><?= $data_orders['shipping_from'] ?></td>
				</tr>
				<tr>
					<th>No. Hp</th>
					<td>:</td>
					<td><?= $data_orders['shipping_from'] ?></td>
				</tr>
				<tr>
					<th style="padding: 5px"><h5>Ekspedisi</h5></th>
					<th style="padding: 5px">
						<h5>:</h5>
						<td><?= strtoupper($data_orders['ekspedisi'] . ' - ' . $data_orders['tarif_tipe']) ?></h5>
					</th>
				</tr>
				<?php if ($show_estimasi_print_nota == 'on') { ?>
				<tr>
					<th style="padding: 5px">Estimasi Ongkir</th>
					<th style="padding: 5px">:</th>
					<td><?=number_format($data_orders['shipping_fee'], 0, '.', '.') ?></th>
				</tr>
				<?php } ?>
			</table>
		</div>
	<?php } ?>
	</div>
</body>

</html>


<script src="<?=base_url()?>application/views/administrator/assets/js/jquery-1.11.0.js"></script>
<script type="text/javascript">

	window.print();

	window.onbeforeprint = setShippingHeight;
	var mediaQueryList = window.matchMedia('print');
	mediaQueryList.addListener(function() {
		setShippingHeight();
	});

	window.onafterprint = function() {
		var order_id = <?= $data_orders['id'] ?>;
		var url = "<?= base_url('administrator/main/update_print') ?>";
		$.post(url, { id: order_id, field: 'print_ekspedisi'}, function(data) {
			console.log(data);
			window.location = "<?=$url_redirect?>";
		}, 'json');
	}

	$(document).ready(function() {
		setShippingHeight();

	});

	$(window).resize(function() {
		setShippingHeight();
	});

	function setShippingHeight() {
		var width = $('.shipping-info table').outerWidth(true);
		// $('.shipping-info').css({ height: width - 155 });
	}

</script>