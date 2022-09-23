<?php

$data_info_kontak = $this->main_model->get_detail('content',array('name' => 'nota'));
$data_footer = $this->main_model->get_detail('content',array('name' => 'footer'));
$data_logo = $this->main_model->get_detail('content',array('name' => 'image_nota'));
$aktif_logo = $this->main_model->get_detail('content',array('name' => 'aktif_logo'));
$pay_id = $data_orders['payment_method_id'];

$payment_methode = $this->main_model->get_detail('payment_method',array('id' =>$pay_id));
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>

	<title>Struk Belanja</title>

	<link href="<?=base_url()?>application/views/administrator/assets/css/bootstrap.css" rel="stylesheet">


	<style>
	body {margin:0;padding:0;font-family: Arial;text-transform: uppercase;font-size: 14px;color: #666666;}
	p {margin:0;padding:0;}
	.clear {clear: both;}
	#wrapper {width: 98%;margin-left: auto;margin-right:auto;padding: 20px 0;}
	.border {
		padding:20px;
		border:2px solid #333;

	}
	.content-top > div {
		display: inline-block;
		vertical-align: middle;
	}
	.content-top {
		margin: 0 auto;
		max-width: 600px;
	}
	.left-content {
		margin-right: 8px;
		width: 15%;
	}
	.content-top img {
		width: 100%;
	}
	.right-content {
		text-align: left;
		width: 60%;
	}
</style>
</head>
<body>
	<div id="wrapper">
		<div class="border" style="text-align: center;">
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
		<div class="border">
			<br>
			<table width="100%">
				<tr>
					<td>tanggal: <?=date('D, d-m-Y H:i:s') ?></td>
					<td>no nota: #<?=$data_orders['id'] ?></td>
					<?php if ($data_orders['customer_id'] == 0) { ?>
						<td>customer: <?=$data_orders['name_customer'] ?> (Guest)</td>
					<?php } else { ?>
						<td>customer: <?=$customer['name'] ?> (<?=$customer['id'] ?>)</td>
					<?php } ?>
					<?php if ($pay_id != 0) {?>
						<td style="color:red;"><?=$payment_methode['name'] ?></td>
					<?php } ?>
				</tr>
			</table>
		</div>
		<div class="border">
			<h2>DATA BELANJA</h2>
			<table width="100%">
				<tr>
					<td width="50%"><strong>items</strong></td>
					<td width="15%" style="text-align: center;"><strong>qty</strong></td>
					<td width="35%" style="text-align: right;"><strong>harga</strong></td>
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
						<td style="text-align: center;"><?= $value['qty'] ?></td>
						<td style="text-align: right; text-transform: capitalize;">Rp <?=numberformat($value['subtotal']) ?></td>
					</tr>
					<?php
					$i++; } ?>
				</table>
				<hr>
				<table width="100%">
					<tr>
						<td><strong>total belanja</strong></td>
						<td>:</td>
						<td style="text-align: right; text-transform: capitalize;"><strong>Rp <?= numberformat($data_orders['subtotal'])  ?></strong></td>
					</tr>
					<tr>
						<td><strong>Ongkos Pengiriman</strong></td>
						<td>:</td>
						<td style="text-align: right; text-transform: capitalize;"><strong>Rp <?=numberformat($data_orders['shipping_fee']) ?></strong></td>
					</tr>
					<tr>
						<td><strong>Total Harga</strong></td>
						<td>:</td>
						<td style="text-align: right; text-transform: capitalize;"><strong>Rp <?=numberformat( $data_orders['total'] ) ?></strong></td>
					</tr>
				</table>
				<div class="clear"></div>
			</div>
			<?php if ($data_orders['order_status'] == 'Dropship' && $shipping['value'] == 1) { ?>
			<div class="border">
				<h2>DATA SHIPPING</h2>
				<table width="100%">
					<tr>
						<td><strong>Dari </strong></td>
					</tr>
					<tr>
						<td><?=$data_orders['shipping_from'] ?></td>
					</tr>
					<tr>
						<td><strong>Kepada </strong></td>
					</tr>
					<tr>
						<td><?=$data_orders['shipping_to'] ?></td>
					</tr>
					<tr>
						<td><strong>No Telpon Penerima </strong></td>
					</tr>
					<tr>
						<td><?=$data_orders['phone_recipient'] ?></td>
					</tr>
					<tr>
						<td><strong>Alamat Penerima </strong></td>
					</tr>
					<tr>
						<td>
							<?=$data_orders['address_recipient'] ?>
							<?= ', Kec. ' . $kecamatan  . ', ' . $kota . ', ' . $provinsi ?>
						</td>
					</tr>
					<tr>
						<td><strong>Kode Pos</strong></td>
					</tr>
					<tr>
						<td><?=$data_orders['postal_code'] ?></td>
					</tr>
					<tr>
						<td><strong>Ekspedisi</strong></td>
					</tr>
					<tr>
						<td>
							<?= $data_orders['ekspedisi'] . ' - ' . $data_orders['tarif_tipe'] ?>
						</td>
					</tr>
				</table>
			</div>
		<?php } ?>
			<div class="border footer_nota" style="text-align: center;">
				<?=$data_footer['value'] ?>
			</div>
		</div>
	</body>

	</html>


	<script src="<?=base_url()?>application/views/administrator/assets/js/jquery-1.11.0.js"></script>
	<script type="text/javascript">

		window.print();
		setTimeout(function() {
			// window.location = "<?=$url_redirect?>";
		}, 500);

		window.onafterprint = function() {
			var order_id = <?= $data_orders['id'] ?>;
			var url = "<?= base_url('administrator/main/update_print') ?>";
			$.post(url, { id: order_id, field: 'print_ekspedisi'}, function(data) {
				console.log(data);
				window.location = "<?=$url_redirect?>";
			}, 'json');
		}

	</script>