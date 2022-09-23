<?php include 'includes/header.php';
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting')); ?>

<style>
	.video-wrapper {
		position: relative;
		max-width: 400px;
	}
	.delete-video {
		position: absolute;
		right: 10px;
		top: 25px;
	}
	.stock {
		width: 30% !important;
		margin-right: 10px;
		float: left;
	}
</style>
<?php $uri4 = $this->uri->segment(4); ?>
<input type="hidden" id="data_value_stock" value="<?= $data_value_stock['value'] ?>">
<input type="hidden" id="product-name" value="<?= $product['name_item']; ?>">
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Edit Data <?= $product['name_item']; ?>
				</h1>
				<ol class="breadcrumb">
					<li class="active">
						<?php if ($this->uri->segment(4) == 'ready_stock')  { ?>
							<i class="fa fa-pencil-square-o"></i> Edit Produk Ready Stock
						<?php }elseif ($this->uri->segment(4) == 'pre_order') { ?>
							<i class="fa fa-pencil-square-o"></i> Edit Produk Pre Order
						<?php } ?>
					</li>
				</ol>
				<?php if (validation_errors()) { ?>
					<?= validation_errors()?>
				<?php } ?>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?= $this->session->flashdata('message') ?>
				<form role="form" action="<?= base_url('administrator/main/update_product_process/' . $uri4) ?>" method="post" enctype="multipart/form-data" id="form-update-product" class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-2 control-label">Nama Item*</label>
						<div class="col-lg-8"><input type="text" id="name-item" name ="name-item" class="form-control" title="masukkan nama produk anda" value="<?= $product['name_item']; ?>" required></div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Kategori* : </label>
						<div class="col-lg-8">
							<select id="category-select" class="form-control" name="category" title="pilih category product anda" required>
								<option value="">Pilih Category Product Anda</option>
								<?php foreach($category->result() as $category) { ?>
									<option value="<?= $category->id ?>" <?php if($product['category_id'] == $category->id) { echo 'selected="selected" '; } ?> ><?= $category->name ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Harga Modal*</label>
						<div class="col-lg-8"><input type="text" id="harga-modal" name="harga-modal" class="form-control" title="masukkan harga modal anda" value="<?= $product['price_production']; ?>" required></div>
					</div>
					<?php foreach ($customer_type as $row) {
						if ($product_price->num_rows() > 0) {
							$list_price = $this->db->get_where('product_price', array('prod_id' => $product['id'], 'cust_type_id' => $row->id))->row_array();
							if (count($list_price) > 0 ) {
								if ($list_price['old_price'] > 0) {
									$price = $list_price['old_price'];
									$old_price = $list_price['price'];
								} else {
									$price = $list_price['price'];
									$old_price = 0;
								}
							} else {
								$get_price = $this->db->get_where('product_price', array('prod_id' => $product['id']))->result();
								$last = count($get_price) - 1;
								if ($get_price[$last]->old_price > 0) {
									$price = $get_price[$last]->old_price;
									$old_price = $get_price[$last]->price;
								} else {
									$price = $get_price[$last]->price;
									$old_price = 0;
								}
							}
						} else {
							if ($row->id == 1) {
								$price = $product['price'];
								$old_price = 0;
							} else if ($row->id == 2) {
								$price = $product['price_luar'];
								$old_price = 0;
							} else {
								$price = $product['price_luar'];
								$old_price = 0;
							}
						} ?>
						<div class="form-group">
							<label class="col-lg-2 control-label">Harga Jual (<?= $row->name ?>)*</label>
							<div class="col-lg-8"><input type="text" name="price_<?= $row->id ?>" class="form-control" title="masukkan harga jual <?= $row->name ?> anda" value="<?= $price; ?>"></div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Harga Diskon (<?= $row->name ?>)</label>
							<div class="col-lg-8">
								<input id="view_discount_price_<?= $row->id ?>" type="checkbox" class="checkbox" name="view_discount_price_<?= $row->id ?>" value="1" style="display:inline-block; vertical-align:middle;" <?php if($old_price > 0) { echo 'checked="checked"'; }?>>
								<label for="view_discount_price_<?= $row->id ?>">Tampilkan Harga Diskon <?= $row->name ?> (<span style="color:red;">*jika tidak ada diskon lewati kolom ini</span>)</label><br>
								<input type="text" id="harga-diskon-<?= $row->id ?>" name ="harga_diskon_<?= $row->id ?>" class="form-control" title="masukkan harga diskon anda" value="<?= $old_price ?>" style="display: <?= $old_price == 0 ? 'none' : 'block' ?>"><br>
							</div>
						</div>
					<?php } ?>
					<div class="form-group">
						<label class="col-lg-2 control-label">Harga Pembelian Grosir</label>
						<div class="col-lg-10">
							<div class="row">
								<div class="pull-right" style="margin: 0px 20px 10px;">
									<a href="javascript:void(0)" class="btn btn-primary btn-sm" id="add-harga-grosir">
										<i class="fa fa-plus"></i> Tambah Harga
									</a>
								</div>
							</div>
							<div class="row">
								<div class="table-responsive">
									<table class="table table-striped table-grosir">
										<tr style="white-space: nowrap;">
											<th>Qty Awal</th>
											<th>Qty Akhir</th>
											<?php foreach($customer_type as $row) { ?>
												<th>Harga <?= $row->name ?></th>
											<?php } ?>
											<th></th>
										</tr>
										<?php if ($harga_grosir->num_rows() > 0) {
											$no = 1; foreach ($harga_grosir->result() as $harga) { ?>
												<tr class="baris" id="baris-<?= $no ?>">
													<td class="col-lg-8">
														<input type="text" name="qty_awal[]" class="form-control" value="<?= $harga->qty_awal ?>">
													</td>
													<td class="col-lg-8">
														<input type="text" name="qty_akhir[]" class="form-control" value="<?= $harga->qty_akhir ?>">
													</td>
													<?php foreach ($customer_type as $row) {
														$price_grosir = $this->main_model->get_detail('harga_grosir',array(
															'prod_id'      => $product['id'],
															'cust_type_id' => $row->id,
															'qty_awal'     => $harga->qty_awal
														))?>
														<td class="col-lg-8">
															<input type="text" name="price_grosir_<?= $row->id?>[]" class="form-control" value="<?= $price_grosir['price'] ?>">
														</td>
													<?php } ?>
													<td class="col-lg-8">
														<a href="javascript:void(0)" class="btn btn-danger btn-sm hapus-baris" baris="<?= $no++ ?>">
															<i class="fa fa-times"></i> Hapus Harga
														</a>
													</td>
												</tr>
											<?php }
										} ?>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Berat*</label>
						<div class="col-lg-8">
							<input type="text" id="berat-product" name="berat-product" class="form-control" title="masukkan berat produk anda" value="<?= $product['weight']; ?>" required><br>
							<span>*Berat dalam format Kg<strong>(hanya angka saja)</strong>, example: 0.1</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Deskripsi</label>
						<div class="col-lg-8">
							<textarea id="deskripsi" name="deskripsi" class="form-control" title="masukkan deskripsi produk anda" maxlength="500" required><?= $product['description']; ?></textarea><br>
							<span>*Maksimal menggunakan <strong>500 Character</strong></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Minimal Order*</label>
						<div class="col-lg-8">
							<input type="text" id="minimal-order" name="minimal-order" class="form-control" title="masukkan minimal order produk anda" value="<?= $product['min_order']; ?>" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-12">
							<div class="alert bg-warning" style="margin-bottom:0;">
								<span>Gunakan Button dibawah ini untuk mengunggah Gambar. Note : hindari penggunaan karakter Titik ( . ) , Kurung () , dan Koma ( , ) pada nama File Gambar Anda, simbol yang kami sarankan antara lain <strong>Add @ , Strip - , dan Underscore _  </strong>. </span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Upload Image</label>
						<div class="col-lg-8" id="foto">
							<div id="fine-uploader-gallery"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Upload Video</label>
						<div class="col-lg-8">
							<div class="upload-video" style="display: <?= $product['video'] ? 'none' : 'block' ; ?>">
								<input type="file" name="video" id="video-file" accept="video/*">
								<video width="400" controls id="video-preview" style="display: none"></video>
								<p class="help-block">Durasi video maksimal 30 detik</p>
							</div>
							<div class="video-wrapper" style="display: <?= $product['video'] ? 'block' : 'none' ; ?>">
								<video class="m-t" style="max-width: 400px; width: 100%" src="<?= base_url('media/videos/' . $product['video']) ?>" controls></video>
								<button title="Hapus" type="button" class="btn btn-danger delete-video"><i class="fa fa-trash-o"></i></button>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Variant*</label>
						<div class="col-lg-8" react-component="Variants"></div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Produk Tag : </label>
						<div class="col-lg-8">
							<?php foreach ($tags as $tag) { ?>
							<label class="checkbox-inline">
								<input type="checkbox" <?= in_array($tag->id, $product_tags) ? 'checked' : '' ?> name="tag[]" value="<?= $tag->id ?>"><?= $tag->name ?>
							</label>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Notifikasi* : </label>
						<div class="col-lg-8">
							<select id="notif-select"  name="notif-select" class="form-control">
								<option value="Tidak"<?php if($this->session->flashdata('notif-select') == 'Tidak') { echo 'selected="selected" '; } ?>>Tidak</option>
								<option value="Ya"<?php if($this->session->flashdata('notif-select') == 'Ya') { echo 'selected="selected" '; } ?>>Ya</option>
							</select>
							<textarea style="width: 75%; margin-top: 15px" rows="3" class="form-control hidden" id="value-notif" name="value_notif" placeholder="Text untuk notifikasi"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Status* : </label>
						<div class="col-lg-8">
							<select id="status-select"  class="form-control" name="status" title="pilih status product anda">
								<option value="Publish" <?php if($product['status'] == "Publish") { echo 'selected="selected" '; } ?> >Publish</option>
								<option value="Unpublish" <?php if($product['status'] == "Unpublish") { echo 'selected="selected" '; } ?> >Unpublish</option>
							</select>
						</div>
					</div>
					<div class="buttons-addproduct-wrapper">
						<input type="hidden" name="id_prod" id="id_prod"  value="<?= $product['id']; ?>" >
						<input type="hidden" name="img_lama" id="img_lama"  value="<?= $product['image']; ?>" >
						<input type="hidden" name="redirect_url" id="redirect_url"  value="" >
						<button type="submit" id="update-product" class="btn btn-lg btn-default m-b-mini">Update</button>
						<a href="#" id="update-back" class="btn btn-lg btn-default m-b-mini">Update and Back to List</a>
						<input type="hidden" name="this_redirect_url" id="this_redirect_url"  value="administrator/main/product/<?= $uri4 == 'ready_stock' ? 'Ready_Stock' : 'PO' ?>/Publish/" >
						<a href="<?=base_url()?>administrator/main/product/<?= $uri4 == 'ready_stock' ? 'Ready_Stock' : 'PO' ?>/Publish/" class="btn btn-lg btn-default m-b-mini">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<button type="button "class="hidden" id="togglePincode"></button>
<button type="button "class="hidden" id="toggleModalStock"></button>
<?php include "includes/footer.php"; ?>
<?php include 'includes/upload_image.php' ?>
<script type="text/javascript">

	$('#deskripsi').on('keyup',function(){
		var maxlength = $(this).attr('maxlength');
		var val = $(this).val();
		var styles = {
			backgroundColor : '#e6584e',
			border: '1px solid #e6584e',
			color: '#fff'
		};

		var styles2 = {
			backgroundColor : '#fff',
			border: '1px solid #ccc',
			color: '#333'
		};

		if (val.length == maxlength) {
			$(this).css(styles);
		} else {
			$(this).css(styles2);
		};
	});

	$('#update-back').click(function() {
		var this_redirect_url = $('#this_redirect_url').val();
		$('#redirect_url').val(this_redirect_url);
		$('#form-update-product').submit();
		return false;
	});



	<?php foreach ($customer_type as $row) { ?>
		$('#view_discount_price_<?= $row->id ?>').click(function () {
			$("#harga-diskon-<?= $row->id ?>").toggle(this.checked);
			if (!this.checked) {
				$("#harga-diskon-<?= $row->id ?>").val(0);
			}
		});
	<?php } ?>

	$(document).on('click', '#add-harga-grosir', function() {
		var no = $('.baris').length + 1;
		var row = '<tr class="baris" id="baris-'+no+'">';
		row += '<td class="col-lg-8"><input type="text" name="qty_awal[]" class="form-control"></td>';
		row += '<td class="col-lg-8"><input type="text" name="qty_akhir[]" class="form-control"></td>';
		<?php foreach ($customer_type as $row) { ?>
			row += '<td class="col-lg-8"><input type="text" name="price_grosir_<?= $row->id ?>[]" class="form-control"></td>';
		<?php } ?>
		row += '<td class="col-lg-8"><a href="javascript:void(0)" class="btn btn-danger btn-sm hapus-baris" baris="'+no+'"><i class="fa fa-times"></i> Hapus Harga</a></td></tr>';

		$('.table-grosir').append(row);
	});

	$('#notif-select').change(function() {
		var value = $(this).val();
		if (value == 'Ya') {
			$('#value-notif').removeClass('hidden');
		} else {
			$('#value-notif').addClass('hidden');
		}
	});

	$(document).on('click', '.hapus-baris', function() {
		var no = $(this).attr('baris');
		$('#baris-'+no).remove();
	});

	$('#fine-uploader-gallery').fineUploader({
		template: 'qq-template-gallery',
		request: {
			endpoint: '<?= base_url('administrator/main/update_image/' . $product['id']) ?>',
		},
		session: {
			endpoint: '<?= base_url('administrator/main/get_foto/' . $product['id']) ?>'
		},
		thumbnails: {
			placeholders: {
				waitingPath: '<?= base_url("application/views/administrator/assets/fine-uploader/placeholders/waiting-generic.png") ?>',
				notAvailablePath: '<?= base_url("application/views/administrator/assets/fine-uploader/placeholders/not_available-generic.png"); ?>'
			}
		},
		autoUpload: false,
		validation: {
			allowedExtensions: ['jpeg', 'jpg', 'png'],
			itemLimit: 10,
			sizeLimit: 512000
		},
		callbacks: {
			onComplete: function (event, id, name, responseJSON) {
				$('#foto').append('<input type="hidden" id="image_'+event+'" name="image_'+event+'" value="'+name.data+'">');
			},
			onSubmitDelete: function(id) {
				var filename = $('#image_'+id).val();
				if (filename !== undefined) {
					this.setDeleteFileParams({
						filename: filename
					}, id);
				} else {
					this.setDeleteFileParams({
						prod_id : 0
					}, id);
				}
			},
			onDeleteComplete: function(id) {
				$('#image_'+id).remove();
				$('.image-file').each(function(idx) {
					$(this).attr('name', 'image_' + idx);
				});
			}
		},
		deleteFile: {
			enabled: true,
			forceConfirm: true,
			endpoint: "<?= base_url('administrator/main/delete_img_produk') ?>",
			method: 'POST'
		}
	});

	$('#trigger-upload').click(function() {
		$('#fine-uploader-gallery').fineUploader('uploadStoredFiles');
	});

	$('.delete-video').click(function() {
		var product_id = <?= $product['id'] ?>;
		if (confirm('Hapus video ?')) {
			$.post(base_url + 'administrator/main/delete_video_produk', { product_id: product_id },function(data) {
				if (data.status == 'Success') {
					$('.video-wrapper').remove();
					$('.upload-video').show();
				}
				alert(data.message);
			}, 'json');
		}
	});

	$('#video-file').change(function() {
		if (this.files[0]) {
			var src = URL.createObjectURL(this.files[0]);
			var video = $('#video-preview');
			video.attr('src', src).show();
			video[0].onloadedmetadata = function() {
				var duration = video[0].duration;
				if (duration > 30) {
					video.removeAttr('src').hide();
					$('#video-file').val('');
					alert('Durasi video maksimal 30 detik');

				}
			}
		}
	});
	$(document).on('click', '.btn-edit-stock, #togglePincode', function() {
		$('#modal-pincode-stock').modal('toggle');
	});

	$(document).on('click', '#toggleModalStock', function() {
		$('#modal-update-stock').modal('toggle');
	});
</script>