<?php include "includes/header.php"; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Cara Order</h1>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-dashboard"></i> Cara Order
					</li>
				</ol>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?=$this->session->flashdata('message') ?>
                <?= $output->output ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php if($output->output != null) { ?>
    <?php foreach($output->js_files as $file): ?>
        <script src="<?php echo $file; ?>?v=3"></script>
    <?php endforeach; ?>
<?php } ?>