<?php include 'includes/header.php' ?>
<div id="page-wrapper">
    <div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Dashboard <small> Statistik Keseluruhan</small>
				</h1>
			</div>
		</div>

        <?= $this->session->flashdata('message') ?>
        <?php if ($available_date <= $reminder_date) { ?>
            <div id="alert_act" class="alert alert-danger" role="alert">
                <span><strong>Peringatan : </strong><br/>Pelanggan Yth, masa berlaku paket berlangganan Anda telah hampir habis, silahkan hubungi Tim Marketing kami untuk memperpanjang Paket Anda. Terima kasih</span>
                <a href="<?= base_url()?>administrator/main/info_paket" class="btn button-upgrade btn-danger offset10">Activation</a>
            </div>
        <?php } ?>

		<div class="well">
			<div class="row">
				<div class="span3 col-md-4">
					<h4><strong><?= $client_name ?></strong></h4>
					<!-- Anda login sebagai <b><?= $this->session->userdata('webadmin_user_name'); ?></b> -->
				</div>
				<div class="span3 col-md-4">
					<h5>
						<strong> MAKSIMAL PRODUK : </strong>Unlimited<br/>
						<strong>PRODUK TER-PUBLISH : </strong><?=$total_publish_product ?><br/>
						<strong>SISA KUOTA PRODUK : </strong>Unlimited<br/>
					</h5>
				</div>
				<div class="span3 col-md-4">
					<h5>
						<strong> MAKSIMAL PELANGGAN :</strong>Unlimited<br/>
						<strong>JUMLAH PELANGGAN ANDA : </strong><?= $total_customer?> <br/>
						<strong>SISA KUOTA PELANGGAN : </strong>Unlimited<br/>
					</h5>
				</div>
			</div>
		</div>
		<hr>

		<div class="row">
			<div class="col-md-3 m-b">
				<a href="<?= base_url()?>administrator/main/order_all" class="btn btn-info btn-block btn-lg"><i class="fa fa-shopping-cart"></i> Pesanan </a>
			</div>
			<div class="col-md-3 m-b">
				<a href="<?= base_url()?>administrator/main/customer" class="btn btn-danger btn-block btn-lg"><i class="fa fa-user"></i> Pelanggan </a>
			</div>
			<div class="col-md-3 m-b">
				<a href="<?= base_url()?>administrator/main/report_per_day" class="btn btn-success btn-block btn-lg"><i class="fa fa-search"></i> Laporan</a>

			</div>
			<div class="col-md-3 m-b">
				<a href="<?= base_url()?>administrator/main/info_paket" class="btn btn-warning btn-block btn-lg"><i class="fa fa-medkit"></i> Informasi Paket</a>
			</div>
		</div>
		<hr>

		<div class="panel">
			<div class="panel-body">
				<div class="row">
					<div class="statistik-wrapper">
						<div class="col-lg-12">
							<h4>Statistik Penjualan </h4>
							<div style="width:100%; margin-bottom: 15px;">
								<div>
									<canvas id="canvas" height="250" width="960"></canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<section class="panel">
					<div class="panel-body">
						<h4>Informasi Pesanan </h4>
						<ul id="myTab" class="nav nav-tabs nav-justified" role="tablist">
							<li class="active">
								<a href="#stock-table" role="tab" data-toggle="tab">Peringatan Stok <span class="label label-danger"><?= $stock_total ?></span></a>
							</li>
							<li><a href="#pesanan-trakhir" role="tab" data-toggle="tab">Pesanan Terakhir</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="stock-table">
								<div class="table-responsive">
									<table class="table table-bordered table-hover table-striped">
										<thead>
											<tr>
												<th class="text-center">No</th>
												<th>Nama Produk</th>
												<th>Varian/Warna</th>
												<th>Sisa Stok</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$i = 1 * ($offset + 1);
											foreach ($min_stock_product as $product) { ?>
												<tr>

													<td class="text-center"><?= $i++ ?></td>
													<td><?= $product->name_item ?></td>
													<td><?= $product->variant ?></td>
													<td><?= $product->stock ?> Pcs</td>
													<td>
														<?php $product_type = $product->product_type == 'Ready Stock' ? 'ready_stock' : 'pre_order' ?>
														<a href="<?= base_url('administrator/main/edit_product/' . $product_type . '/' . $product->prod_id); ?>" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Tambah Stok</a>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<?= $this->pagination->create_links(); ?>
							</div>
							<div class="tab-pane" id="pesanan-trakhir">
								<div class="table-responsive">
									<table class="table table-bordered table-hover table-striped">
										<thead>
											<tr>
												<th class="text-center">#</th>
												<th>ID Pelanggan</th>
												<th>Nama Pelanggan</th>
												<th>Nama Produk</th>
												<th>QTY</th>
												<th>Subtotal</th>
											</tr>
										</thead>
										<tbody>
										<?php
										$i = 1;
										foreach ($last_order as $order) { ?>
											<tr>
												<td class="text-center"><?= $i++ ?></td>
												<td>
													<?= $order->customer_id > 0 ? $order->customer_id : '<span style="color:#e23427;"><strong>(Guest)</strong></span>' ?>
												</td>
												<td>
													<?= $order->customer_id > 0 ? $order->customer_name : '<span style="color:#e23427;">' . $order->name_customer . '</span>' ?>
												</td>
												<td><?= $order->name_item ?></td>
												<td><?= $order->qty ?></td>
												<td>
													<?= 'Rp. ' . number_format($order->subtotal, 0, '.', '.') ?>
												</td>
												<?php } ?>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
		<?php if ($nm == '12345') { ?>
			<div class="modal fade" id="admin_info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
							<h4 class="modal-title" id="myModalLabel">Pemberitahuan Update Password Admin</h4>
						</div>
						<div class="modal-body">
							Untuk memberikan keamanan pada anda silahkan anda ganti password anda dengan yang baru
						</div>
						<div class="modal-footer">
							<button type="button" class="btn close-button" data-dismiss="modal" aria-hidden="true">Close</button>
							<a href="<?= base_url(); ?>administrator/main/edit_profile" class="btn button-update btn-primary offset10">Update Profile</a>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<script>
	var base_url = '<?= base_url() ?>';
	$('.close-button').click(function(event){
		var check_val = $('.check-popup:checked').val();
		event.preventDefault();
		$.ajax({
			type:"POST",
			url:base_url+'administrator/main/checked',
			data: { checked_box : check_val},
			success:function(data){
			}
		});
	});

	var lineChartData = {
		labels : <?= $last_chart_date ?>,
		datasets : [{
			label: 'Statistik Pesanan',
			fillColor : 'rgba(151,187,205,0.2)',
			strokeColor : 'rgba(151,187,205,1)',
			pointColor : 'rgba(151,187,205,1)',
			pointStrokeColor : '#fff',
			pointHighlightFill : '#c00000',
			pointHighlightStroke : 'rgba(151,187,205,1)',
			data : <?= $last_chart_order ?>
		}]
	}
	window.onload = function() {
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true
		});
	}
	localStorage.setItem('pincode', <?= $pincode['value'] ?>);
	localStorage.setItem('notifikasi', '<?= $notifikasi['value'] ?>');
</script>

<?php include 'includes/footer.php' ?>
