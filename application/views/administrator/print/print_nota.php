<?php

$data_info_kontak = $this->main_model->get_detail('content',array('name' => 'nota'));
$data_footer = $this->main_model->get_detail('content',array('name' => 'footer'));
$data_logo = $this->main_model->get_detail('content',array('name' => 'image_nota'));
$aktif_logo = $this->main_model->get_detail('content',array('name' => 'aktif_logo'));
$data_shipping = $this->main_model->get_detail('content',array('name' => 'shipping_show'));
$data_confirm = $this->main_model->get_list_where('confirmation',array('order_id' => $orders['id']));
$pay_id = $orders['payment_method_id'];

$payment_methode = $this->main_model->get_detail('payment_method',array('id' =>$pay_id));

?>

<!DOCTYPE html>

<html>

<head>

	<title>Struk Belanja</title>

	<!-- Bootstrap Core CSS -->

	<link href="<?=base_url()?>application/views/administrator/assets/css/bootstrap.css" rel="stylesheet">


	<style>


	body {margin:0;padding:0;font-family: Arial;text-transform: uppercase;font-size: 12px;color: #666666;}

	p {margin:0;padding:0;}

	.clear {clear: both;}

	#wrapper {width: 100%;margin: 0;padding: 0;margin-bottom:30px;}

	.header {border-bottom:1px solid #666666;padding:10px 0; margin: 0 10px; text-align: center; font-size: 10px;}

	.header p {text-align:center; font-size: 10px;}

	.informasi {border-bottom:1px solid #666666;margin:10px; padding: 10px 0;}

	.daftar_belanja {border-bottom:1px solid #666666;margin:10px;}

	.daftar_belanja table {font-size:10px;	}

	.total_harga {border-top: 1px solid #666666;margin-top: 20px;padding: 10px 0; font-size: 10px;}

	.total_harga .right {float: right;}

	.footer {padding: 0 10px;}

	.footer p {text-align:center;padding:10px 0 5px;}

	.daftar_belanja td, .informasi p {
		padding: 3px 0;
	}

	.content-top > div {
		display: inline-block;
		vertical-align: middle;
	}

	.content-top {
		margin: 0 auto;
		max-width: 450px;
	}

	.left-content {
		margin-right: 8px;
		width: 18%;
	}

	.content-top img {
		width: 100%;
	}

	.right-content {
		text-align: left;
		width: 60%;
	}

	p{font-size: 10px;}

	.table td {
		font-size: 10px;
	}
</style>
</head>
<body>
	<div id="wrapper">
		<div class="header">
			<?php if ($aktif_logo['value'] == 1) { ?>
				<?php if ($data_logo['value'] == null) { ?>
					<?=$data_info_kontak['value'] ?>
				<?php } else { ?>
					<div class='content-top'>
						<div class="img">
							<img src="<?= base_url() ?>media/images/<?= $data_logo['value'] ?>" alt="<?= base_url() ?>" />
						</div>
						<div class="img">
							<?=$data_info_kontak['value'] ?>
						</div>
					</div>
				<?php } ?>
			<?php } else{ ?>
				<?= $data_info_kontak['value'] ?>
			<?php } ?>
		</div>
		<div class="informasi">
			<p class="col-sm-6">tanggal: <?=date('D, d-m-Y H:i:s') ?></p>
			<?php if ($pay_id != 0) {?>
				<p class="col-sm-6 text-right" style="color:red;"><?=$payment_methode['name'] ?></p>
			<?php } ?>

			<p>ID Pesanan : <?=$orders['id'] ?></p>

			<?php if ($orders['customer_id'] == 0) { ?>
				<p>Customer : <?=$orders['name_customer'] ?> <strong>(Guest)</strong></p>
			<?php } else { ?>
				<p>Customer : <?=$customer['name'] ?></p>
				<p>customer ID : <?=$customer['id'] ?></p>
			<?php } ?>
			<?php if ($orders['notes'] != '') { ?>
				<p><b>Notes : </b><?= $orders['notes'] ?></p>
			<?php } ?>
		</div>

		<div class="daftar_belanja">
			<table width="100%">
				<tr>
					<td colspan="3" align="center">
						<svg id="barcode"></svg>
						<p>Scan Untuk Jadikan Lunas</p>
					</td>
				</tr>
				<tr>
					<td width="60%">items</td>
					<td width="10%">qty</td>
					<td width="30%" style="text-align: right;">harga</td>
				</tr>

				<?php
				$i = 1;
				$total_qty = 0;
				$nota_variant = array();
				$nota_variant_id = array();

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
							'variant_id' => $orders_item->variant_id,
							'prod_id'    => $orders_item->prod_id,
							'qty'        => $orders_item->qty,
							'subtotal'   => $orders_item->subtotal,
							'notes'      => $orders_item->notes
						);
					}
				}

				foreach ($nota_variant as $key => $value) {
					$data_product = $this->main_model->get_detail('product',array('id' => $value['prod_id']));
					$data_variant = $this->main_model->get_detail('product_variant',array('id' => $value['variant_id']));
					$total_qty = $total_qty + $value['qty']; ?>
					<tr>
						<td>
							<?=$data_product['name_item'] ?>
							<br><?=$data_variant['variant']?>
							<?php if ($value['notes']) { ?>
								<br>Catatan : <?= $value['notes'] ?>
							<?php } ?>
						</td>
						<td><?= $value['qty'] ?></td>
						<td style="text-align: right; text-transform: capitalize;">Rp  <?=numberformat($value['subtotal']) ?></td>
					</tr>
					<?php
					$i++;
				} ?>
			</table>

			<div class="total_harga">
				<span class="left">total barang (QTY) :</span> <span class="right"><?=$total_qty?> Pcs</span><br/>
				<?php if ($orders ['order_status'] == 'Dropship') { ?>
					<span class="left">Sub Total :</span> <span class="right" style=" text-transform: capitalize;" >Rp  <?=numberformat($orders['subtotal']) ?></span><br/>
					<span class="left">Ongkos Pengiriman :</span> <span class="right" style=" text-transform: capitalize;">Rp  <?=numberformat($orders['shipping_fee']) ?></span><br/>
				<?php } ?>
				<span class="left"><strong>diskon</strong> :</span> <span class="right" style=" text-transform: capitalize;" >Rp  <?=numberformat($orders['diskon']) ?></span><br/>
				<span class="left"><strong>total pembayaran</strong> :</span> <span class="right" style=" text-transform: capitalize;" >Rp  <?=numberformat($orders['total']) ?></span>
			</div>
			<div class="clear"></div>
			<hr>
			<div class="total_harga">
				<strong>Info Pembayaran</strong><br/>
				<?php
				if ($data_confirm->num_rows() > 0){
					$confirm = $data_confirm->row_array();
					?>
					Rekening : <?=$confirm['bank_account_number'] ?><br/>
					Bank : <?=$confirm['bank'] ?><br/>
					Tanggal Pembayaran : <?=$confirm['date'] ?>
				<?php } else {?>
					- Data Pembayaran tidak ditemukan -
				<?php } ?>
			</div>
		</div>

		<div class="footer">
			<p><?=$data_footer['value'] ?></p>
		</div>
	</div>
</body>
</html>


<script src="<?=base_url()?>application/views/administrator/assets/js/jquery-1.11.0.js"></script>
<script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/jquery-barcode.min.js"></script>

<script type="text/javascript">
	window.print();
	$('#barcode').JsBarcode(<?= $orders['id'] ?>, { height: 50 });

	window.onafterprint = function() {
		window.location = "<?=$url_redirect?>";
		var order_id = <?= $orders['id'] ?>;
		var url = "<?= base_url('administrator/main/update_print') ?>";
		$.post(url, { id: order_id, field: 'print_nota'}, function(data) {
			console.log(data);
			window.location = "<?=$url_redirect?>";
		});
	}
</script>