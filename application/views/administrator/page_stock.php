<?php include 'includes/header.php';
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting')); ?>

<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Stok <small> Untuk mengetahui stok produk</small>
			</h1>
			<h4>Kontrol Stok</h4>
			<ol class="breadcrumb">
				<li class="active">
					<i class="fa fa-dashboard"></i> Stok
				</li>
			</ol>
		</div>
	</div>
	<input type="hidden" id="data_value_stock" value="<?= $data_value_stock['value'] ?>">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel" react-component="Stock"></div>
		</div>
	</div>
	<button class="hidden" id="togglePincode"></button>
	<button class="hidden" id="toggleModalStock"></button>
</div>
<?php include "includes/footer.php"; ?>
<script type='text/javascript' src='<?php echo base_url();?>assets/js/jquery.autocomplete.js'></script>
 <script type='text/javascript'>
	var site = "<?php echo site_url();?>";

	$(document).on('click', '.btn-edit-stock, #togglePincode', function() {
		$('#modal-pincode-stock').modal('toggle');
	});

	$(document).on('click', '#toggleModalStock', function() {
		$('#modal-update-stock').modal('toggle');
	});

	$('#submit-pincode').click(function() {
		var code = $('#pincode').val();
		$('.form-pincode').hide();
		$('.loading-pincode').show();
		$.post(base_url + 'administrator/main/check_pincode', { code: code }, function(data) {
			$('.form-pincode').show();
			$('.loading-pincode').hide();
			if (data.status == 'Success') {
				$('#modal-pincode').modal('hide');
				var no = $('#var-no').val();
				$('#pincode').val('');
				var newStock = '<input type="number" name="stock[]" class="form-control" min="0" placeholder="Stok baru" required>';
				$('#stock_' + no).after(newStock);
				$('#stock_' + no).removeAttr('name');
			} else {
				alert('Pincode salah');
			}
		}, 'json');
	});
</script>