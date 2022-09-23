<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
	<form role="form" method="post">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">
						Settings Informasi<small> Untuk merubah informasi toko online</small>
					</h1>
					<ol class="breadcrumb">
						<li class="active">
							<i class="fa fa-list"></i> Settings Informasi
						</li>
					</ol>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<?= $this->session->flashdata('message'); ?>
					<?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
					<ul id="myTab" class="nav nav-tabs nav-justified">
						<li class="active"><a href="#discount-settings" data-toggle="tab">
							<b>Pengaturan Diskon</b></a>
						</li>
					</ul>
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane active in" id="discount-settings">
							<div class="panel">
								<div class="panel-body">
									<div class="form-horizontal">
										<div class="form-group">
											<label class="control-label col-lg-2">Status Diskon</label>
											<div class="col-lg-6">
												<label class="radio-inline">
													<input type="radio" value="1" name="status" <?= $status == '1' ? 'checked' : '' ?>>On
												</label>
												<label class="radio-inline">
													<input type="radio" value="0" name="status" <?= $status == '0' ? 'checked' : '' ?>>off
												</label>
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Judul Diskon</label>
											<div class="col-lg-6">
												<input value="<?= $title ?>" type="text" name="title" class="form-control">
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Range Tanggal</label>
											<div class="col-lg-6 form-inline">
												<input autocomplete="off" value="<?= $from_date ?>" type="text" name="from_date" class="datepicker form-control" data-date-format="yyyy-mm-dd"> <b>&mdash;</b> <input autocomplete="off" value="<?= $to_date ?>" type="text" name="to_date" class="datepicker form-control" data-date-format="yyyy-mm-dd">
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Range Qty</label>
											<div class="col-lg-6 form-inline">
												<input min="1" value="<?= $min_qty ?>" type="number" name="min_qty" class="form-control"> <b>&mdash;</b> <input min="2" value="<?= $max_qty ?>" type="number" name="max_qty" class="form-control">
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Tipe Pelanggan</label>
											<div class="col-lg-6">
											<?php foreach ($customer_types as $type) { ?>
												<label class="checkbox-inline">
													<input <?= in_array($type->id, $customer_type_discount) ? 'checked' : '' ?> name="customer_types[]" type="checkbox" value="<?= $type->id ?>">
													<?= $type->name ?>
												</label>
											<?php } ?>
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Produk Tag</label>
											<div class="col-lg-6">
											<?php foreach ($name_tags as $tag) { ?>
												<label class="checkbox-inline">
													<input <?= in_array($tag->id, $tag_discount) ? 'checked' : '' ?> name="name_tags[]" type="checkbox" value="<?= $tag->id ?>">
													<?= $tag->name ?>
												</label>
											<?php } ?>
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Produk Kategori</label>
											<div class="col-lg-10">
											<?php foreach ($categories as $category) { ?>
												<label class="checkbox-inline">
													<input <?= in_array($category->id, $product_category_discount) ? 'checked' : '' ?> name="categories[]" type="checkbox" value="<?= $category->id ?>">
													<?= $category->name ?>
												</label>
											<?php } ?>
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Jumlah Diskon</label>
											<div class="col-lg-3">
												<div class="input-group">
													<input value="<?= $amount ?>" type="text" name="amount" class="form-control">
													<div class="input-group-btn">
														<select class="form-control" name="discount_type" style="border-left: none; width: 95px; border-top-right-radius: 4px; border-bottom-right-radius: 4px">
															<option <?= $discount_type == 'percent' ? 'selected' : '' ?> value="percent">%</option>
															<option <?= $discount_type == 'nominal' ? 'selected' : '' ?> value="nominal">Rupiah</option>
														</select>
													</div>
												</div>
											</div>
											<div class="clearfix"></div>
										</div>
										<button type="submit" class="btn btn-success">Simpan</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php include 'includes/footer.php'; ?>