<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Kirim Pesan (Multiple)
				</h1>
				<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">

					<li><a href="<?=base_url()?>administrator/main/message_add" ><b>Kirim Pesan</b></a></li>
					<li class="active"><a href="<?=base_url()?>administrator/main/message_to_multiple" ><b>Kirim Pesan (Multiple)</b></a></li>
					<li><a href="<?=base_url()?>administrator/main/message" ><b>History Pesan</b></a></li>
				</ul>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-fw fa-edit"></i>  Kirim Pesan (multiple)
					</li>
				</ol>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?= $this->session->flashdata('message') ?>
				<?= form_open('administrator/main/message_to_multiple_process', array('role' => 'form','enctype' => 'multipart/form-data')); ?>
				<div class="row">
					<div class="col-md-6">
						<p><strong>Subjek</strong></p>
						<input type="hidden" name="url" value="message_multiple">
						<input type="text" name="subject"  class="form-control" placeholder="Ketik Disini..."/><br/>

						<p><strong>Gambar</strong></p>
						<input type="file" onchange="readURL(this);" id="image" name="image" accept=".png, .jpg, .jpeg">
						<img id="previewImage"><br>
						<p><strong>Pesan</strong></p>
						<textarea name="content" rows="10"  class="form-control" placeholder="Ketik Disini..."></textarea><br/>
						<button type="submit" name="btn_send" class="btn btn-success">Kirim</button>
					</div>
					<div class="col-md-6">
						<p><strong>Pilihan</strong></p>
						<div>
							<label class="radio-inline"><input class="tipe_penerima" type="radio" value="customer" name="tipe_penerima" checked>Customer</label>
							<label class="radio-inline"><input class="tipe_penerima" type="radio" value="customer_type" name="tipe_penerima">Customer Category</label>
						</div><br>
						<p><strong>Penerima</strong></p>
						<div class="customer">
							<div class="row">
								<div class="col-md-9">
									<input type="hidden" name="customer_id[]" id="customer_id_0">
									<input type="text" no="0" class="customer_name form-control autocomplete">
								</div>
								<div class="col-md-3">
									<button type="button" class="btn btn-info" id="btn-add-customer">Tambah</button>
								</div>
							</div>
						</div>
						<div class="customer_category" style="display: none;">
							<?php foreach ($customer_categories as $category) { ?>
							<div class="checkbox">
								<label><input type="checkbox" name="customer_category[]" value="<?= $category->id ?>"><?= $category->name ?></label>
							</div>
							<?php } ?>
							<br>
							<button type="button" rel="customer_category" class="btn btn-info select_all">Select All</button>
						</div>
					</div>
				</div>
				<?=form_close()?>
			</div>
		</div>
		<br>
	</div>
</div>

<?php include 'includes/footer.php'; ?>
<script type="text/javascript" src='<?= base_url();?>assets/js/jquery.autocomplete.js'></script>
<script type="text/javascript">
	$('.select_all').click(function() {
		var type = $(this).attr('rel');
		if (type === 'customer') {
			var option = $('#customer_id option');
			option.prop('selected', !option.prop('selected'));
		} else {
			var checkbox = $('.customer_category input');
			checkbox.prop('checked', !checkbox.prop('checked'));
		}
	});
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#previewImage')
				.attr('src', e.target.result)
				.width(200);
			};
			reader.readAsDataURL(input.files[0]);
		}
	}
	$('.tipe_penerima').click(function() {
		var val = $(this).val();
		if (val === 'customer') {
			$('.customer').show();
			$('.customer_category').hide();
		} else {
			$('.customer').hide();
			$('.customer_category').show();
		}
	});

	var site = '<?= base_url() ?>';

	function setAutocomplete() {
		$('.autocomplete').autocomplete({
			serviceUrl: site + 'administrator/main/search_id_customer',
			onSelect: function(suggestion) {
				var no = $(this).attr('no');
				$('#customer_id_' + no).val(suggestion.data);
			}
		});
	}

	setAutocomplete();

	$('#btn-add-customer').click(function() {
		var no = $('.customer_name').length;
		var input = '<div class="row customer-' + no + ' m-t">' +
						'<div class="col-md-9">' +
							'<input type="hidden" name="customer_id[]" id="customer_id_' + no + '">' +
							'<input type="text" no="' + no + '" class="customer_name form-control autocomplete">' +
						'</div>' +
						'<div class="col-md-3">' +
							'<button no="' + no + '" type="button" class="btn btn-danger btn-delete-customer">Hapus</button>' +
						'</div>' +
					'</div>';
		$('.customer').append(input);
		setAutocomplete();
	});

	$(document).on('click', '.btn-delete-customer', function() {
		var no = $(this).attr('no');
		$('.customer-' + no).remove();
	});
</script>