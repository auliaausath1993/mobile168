<?php include 'includes/header.php' ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?= $title ?></h1>
				<?= validation_errors('<div class="alert alert-danger">', '</div>') ?>
				<?= $this->session->flashdata('message'); ?>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?php include 'includes/form_pembelian.php'; ?>
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
</script>