<?php include 'includes/header.php' ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Data Pembelian</h1>
				<?= validation_errors('<div class="alert alert-danger">', '</div>') ?>
				<?= $this->session->flashdata('message'); ?>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?php include 'includes/form_pembelian.php'; ?>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<div class="col-md-12 text-right" style="margin-bottom: 10px">
					<a href="<?= base_url('administrator/main/export_pembelian') ?>" target="_blank" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
					<a href="<?= base_url('administrator/main/print_pembelian') ?>" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
				</div>
				<form id="form_update_payment" method="POST" action="<?= base_url('administrator/main/pembelian_update_payment') ?>">
					<input type="hidden" name="purchase_id" id="purchase_id">
					<input type="hidden" name="payment_status" id="purchase_payment_status">
				</form>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th class="text-center">No. Invoice</th>
										<th class="text-center">Tanggal Pembelian</th>
										<th class="text-center">Nama Produk</th>
										<th class="text-center">Nama Supplier</th>
										<th class="text-center">Status Pembelian</th>
										<th class="text-center">Status Pembayaran</th>
										<th class="text-center">Tanggal Pembayaran</th>
										<th class="text-center">User</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php if (count($results) == 0) { ?>
										<tr>
											<td colspan="9" class="text-center">Data Pembelian Kosong</td>
										</tr>
									<?php }
									foreach ($results as $result) { ?>
										<tr>
											<td class="text-center"><?= $result->no_invoice ?></td>
											<td>
												<?= date('d-m-Y', strtotime($result->purchase_date)) ?><br>
												<?= date('H:i:s', strtotime($result->purchase_date)) ?>
											</td>
											<td><?= $result->name_item ?></td>
											<td><?= $result->nama_supplier ?></td>
											<td class="text-center"><?= $result->purchase_status ?></td>
											<td>
												<select class="form-control" id="payment_status_<?= $result->id ?>">
													<option <?= $result->payment_status == 'Belum Lunas' ? 'selected' : '' ?> value="Belum Lunas">Belum Lunas</option>
													<option <?= $result->payment_status == 'Lunas' ? 'selected' : '' ?> value="Lunas">Lunas</option>
												</select>
											</td>
											<td>
												<?php if ($result->payment_date) { ?>
													<?= date('d-m-Y', strtotime($result->payment_date)) ?><br>
													<?= date('H:i:s', strtotime($result->payment_date)) ?>
												<?php } else { ?>
													<div class="text-center"><b>&ndash;</b></div>
												<?php } ?>
											</td>
											<td><?= $result->user_fullname ?></td>
											<td><button type="button" class="btn btn-success btn-edit-payment" id="<?= $result->id ?>">Edit</button></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'includes/footer.php' ?>
<script type="text/javascript">
	$('.purchase_date_type').click(function() {
		var value = $(this).val();
		if (value == 'date') {
			$('.purchase_month').removeAttr('required').attr('disabled', 'disabled');
			$('.purchaseDateFrom').attr('required', 'required').removeAttr('disabled');
			$('.purchaseDateTo').attr('required', 'required').removeAttr('disabled');
		} else {
			$('.purchase_month').attr('required', 'required').removeAttr('disabled');
			$('.purchaseDateFrom').removeAttr('required').attr('disabled', 'disabled');
			$('.purchaseDateTo').removeAttr('required').attr('disabled', 'disabled');
		}
	});
	$('.payment_date_type').click(function() {
		var value = $(this).val();
		if (value == 'date') {
			$('.payment_month').attr('disabled', 'disabled');
			$('.paymentDateFrom').removeAttr('disabled');
			$('.paymentDateTo').removeAttr('disabled');
		} else {
			$('.payment_month').removeAttr('disabled');
			$('.paymentDateFrom').attr('disabled', 'disabled');
			$('.paymentDateTo').attr('disabled', 'disabled');
		}
	});

	$('.btn-edit-payment').click(function() {
		var id = $(this).attr('id');
		var status = $('#payment_status_' + id).val();
		$('#purchase_id').val(id);
		$('#purchase_payment_status').val(status);
		$('#form_update_payment').submit();
	});
</script>