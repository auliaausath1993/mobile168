<?php

$data_info_kontak = $this->main_model->get_detail('content',array('id' => 3));

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>
	<title>Struk Belanja</title>
	<style>
	@font-face {font-family: BPdots;src: url(fonts/BPdots.otf);}
	body {margin:0;padding:0;font-family: Arial;text-transform: uppercase;font-size: 10px;color: #666666;}
	p {margin:0;padding:0;}
	.clear {clear: both;}
	#wrapper {width: 220px;margin: 0;padding: 0;}
	.header {border-bottom:1px solid #666666;padding:0 0 10px 0;}
	.header p {text-align:center;}
	.informasi {border-bottom:1px solid #666666;padding:10px 0;}
	.daftar_belanja {border-bottom:1px solid #666666;padding:10px 0;}
	.daftar_belanja table {font-size:10px;	}
	.total_harga {border-top: 1px solid #666666;margin-top: 20px;padding: 10px 0;}
	.total_harga .right {float: right;}
	.footer p {text-align:center;padding:20px 0 30px 0;}
</style>

</head>
<body>
	<div id="wrapper">
		<div class="header">
			<?=$data_info_kontak['value'] ?>
		</div>
		<div class="informasi">
			<p>tanggal: <?=date('D, d-m-Y H:i:s') ?></p>
			<p>no nota: <?=$data_orders['id'] ?></p>
			<p>customer: <?=$customer['name'] ?></p>
			<p>customer ID: <?=$customer['id'] ?></p>
		</div>
		<div class="daftar_belanja">
			<table>
				<tr>
					<td width="60%">items</td>
					<td width="5%">qty</td>
					<td width="35%" style="text-align: right;">harga</td>
				</tr>
				<?php 
				$i = 1;
				$total_qty = 0;
				foreach($orders_item->result() as $orders) {
					$data_customer = $this->main_model->get_detail('customer',array('id' => $orders->customer_id));
					$data_product = $this->main_model->get_detail('product',array('id' => $orders->prod_id));
					$data_color = $this->main_model->get_detail('rel_color_prod',array('id' => $orders->color_id));
					$total_qty = $total_qty + $orders->qty;
					?>
					<tr>
						<td><?=$data_product['name_item'] ?><br/><?=$data_color['color']?></td>
						<td><?=$orders->qty ?></td>
						<td>Rp<?=numberformat($orders->subtotal) ?></td>
					</tr>
					<?php 
					$i++;
				} ?>
			</table>
			<div class="total_harga">
				<span class="left">total barang (QTY) :</span> <span class="right"><?=$total_qty?> Pcs</span>
				<br/>
				<span class="left">total harga :</span> <span class="right">Rp<?=numberformat($total) ?></span>
			</div>
			<div class="clear"></div>
		</div>
		<h4>DATA SHIPPING</h4>
		<div class="daftar_belanja">
			<table>
				<tr>
					<td width="60%">FROM : </td>
				</tr>
				<tr>
					<td width="60%"><?=$data_orders['shipping_from'] ?></td>
				</tr>
			</table>
		</div>
		<div class="daftar_belanja">
			<table>
				<tr>
					<td width="60%">TO : <br/></td>
				</tr>
				<tr>
					<td width="60%"><?=$data_orders['shipping_to'] ?></td>
				</tr>
			</table>
			<div class="clear"></div>
		</div>
		<div class="daftar_belanja">
			<table>
				<tr>
					<td width="60%">ONGKIR : <?=$data_orders['shipping_fee'] ?><br/></td>
				</tr>
			</table>
			<div class="clear"></div>
		</div>
		<div class="footer">
			<p>barang yang sudah anda beli tidak dapat dikembalikan. terima kasih telah belanja.</p>
		</div>

	</div>
</body>
</html>
<script type="text/javascript">
	window.print();
	window.location = "<?=$url_redirect?>";

</script>