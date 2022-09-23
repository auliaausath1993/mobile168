<?php include 'includes/header.php'; ?>
<div id="report" class="container-fluid">
	<div class="panel">
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-12">
					<div class="search_report">
						<div class="col-lg-12">
							<h1 class="page-header">
								Laporan Point dan Diskon
							</h1>
						</div>
						<div class="body-report">

							<?=form_open('administrator/main/report_per_month_point_process', array('class' => 'form-inline')) ?>
							<div class="form-group">
								<label>Bulan</label>
								<input type="text" name="month" class="form-control datepicker" data-date-format="yyyy-mm" data-date-viewmode="years" data-date-minviewmode="months" />
							</div>
							<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> LIHAT LAPORAN</button>
							<a href="<?=base_url() ?>administrator/main/report_per_month_diskon_point" class="btn btn-danger">RESET</a>

							<?=form_close()?>


							<div class="col-sm-2">
								<form action="<?=base_url()?>administrator/main/report_poin_diskon_eksport" method="post" target="_new">
									<input class="month-input" type="hidden" name="month" value="<?=$this_month ?>"/>
									<button type="submit" id="exsport-laporan-day" class="btn btn-success" style="margin-bottom: 10px; padding:4px 12px;"><i class="fa fa-file-excel-o"></i> Export Excel</button>
								</form>
							</div>

						</div>

					</div>

				</div>

			</div>

			<!-- /.row -->

			<div class="row">

				<div class="col-lg-12">

		    			<!-- <div class="row">
		    				<div class="col-sm-1">
			    				<form action="<?=base_url()?>administrator/main/report_per_month_print" method="post" target="_new">
									<input class="date-input" type="hidden" name="month" value="<?=$this_month ?>"/>
									<input type="hidden" name="jenis_customer" value="<?=$jenis_customer ?>"/>
									<button type="submit" class="btn btn-info buton-print"><i class="fa fa-print"></i> CETAK</button>
								</form>
			    			</div>
			    			<div class="col-sm-2">
			    				<form action="<?=base_url()?>administrator/main/report_per_month_eksport" method="post" target="_new">
									<input class="month-input" type="hidden" name="month" value="<?=$this_month ?>"/>
									<input type="hidden" name="jenis_customer" value="<?=$jenis_customer ?>"/>
									<button type="submit" id="exsport-laporan-day" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Excel</button>
								</form>
			    			</div>
			    		</div> -->
			    		<div class="table-responsive">
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
			    </div>

			</div>
		</div>
	</div>

</div>

</div>

<?php
foreach($transaksi->result() as $trans):
	?>
	<div class="modal fade" id="modal-<?=$trans->id?>" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" style="background-color: rgb(40, 169, 131); color: white;">
					<h4 class="modal-title">Detail List Modal Product Pada Pesanan Tanggal <?=date("d-M-Y",strtotime($trans->order_datetime))?></h4>
				</div>

				<div class="modal-body">

					<table class="table table-bordered">

						<thead>
							<tr>
								<th>Item</th>
								<th>Harga Modal</th>
								<th>QTY yang terjual</th>
								<th class="text-right">Modal</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$data_items_trans = $this->main_model->get_list_where('orders_item',array('order_id' => $trans->id));
							$all_modal = 0;
							foreach($data_items_trans->result() as $list_item):

								$data_this_product = $this->main_model->get_detail('product',array('id' => $list_item->prod_id));

								$list_product = $data_this_product ['name_item'];

								$prod_price=$data_this_product['price_production'];

								$md = $prod_price * $list_item->qty;

								$all_modal = $all_modal + $md;
								?>


								<tr>
									<td><?= $list_product?></td>
									<td>Rp. <?=numberformat($prod_price)?></td>
									<td><?= $list_item->qty?></td>
									<td class="text-right">Rp. <?=numberformat($md)?></td>
								</tr>

								<?php
							endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3"><strong>TOTAL MODAL</strong></td>
								<td class="text-right">Rp. <?=numberformat($all_modal)?></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endforeach; ?>


<?php include "includes/footer.php"; ?>

