<?php include 'includes/header.php'; ?>
<div id="report" class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<div class="search_report">
				<div class="heading-report">
					<h1 class="page-header">
						Lihat laporan Pembayaran (Harian) <small> Check laporan per hari</small>

					</h1>
					<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
						<li <?php if ($this->uri->segment(3) == 'report_pembayaran_perday_process') { echo 'class="active"'; } ?>><a href="<?=base_url()?>administrator/main/report_pembayaran_perday" ><b>Laporan Pembayaran Harian</b></a></li>
						<li><a href="<?=base_url()?>administrator/main/report_pembayaran_permonth" ><b>Laporan Pembayaran Bulanan</b></a></li>
					</ul>
					<ol class="breadcrumb">
						<li class="active">
							<i class="fa fa-list"></i> Laporan harian
						</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel">
				<div class="panel-body">
					<?=form_open('administrator/main/report_pembayaran_perday_process', array('class' => 'form-inline')) ?>
					<div class="form-group">
						<label>Tanggal</label>
						<input type="text" name="date" class="datepicker form-control" data-date-format="yyyy-mm-dd" value="<?= $this_date ?>">
					</div>
					<div class="form-group">
						<label>Jenis Customer</label>
						<select name="jenis_customer" class="form-control" style="padding:6px;">
							<option value="All">Semua Customer</option>
							<?php foreach ($customer_types as $type) { ?>
								<option <?= $type->id == $jenis_customer ? 'selected' : '' ?> value="<?= $type->id ?>"><?= $type->name ?></option>
							<?php } ?>
						</select>
					</div>
					<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> LIHAT LAPORAN</button>
					<?=form_close() ?>
					<div class="row">
						<div class="col-sm-2">
							<form action="<?=base_url()?>administrator/main/report_pembayaran_perday_print" method="post" target="_new">
								<input class="date-input" type="hidden" name="date" value="<?=$this_date ?>"/>
								<input type="hidden" name="jenis_customer" value="<?=$jenis_customer ?>"/>
								<button type="submit" class="btn btn-info buton-print btn-block"><i class="fa fa-print"></i> CETAK</button>
							</form>
						</div>
						<div class="col-sm-2">
							<form action="<?=base_url()?>administrator/main/report_pembayaran_perday_eksport" method="post" target="_new">
								<input class="date-input" type="hidden" name="date" value="<?=$this_date ?>"/>
								<input type="hidden" name="jenis_customer" value="<?=$jenis_customer ?>"/>
								<button type="submit" class="btn btn-success btn-block"><i class="fa fa-file-excel-o"></i> Export Excel</button>
							</form>
						</div>
						<div class="col-sm-2">
							<form action="<?=base_url()?>administrator/main/report_pembayaran_perday_print_settle" method="post" target="_new">
								<input class="date-input" type="hidden" name="date" value="<?=$this_date ?>"/>
								<input type="hidden" name="jenis_customer" value="<?=$jenis_customer ?>"/>
								<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-print"></i> Print Settle</button>
							</form>
						</div>
					</div>
					<div class="table-responsive">
						<table id="table-laporan" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No</th>
									<th>No Nota</th>
									<th>Pembeli</th>
									<th>Tanggal Pembayaran</th>
									<th width="10%">Metode Pembayaran</th>
									<th>Total Modal</th>
									<th>Subtotal</th>
									<th>Diskon</th>
									<th>Biaya Pengiriman</th>
									<th>Total Penjualan</th>
								</tr>
							</thead>
							<tbody>
							<?php
							$i = 1;
							foreach ($reports as $report) { ?>
								<tr>
									<td><?=$i ?></td>
									<td>#<?=$report->id?></td>
									<td>
									<?php if ($report->customer_id > 0) {
										echo $report->customer . ' (' . $report->customer_id . ')';
									} else { ?>
										<span style="color: #e23427">
											<?= $report->name_customer ?> - <strong>(Guest)</strong>
										</span>
									<?php } ?>
									</td>
									<td>
										<?=date("D, d-M-Y H:i:s",strtotime($report->date_payment)) ?>
									</td>
									<td><?= $report->payment_method ?: '-' ?></td>
									<td>
										<a href="#" data-toggle="modal" data-target="#modal-<?=$report->id?>" class="list-modal">Rp. <?= numberformat($report->total_modal)?></a>
									</td>
									<td>Rp. <?= numberformat($report->subtotal)?></td>
									<td>Rp. <?= numberformat($report->diskon)?></td>
									<td>Rp. <?= numberformat($report->shipping_fee)?></td>
									<td>Rp. <?= numberformat($report->total)?></td>
								</tr>
							<?php
							$i++;
							} ?>
							<tr>
								<td colspan="5"><strong>TOTAL</strong></td>
								<td>Rp. <?= numberformat($summary['total_modal']) ?></td>
								<td>Rp. <?= numberformat($summary['subtotal']) ?></td>
								<td>Rp. <?= numberformat($summary['total_diskon']) ?></td>
								<td>Rp. <?= numberformat($summary['total_shipping']) ?></td>
								<td>Rp. <?= numberformat($summary['subtotal'] - $summary['total_diskon'] + $summary['total_shipping']) ?></td>
							</tr>
						</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<?php
foreach($reports as $report):
	?>
	<div class="modal fade" id="modal-<?=$report->id?>" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" style="background-color: rgb(40, 169, 131); color: white;">
					<h4 class="modal-title">Detail List Modal Product Pada Pesanan Tanggal <?=date("d-M-Y",strtotime($report->order_datetime))?></h4>
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
							$data_items_trans = $this->main_model->get_list_where('orders_item',array('order_id' => $report->id, 'order_status !=' => 'Cancel'));
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
									<td>Rp. <?= numberformat($prod_price)?></td>
									<td><?= $list_item->qty?></td>
									<td class="text-right">Rp. <?= numberformat($md)?></td>
								</tr>

								<?php
							endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3"><strong>TOTAL MODAL</strong></td>
								<td class="text-right">Rp. <?= numberformat($all_modal)?></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>

