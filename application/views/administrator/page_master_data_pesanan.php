<?php include 'includes/header.php' ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Master Data <small> Mencari Informasi pesanan</small>
				</h1>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<form method="post" action="<?=base_url()?>administrator/main/master_data_session" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label">Nama Pembeli</label>
						<div class="col-sm-10">
							<div class="radio">
								<label>
									<input type="radio" name="radio_customer" id="radio_customer_all" value="all" checked>
									Semua Pembeli
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="radio_customer" id="radio_tamu_name" value="tamu">
									Non-Pelanggan
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="radio_customer" id="radio_customer_name" value="customer">
									Pelanggan
								</label>
							</div>
							<input name="customer_name" id="customer_name" class="customer_name form-control" type="text" style="display: none;" value="<?php if(!empty($arr['customer_name'])){echo $arr['customer_name'];}else{echo '';}?>" placeholder="Nama Pelanggan" >
							<input type="hidden" class="form-control" id="customer_id" name="customer_id" value="<?php if(!empty($arr['customer_id'])){echo $arr['customer_id'];}else{echo '';}?>" >

						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Tanggal Pesanan</label>
						<div class="col-sm-10">
							<div class="row">
								<div class="col-sm-3">
									<input class="datepicker form-control" name="date_awal" placeholder="Pilih Tanggal" value="<?php if(!empty($arr['date_awal'])){echo $arr['date_awal'];}else{echo '';}?>">
								</div>
								<div class="col-sm-2 text-center">
									<label class="m-t-mini">Sampai Dengan</label>
								</div>
								<div class="col-sm-3">
									<input class="datepicker form-control" name="date_akhir"value="<?php if(!empty($arr['date_akhir'])){echo $arr['date_akhir'];}else{echo '';}?>" placeholder="Pilih Tanggal">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Nama Produk</label>
						<div class="col-sm-10">
							<input name="product_name" id="product_name" class="product_name form-control" type="text" value="<?php if(!empty($arr['product_name'])){echo $arr['product_name'];}else{echo '';}?>" placeholder="Nama Produk" >
							<input type="hidden" class="form-control" id="product_id" name="product_id" value="<?php if(!empty($arr['product_id'])){echo $arr['product_id'];}else{echo '';}?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Status Pesanan </label>
						<div class="col-sm-10">
							<select class="form-control" name="status_pesanan">
								<option value="all">Semua Status</option>
								<option value="keep"<?php if(!empty($arr['status_pesanan']) && $arr['status_pesanan']=="keep"){ echo "selected"; } ?>>Pesanan Keep</option>
								<option value="dropship"<?php if(!empty($arr['status_pesanan']) && $arr['status_pesanan']=="dropship"){ echo "selected"; } ?>>Pesanan Dropship</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Status Pembayaran </label>
						<div class="col-sm-10">
							<select class="form-control" name="status_pembayaran">
								<option value="all">Semua Status</option>
								<option value="paid"<?php if(!empty($arr['status_pembayaran']) && $arr['status_pembayaran']=="paid"){ echo "selected"; } ?>>Lunas</option>
								<option value="unpaid"<?php if(!empty($arr['status_pembayaran']) && $arr['status_pembayaran']=="unpaid"){ echo "selected"; } ?>>Belum Lunas</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Data Per Halaman</label>
						<div class="col-sm-10">
							<select class="form-control" name="tampilan_halaman">
								<option value="">- Pilih Jumlah Tampilan -</option>
								<option value="50"<?php if(!empty($arr['perpage']) && $arr['perpage']=="50"){ echo "selected"; } ?>>50 Per Halaman</option>
								<option value="250"<?php if(!empty($arr['perpage']) && $arr['perpage']=="250"){ echo "selected"; } ?>>250 Per Halaman</option>
								<option value="500"<?php if(!empty($arr['perpage']) && $arr['perpage']=="500"){ echo "selected"; } ?>>500 Per Halaman</option>
								<option value="all">Tampilkan Semua</option>
							</select>
						</div>
					</div>
					<input type="hidden" name="cari" value="<?php echo "cari";?>" >

					<div class="form-group button-cari">
						<div class="col-sm-12">
							<button class="btn btn-primary"  style="margin-bottom:10px;"><i class="fa fa-fw fa-search"> </i>CARI DATA </button>
							<a href="<?=base_url() ?>administrator/main/master_data_pesanan" class="btn btn-danger" style="margin-bottom:10px;">RESET</a>
						</div>
					</div>

				</form>
			</div>
		</div>

		<div class="container col-sm-12" style="margin-bottom: 10px;">
			<h4 class="page-header">
				Filter Search <small></small>
			</h4>
			<?=$this->session->flashdata('message') ?>
		<div class="panel">
			<div class="panel-body">
				<div class="text-right" style="margin-bottom: 20px;">
					<a id="exsport-master-data" class="btn btn-success" href="#"><i class="fa fa-file-excel-o"></i> Export Excel</a>
					<a id="print-master-data" class="btn btn-primary" href="#"><i class="fa fa-print"></i> Cetak</a>
				</div>
				<div class="table-responsive">
					<table id="flex1" class="table table-bordered">
						<thead>
							<tr class="btn-info">
								<th>No</th>
								<th>Tanggal Pesan</th>
								<th>Pembeli</th>
								<th>Produk</th>
								<th>Varian</th>
								<th>QTY</th>
								<th>Subtotal</th>
								<th>Status Pesanan</th>
								<th>Status Pembayaran</th>
								<th>Notes</th>
							</tr>
						</thead>
						<?php
						if ($orders_item->result() != null){
							$i = 1 * ($offset + 1);
							foreach ($orders_item->result() as $items):
								$product = $this->main_model->get_detail('product',array('id' => $items->prod_id));
								$customer = $this->main_model->get_detail('customer',array('id' => $items->customer_id));
								$orders = $this->main_model->get_detail('orders',array('id' => $items->order_id));
								$variant = $this->main_model->get_detail('product_variant',array('id' => $items->variant_id));
								?>
								<tbody>
									<tr>
										<td><?=$i ?></td>
										<td><?=$items->order_datetime ?></td>
										<td><?php if($items->customer_id != 0){echo $customer['name'].' ('.$customer['id'].')';}else{echo "<span style=\"color:#e23427;\"><strong>".$orders['name_customer']." (Guest)</strong><span>";} ?></td>
										<td><?=$product['name_item']?></td>
										<td><?=$variant['variant']?></td>
										<td><?=$items->qty?></td>
										<td><?=$items->subtotal?></td>
										<td><?=$items->order_status?></td>
										<?php if ($items->order_payment =="Unpaid") { ?>
											<td>Belum Lunas</td>
										<?php }else{ ?>
											<td>Lunas</td>
										<?php } ?>
										<td><?=$items->notes ?></td>
									</tr>
								</tbody>
								<?php $i++; endforeach;?>
							<?php }?>
						</table>
					</div>
					<?=$this->pagination->create_links(); ?>
				</div>
			</div>
			<hr/>





			</div>

			<!-- /.container-fluid -->



		</div>


		<!--- AUTOCOMPLETE ---->



		<script type='text/javascript'>
			var site = "<?php echo site_url();?>";

			$(function(){
				$('#customer_name').autocomplete({
					serviceUrl: site+'administrator/main/search_id_customer',
					onSelect: function (suggestion) {
						$('#customer_id').val(suggestion.data);
					}
				});
			});

			$(function(){
				$('#product_name').autocomplete({
					serviceUrl: site+'administrator/main/search_id_produk_item',
					onSelect: function (suggestion) {
						$('#product_id').val(suggestion.data);
					}
				});
			});

			$(function(){
				$('.datepicker').datepicker({
					format: 'yyyy-mm-dd'
				});
			});

			$( "#radio_tamu_name" ).click(function() {
				$("#customer_name").hide();
				$("#customer_id").prop('disabled', true);
			});

			$( "#radio_customer_all" ).click(function() {
				$("#customer_name").hide();
				$("#customer_id").prop('disabled', true);
			});

			$( "#radio_customer_name" ).click(function() {
				$("#customer_name").show();
				$("#customer_id").prop('disabled', false);
			});

			$( "#exsport-master-data" ).click(function() {
				$('#flex1').tableExport({type:'excel',tableName:'download_table', escape:'false'});
			});

			function printData()
			{
				var divToPrint = document.getElementById('flex1');
				var htmlToPrint = '' +
				'<style type="text/css">' +
				'table th, table td {' +
				'border:1px solid #666;' +
				'padding:5px;' +
				'}' +
				'table th{' +
				'background-color: #999; color: #fff;' +
				'}' +
				'</style>';
				htmlToPrint += divToPrint.outerHTML;
				newWin = window.open("");
				newWin.document.write(htmlToPrint);
				newWin.print();
				newWin.close();
			}

			$('#print-master-data').on('click',function(){
				printData();
			})

		</script>
		<?php include "includes/footer.php" ?>

