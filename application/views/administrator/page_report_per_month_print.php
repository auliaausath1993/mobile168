	<head>
		    <link href="<?=base_url()?>application/views/administrator/assets/css/bootstrap.css" rel="stylesheet">
	</head>

		<div class="container">
			<h3 align="center"><?=$header['value']?></h3>
			<h4>Laporan <?=date('M-Y',strtotime($this_month)) ?></h4>

				<table class="table table-bordered">

					<thead>

					<tr class="active">

						<th>No</th>

						<th>No Nota</th>

						<th>Pembeli</th>

						<th>Tanggal Pesanan</th>

						<th>Metode Pembayaran</th>

						<th>Total Modal</th>

						<th>Subtotal</th>

						<th>Biaya Pengiriman</th>

						<th>Total Penjualan</th>



					</thead>



					<tbody>

					<?php

					$i = 1;



					$report_total = 0;

					$total_trans = 0;

					$total_purchase = 0;
					$total_modal = 0;
					foreach($transaksi->result() as $trans):

					$payment_method = $this->main_model->get_detail('payment_method',array('id' => $trans->payment_method_id));

					// Total Pembelian

					$data_items_trans = $this->main_model->get_list_where('orders_item',array('order_id' => $trans->id, 'order_status !=' => 'Cancel'));
					$all_modal = 0;
					foreach($data_items_trans->result() as $items_trans):

						$data_this_product = $this->main_model->get_detail('product',array('id' => $items_trans->prod_id));
						$modal=$data_this_product['price_production'];

						$dt = $modal * $items_trans->qty;

						$all_modal = $all_modal + $dt;

					endforeach;

					$purchase = $trans->subtotal;

					$total_purchase = $total_purchase + $purchase;

					$this_total = $trans->total;

					$report_total = $report_total + $this_total ;

					$data_this_customer = $this->main_model->get_detail('customer',array('id' => $trans->customer_id));

					$total_trans = $total_trans + $trans->shipping_fee;

					?>

						<tr>

							<td><?=$i ?></td>

							<td>#<?=$trans->id?></td>

							<td><?php if ($trans->customer_id == 0) {echo"<span style=\"color:#e23427;\">".$trans->name_customer." - <strong>(Guest)</strong>";
							 }else{echo $data_this_customer['name']." (".$data_this_customer['id'].")";} ?></td>

							<td><?=date("D, d-M-Y",strtotime($trans->order_datetime))?></td>

							<?php if ($trans->payment_method_id == 0) { ?>
								<td> - </td>
							<?php }else{ ?>
								<td><?=$payment_method['name'] ?></td>
							<?php } ?>
							<td>
								Rp. <?=numberformat($all_modal)?>
							</td>
							<td>Rp. <?=numberformat($trans->subtotal)?></td>

							<td>Rp. <?=numberformat($trans->shipping_fee)?></td>

							<td>Rp. <?=numberformat($trans->total)?></td>





						</tr>

					<?php

					$i++;

					$total_modal = $total_modal + $all_modal;

					endforeach; ?>



					<tr>

						<td colspan="5"><strong>TOTAL</strong></td>

						<td>Rp. <?=numberformat($total_modal) ?></td>

						<td>Rp. <?=numberformat($total_purchase) ?></td>

						<td>Rp. <?=numberformat($total_trans) ?></td>

						<td>Rp. <?=numberformat($report_total) ?></td>



					</tbody>



				</table>

		</div>


<script>

	window.print();

	window.location = "<?=base_url()?>administrator/main/report_per_month";



</script>