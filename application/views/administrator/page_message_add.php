<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Kirim Pesan
				</h1>
				<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
					<li class="active"><a href="<?=base_url()?>administrator/main/message_add" ><b>Kirim Pesan</b></a></li>
					<li><a href="<?=base_url()?>administrator/main/message_to_multiple" ><b>Kirim Pesan (Multiple)</b></a></li>
					<li><a href="<?=base_url()?>administrator/main/message" ><b>History Pesan</b></a></li>
				</ul>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-fw fa-edit"></i>  Kirim Pesan
					</li>
				</ol>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?= $this->session->flashdata('message') ?>
				<?= form_open('administrator/main/message_to_multiple_process', array('role'=>'form','enctype'=>'multipart/form-data')); ?>
				<div class="row">
					<div class="col-md-6">
						<input type="hidden" name="url" value="message_single">
						<p><strong>Penerima</strong></p>
						<input type="hidden" name="customer_id[]" id="customer_id">
						<input type="text" id="autocomplete" class="form-control"><br>
						<p><strong>Subjek</strong></p>
						<input type="text" name="subject" class="form-control" placeholder="Ketik Disini..."><br>
						<p><strong>Gambar</strong></p>
						<input type="file" onchange="readURL(this);" id="image" name="image" accept=".png, .jpg, .jpeg">
						<img id="previewImage"><br>
						<p><strong>Pesan</strong></p>
						<textarea name="content" rows="10" id="content" class="form-control" placeholder="Ketik Disini..."></textarea><br>
						<button type="submit" name="btn_send" class="btn btn-success">Kirim</button>
					</div>
				</div>
				<?= form_close() ?>
			</div>
		</div>
		<br>
	</div>
</div>
<?php include 'includes/footer.php'; ?>
<script type="text/javascript" src='<?= base_url();?>assets/js/jquery.autocomplete.js'></script>
<script type="text/javascript">
	var site = '<?= site_url() ?>';
	$(function() {
		$('#autocomplete').autocomplete({
			serviceUrl: site + 'administrator/main/search_id_customer',
			onSelect: function(suggestion) {
				$('#customer_id').val(suggestion.data);
			}
		});
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
</script>