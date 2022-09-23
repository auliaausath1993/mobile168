<?php include "includes/header.php"; ?>

<script type="text/javascript">

	var table_name = '<?= $table_name; ?>';

</script>

<!-- Content

	================================================== -->

	<div id="page-wrapper">



		<div id="report" class="container-fluid">

			<!-- Page Heading -->

			<div class="row">

				<div class="col-lg-12">

					<div class="search_report">

						<div class="heading-report">

							<h1 class="page-header">

								Lihat laporan <small> Tutup Buku</small>

							</h1>

							<ul id="submenu-container" class="nav nav-tabs" style="margin-bottom: 20px;">
								<!-- <li><a href="<?=base_url()?>administrator/main/report_tutup_buku_v2" ><b>Laporan Belum Tutup Buku</b></a></li> -->
								<li><a href="<?=base_url()?>administrator/main/report_tutup_buku_lock" ><b>Laporan Tutup Buku</b></a></li>
							</ul>

							<ol class="breadcrumb">

								<li class="active">

									<i class="fa fa-list"></i> Laporan Tutup Buku

								</li>

							</ol>

						</div>

						<div class="body-report">

							<?=form_open('administrator/main/report_tutupbuku_process') ?>

							Bulan :<input type="text" name="month" class="datepicker" data-date-format="yyyy-mm" data-date-viewmode="years" data-date-minviewmode="months" readonly /> 

							<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> LIHAT LAPORAN</button>

							<?=form_close()?>

						</div>

					</div>

				</div>

			</div>

			<!-- /.row -->

			<div class="row">

				<div class="col-lg-12">

					<div class="row">
						<div class="col-sm-1">
							<form onsubmit="return confirm('Apakah anda yakin ingin tutup buku ?');" action="<?= base_url('administrator/main/update_lock_tutupbuku/') ?>" method="post">
								<input class="month-input" type="hidden" name="month" value="<?=$this_month ?>"/>
								<button id="januari" class="btn btn-md btn-primary colors" type="submit" name="submit-inlock" class="btn btn-md btn-danger">Tutup Buku Periode <?php echo $this_month; ?></button>
							</form>
						</div>
					</div>
					<table id="table-laporan" class="table table-bordered table-striped">

						<thead>

							<tr>

								<th>No</th>

								<th>No Nota</th>

								<th width="10%">Pembeli</th>

								<th>Tanggal Pembayaran</th>

								<th width="10%">Metode Pembayaran</th>
								<th>Total Modal</th>
								<th>Subtotal</th>

								<th>Biaya Pengiriman</th>

								<th>Total Penjualan</th>

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

								$data_items_trans = $this->main_model->get_list_where('orders_item',array('order_id' => $trans->id));



								$this_purchase = 0;



								$all_modal = 0;
								foreach($data_items_trans->result() as $items_trans):

									$data_this_product = $this->main_model->get_detail('product',array('id' => $items_trans->prod_id));

									$modal=$data_this_product['price_production'];

									$dt = $modal * $items_trans->qty;

									$all_modal = $all_modal + $dt;
								endforeach;

								$this_total = $trans->total;


								$report_total = $report_total +$this_total ;

								$data_this_customer = $this->main_model->get_detail('customer',array('id' => $trans->customer_id));

								$total_trans = $total_trans + $trans->shipping_fee;

								?>

								<tr>

									<td><?=$i ?></td>

									<td>#<?=$trans->id?></td>

									<td><?php if ($trans->customer_id == 0) {echo"<span style=\"color:#e23427;\">".$trans->name_customer." - <strong>(Guest)</strong>";
								}else{echo $data_this_customer['name']." (".$data_this_customer['id'].")";} ?></td>

								<td><?=date("D, d-M-Y H:i:s",strtotime($trans->date_payment))?></td>

								<?php if ($trans->payment_method_id == 0) { ?>
									<td> - </td>
								<?php }else{ ?>
									<td><?=$payment_method['name'] ?></td>
								<?php } ?>

								<td>
									<a href="#" data-toggle="modal" data-target="#modal-<?=$trans->id?>" class="list-modal">Rp. <?=numberformat($all_modal)?></a>
								</td>
								<td>Rp. <?=numberformat($trans->subtotal)?></td>

								<td>Rp. <?=numberformat($trans->shipping_fee)?></td>

								<td>Rp. <?=numberformat($trans->total)?></td>

							</tr>	

							<?php 

							$i++;

							$total_purchase = $total_purchase + $trans->subtotal;
							$total_modal = $total_modal + $all_modal;

						endforeach; ?>	



						<tr>

							<td colspan="5"><strong>TOTAL</strong></td>

							<td>Rp. <?=numberformat($total_modal) ?></td>
							<td>Rp. <?=numberformat($total_purchase) ?></td>

							<td>Rp. <?=numberformat($total_trans) ?></td>

							<td>Rp. <?=numberformat($report_total) ?></td>

						</tr>

					</tbody>

					

				</table>

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
<script type="text/javascript">
	function validate(form) {

		if(!valid) {
			alert('Please correct the errors in the form!');
			return false;
		}
		else {
			return confirm('Do you really want to submit the form?');
		}
	}

</script>

<form onsubmit="return validate(this);">

