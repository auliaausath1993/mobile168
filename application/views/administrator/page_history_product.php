<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					History Produk <small>dari <?= $product_name ?></small>
				</h1>
			</div>
		</div>
		<input type="hidden" id="product-id" value="<?= $this->uri->segment(4) ?>">
		<div class="panel">
			<div class="panel-body" react-component="HistoryProduct"></div>
		</div>
	</div>
</div>

<?php include 'includes/footer.php'; ?>