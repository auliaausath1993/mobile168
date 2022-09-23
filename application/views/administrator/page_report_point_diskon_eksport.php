	<head>
		    <link href="<?=base_url()?>application/views/administrator/assets/css/bootstrap.css" rel="stylesheet">
	</head>	

		<div class="container">
			<h3 align="center"><?=$header['value']?></h3>
			<h4>Laporan <?=date('M-Y',strtotime($this_month)) ?></h4>


				<table id="table-laporan" class="table table-bordered table-striped">

			    				<thead>

			    					<tr>

			    						<th>No</th>

			    						<th>ID Pesanan</th>

			    						<th>Pembeli</th>

			    						<th>Tanggal Pesanan</th>
			    						
			    						<th>Total Point</th>
			    						<th>Total Diskon</th>

			    					</tr>

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



			    						$this_purchase = 0;



			    						$all_modal = 0;
			    						foreach($data_items_trans->result() as $items_trans):

			    							$data_this_product = $this->main_model->get_detail('product',array('id' => $items_trans->prod_id));

			    							$modal=$data_this_product['price_production'];

			    							$dt = $modal * $items_trans->qty;

			    							$all_modal = $all_modal + $dt;
			    						endforeach;

			    						$this_total = $trans->total;
			    						$this_diskon = $trans->diskon;
			    						$this_point = $trans->point;


			    						$report_total = $report_total +$this_total ;
			    						$report_diskon = $report_diskon +$this_diskon ;
			    						$report_point = $report_point +$this_point ;

			    						$data_this_customer = $this->main_model->get_detail('customer',array('id' => $trans->customer_id));

			    						$total_trans = $total_trans + $trans->shipping_fee;

			    						?>

			    						<tr>

			    							<td><?=$i ?></td>

			    							<td>#<?=$trans->id?></td>

			    							<td><?php if ($trans->customer_id == 0) {echo"<span style=\"color:#e23427;\">".$trans->name_customer." - <strong>(Guest)</strong>";
			    						}else{echo $data_this_customer['name']." (".$data_this_customer['id'].")";} ?></td>

			    						<td><?=date("D, d-M-Y",strtotime($trans->order_datetime))?></td>

			    						


			    						<td>Rp. <?=numberformat($trans->diskon)?></td>
			    						<td>Rp. <?=numberformat($trans->point)?></td>

			    					</tr>

			    					<?php

			    					$i++;

			    					$total_purchase = $total_purchase + $trans->subtotal;
			    					$total_modal = $total_modal + $all_modal;

			    				endforeach; ?>



			    				<tr>

			    					<td colspan="4"><strong>TOTAL</strong></td>

			    					
			    					<td>Rp. <?=numberformat($report_diskon) ?></td>
			    					<td>Rp. <?=numberformat($report_point) ?></td>

			    				</tr>

			    			</tbody>



			    		</table>

		</div>			