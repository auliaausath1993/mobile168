<?php include "includes/header.php"; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Data Supplier
				</h1>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-fw fa-list"></i> Data Supplier
					</li>
				</ol>
			</div>
		</div>
		<?= $this->session->flashdata('message') ?>
		<div id="list-supplier">
			<?= $output->output; ?>
		</div>
	</div>
</div>

<?php include 'includes/footer.php'; ?>

<script type="text/javascript">
	$(document).on('click', '#list-supplier .supplier-delete', function() {
		var x = confirm('Anda yakin ingin menghapus ?');
		return x;
	});
</script>

<?php foreach($output->js_files as $file) { ?>
	<script src="<?= $file; ?>"></script>
<?php } ?>