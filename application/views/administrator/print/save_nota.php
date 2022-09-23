<?php

$data_info_kontak = $this->main_model->get_detail('content',array('id' => 3));
$data_footer = $this->main_model->get_detail('content',array('id' => 8));

$data_logo = $this->main_model->get_detail('content',array('id' => 9));

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>

<title>Struk Belanja</title>
<link href="<?=base_url()?>application/views/administrator/assets/css/bootstrap.css" rel="stylesheet">
 	<script src="<?=base_url()?>application/views/administrator/assets/js/jquery-1.11.0.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?=base_url()?>application/views/administrator/assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/tableExport.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/jquery.base64.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/html2canvas.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/jspdf/libs/sprintf.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/jspdf/jspdf.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/jspdf/libs/base64.js"></script>

<style type="text/css">
td {
    font-size: 12px;
    margin: 0;
}

td p{
	margin: 0;
}
</style>
</head>



<body>



<div id="wrapper">
	<table id="view-nota" class="table" style="width: 400px;" border="1">
		<thead>
			<?php if ($data_logo['value'] == null) { ?>
				<th colspan="8"><?=$data_info_kontak['value'] ?></th>
			<?php }else{ ?>
				<th colspan="8" class="text-center">
					<img width="60" src="<?= base_url() ?>media/images/<?= $data_logo['value'] ?>" alt="<?= base_url() ?>" />
					<span style=" display: inline-block; vertical-align: middle; width: 350px;"><?=$data_info_kontak['value'] ?></span>
				</th>
			<?php } ?>
		</thead>
		<tbody>
			<tr>
				<td colspan="8">
					<p>Tanggal: <?=date('D, d-m-Y H:i:s') ?></p>
					<p>Id Pesanan: <?=$order['id']?></p>
					<?php if ($order['customer_id'] == 0) { ?>
						<p>customer: <?=$order['name_customer'] ?> <strong>(Guest)</strong></p>
					<?php }else{ ?>
						<p>customer: <?=$customer['name'] ?></p>
						<p>customer ID: <?=$customer['id'] ?></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td width="40%">
					<strong>Items</strong>
				</td>
				<td width="30%">
					<strong>Qty</strong>
				</td>
				<td width="30%" style="text-align: right;">
					<strong>Harga</strong>
				</td>
			</tr>
			<?php 

			$i = 1;

			$total_qty = 0;

			$nota_variant = array(); 
			$nota_variant_id = array();

			foreach($order_item->result() as $orders_item) : 

				if(in_array($orders_item->variant_id,$nota_variant_id))
				{
					$nota_variant[$orders_item->variant_id]['qty'] = $nota_variant[$orders_item->variant_id]['qty'] + $orders_item->qty;
					$nota_variant[$orders_item->variant_id]['subtotal'] = $nota_variant[$orders_item->variant_id]['subtotal'] + $orders_item->subtotal;
			
				}
				else
				{	
					$nota_variant_id[] = $orders_item->variant_id;
					$nota_variant[$orders_item->variant_id] = array(
																	'variant_id' => $orders_item->variant_id,
																	'prod_id' => $orders_item->prod_id,
																	'qty' => $orders_item->qty,
																	'subtotal' => $orders_item->subtotal
																);
				}

			endforeach;	


			foreach($nota_variant as $key=>$value) : 

			$data_product = $this->main_model->get_detail('product',array('id' => $value['prod_id']));

			$data_variant = $this->main_model->get_detail('product_variant',array('id' => $value['variant_id']));

			$total_qty = $total_qty + $value['qty'];

			?>

				<tr>

					<td><?=$data_product['name_item'] ?><br/><?=$data_variant['variant']?></td>

					<td><?= $value['qty'] ?></td>

					<td style="text-align: right; text-transform: capitalize;">Rp  <?=numberformat($value['subtotal']) ?></td>

				</tr>	
			<?php $i++; endforeach; ?>	
			<tr>

				<td colspan="8">
					<div class="row">
						<p class="col-sm-6">TOTAL BARANG (QTY) :</p>
						<p class="col-sm-6 text-right"><?=$total_qty?> Pcs</p>
					</div>
					<?php if ($order ['order_status'] == 'Dropship') { ?>
						<div class="row">
							<p class="col-sm-6">SUBTOTAL :</p>
							<p class="col-sm-6 text-right">Rp <?=number_format($order['subtotal'])?></p>
						</div>
						<div class="row">
							<p class="col-sm-6">ONGKOS PENGIRIMAN :</p>
							<p class="col-sm-6 text-right">Rp <?=number_format($order['shipping_fee'])?></p>
						</div>
					<?php } ?>
					<div class="row">
						<p class="col-sm-6"><strong>TOTAL PEMBAYARAN:</strong></p>
						<p class="col-sm-6 text-right">Rp <?=number_format($order['total'])?></p>
					</div>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8" class="text-center">
					<p>barang yang sudah anda beli tidak dapat dikembalikan. terima kasih telah belanja.</p>

					<p><?=$data_footer['value'] ?></p>
				</td>
			</tr>
		</tfoot>
	</table>

</div>



</body>

</html>

<script type="text/javascript">
</script>