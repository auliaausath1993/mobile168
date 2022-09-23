<?php include 'includes/header.php';
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
$dst = $data_value_stock['value'] ;
$uri4 = $this->uri->segment(4);
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<?php if ($uri4 == 'ready_stock') { ?>
				<h1 class="page-header">
					Data Produk <small>Ready Stock</small>
				</h1>
				<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
					<li class="active"><a href="<?=base_url()?>administrator/main/add_product/ready_stock" ><b>Tambah Produk</b></a></li>
					<li><a href="<?=base_url()?>administrator/main/product/Ready_Stock/Publish/" ><b>Data Produk (Publish)</b></a></li>
					<li><a href="<?=base_url()?>administrator/main/product/Ready_Stock/Unpublish/" ><b>Data Produk (Unpublish)</b></a></li>
				</ul>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-plus"></i> Add Produk Ready Stock
					</li>
				</ol>
				<?php } else if ($uri4 == 'pre_order') { ?>
					<h1 class="page-header">
						Data Produk <small>Pre Order</small>
					</h1>
					<ul id="submenu-container" class="nav nav-tabs" style="margin-bottom: 20px;">
				      	<li class="active"><a href="<?=base_url()?>administrator/main/add_product/pre_order" ><b>Tambah Produk</b></a></li>
				      	<li><a href="<?=base_url()?>administrator/main/product/PO/Publish/" ><b>Data Produk (Publish)</b></a></li>
				    	<li><a href="<?=base_url()?>administrator/main/product/PO/Unpublish/" ><b>Data Produk (Unpublish)</b></a></li>
				    </ul>
					<ol class="breadcrumb">
						<li class="active">
							<i class="fa fa-plus"></i> Add Produk Pre Order
						</li>
					</ol>
				<?php } ?>

				<?php if (validation_errors()) { ?>
					<?=validation_errors()?>
				<?php } ?>
		</div>
    </div>
	<div class="panel">
		<div class="panel-body">
			<?=$this->session->flashdata('message') ?>
			<form role="form" action="<?= base_url('administrator/main/add_product_process/' . $uri4) ?>" method="post" enctype="multipart/form-data" id="form-add-product" class="form-horizontal">
				<div class="form-group">
					<label class="col-lg-2 control-label">Nama Item*</label>
					<div class="col-lg-8">
						<input type="text" id="name-item" name="name-item" class="form-control" title="masukkan nama produk anda" required>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Kategori* : </label>
					<div class="col-lg-8">
						<select id="category-select"  class="form-control" name="category" title="pilih category product anda" required>
							<option value="">Pilih Kategori Product Anda</option>
							<?php foreach($category->result() as $category) : ?>
								<option value="<?= $category->id ?>"><?= $category->name ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Harga Modal*</label>
					<div class="col-lg-8">
						<input type="text" id="harga-modal" name="harga-modal" class="form-control" title="masukkan harga modal anda" required>
					</div>
				</div>
				<?php $no = 1; foreach ($customer_type as $row) { ?>
					<div class="form-group">
						<label class="col-lg-2 control-label">Harga Jual (<?= $row->name ?></label>
						<div class="col-lg-8">
							<input type="text" name="price_<?= $row->id ?>" id="price-<?=$no?>" class="price form-control" title="masukkan harga jual <?= $row->name ?> anda" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">Harga Diskon (<?= $row->name ?>)</label>
						<div class="col-lg-8">

							<input id="view_discount_price_<?= $row->id ?>" type="checkbox" class="checkbox" name="view_discount_price_<?= $row->id ?>" value="1" style="display:inline-block; vertical-align:middle;">
							<label for="<?= 'view_discount_price_' . $row->id ?>">Tampilkan Harga Diskon <?= $row->name ?>(<span style="color:red;">*jika tidak ada diskon lewati kolom ini</span>)</label><br/>
							<input type="text" id="harga-diskon-<?= $row->id ?>" name="harga_diskon_<?= $row->id ?>" class="harga-diskon-<?= $no++ ?> form-control" title="masukkan harga diskon anda"  style="display: none;"><br/>
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
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Berat*</label>
					<div class="col-lg-8">
						<input type="text" id="berat-product" name="berat-product" class="form-control" title="masukkan berat produk anda" required><br/>
						<span>*Berat dalam format Kg<strong> (hanya angka saja)</strong>, contoh: 0.1</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Deskripsi</label>
					<div class="col-lg-8">
						<textarea id="deskripsi" name="deskripsi" class="form-control" title="masukkan deskripsi produk anda" maxlength="500" required></textarea><br/>
						<span>*Maksimal menggunakan <strong>500 Karakter</strong></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Minimal Order*</label>
					<div class="col-lg-8">
						<input type="text" id="minimal-order" name="minimal-order" class="form-control" title="masukkan minimal order produk anda" required>
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
						<input type="file" class="form-control" name="video" id="video-file" accept="video/*">
						<video controls class="m-t" id="video-preview" style="display: none; max-width: 400px; width: 100%"></video>
						<p class="help-block">Durasi video maksimal 30 detik</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Varian*</label>
					<div class="col-lg-8">
						<div class="variant-wrapper row">
							<div class="col-md-12 text-right">
								<button type="button" class="add-variant btn btn-info"><i class="fa fa-plus"></i> Tambah Varian</button>
							</div>
							<div class="col-md-8 variant-list">
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-6">
										<h5><strong>Isikan Varian Anda</strong></h5>
										<input type="hidden" name="jml_input[]" id="jml_input">
										<input type="text" id="variant_1" name="variant_product[]" class="form-control m-b input-variant" title="silahkan masukkan variant produk anda">
									</div>
									<?php if ($data_value_stock['value'] != 3) { ?>
										<div class="col-md-6 col-sm-6 col-xs-6">
											<h5><strong>Isikan Jumlah Stock</strong></h5>
											<input type="number" id="stock_1" name="stock_product[]" class="form-control m-b input-stock" min="0">
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Produk Tag : </label>
					<div class="col-lg-8">
						<?php foreach ($tags as $tag) { ?>
						<label class="checkbox-inline">
							<input type="checkbox" name="tag[]" value="<?= $tag->id ?>"><?= $tag->name ?>
						</label>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Status* : </label>
					<div class="col-lg-8">

						<select id="status-select"  class="form-control" name="status" title="pilih status product anda">
							<option value="Publish">Publish</option>
							<option value="Unpublish">Unpublish</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Notifikasi* : </label>
					<div class="col-lg-8">

						<select id="notif-select"  name="notif-select" class="form-control">
							<option value="Tidak">Tidak</option>
							<option value="Ya">Ya</option>
						</select>
						<textarea style="width: 75%; margin-top: 15px" rows="3" class="form-control hidden" id="value-notif" name="value_notif" placeholder="Text untuk notifikasi"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Supplier* : </label>
					<div class="col-lg-8">

						<select id="supplier" name="supplier" class="form-control">
							<option value="">Pilih Supplier</option>
							<?php foreach ($suppliers as $supplier) { ?>
								<option value="<?= $supplier->id ?>">
									<?= $supplier->nama_supplier ?>
								</option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">No. Invoice*</label>
					<div class="col-lg-8">

						<input type="text" id="no_invoice" name="no_invoice" class="form-control" title="Masukkan nomor invoice">
						<div class="row">
							<label class="control-label col-md-3" style="text-align: left">Jenis Pembelian</label>
							<div class="col-md-3">
								<label class="radio-inline"><input type="radio" name="purchase_status" value="Cash">Cash</label>
								<label class="radio-inline"><input type="radio" name="purchase_status" value="Kredit">Kredit</label>
							</div>
						</div>
					</div>
				</div>
				<div class="buttons-addproduct-wrapper">
					<input type="hidden" name="redirect_url" id="redirect_url"  value="" />
					<button type="submit" id="add-product" class="btn btn-lg btn-default m-b-mini">Save</button>
					<a href="#" id="add-back" class="btn btn-lg btn-default m-b-mini">Save and Back to List</a>
					<?php if ($uri4 == 'ready_stock')  { ?>
						<input type="hidden" name="this_redirect_url" id="this_redirect_url"  value="administrator/main/product/Ready_Stock/Publish/" />
						<a href="<?=base_url()?>administrator/main/product/Ready_Stock/Publish/" class="btn btn-lg btn-default m-b-mini">Cancel</a>
					<?php } else if ($uri4 == 'pre_order') { ?>
						<input type="hidden" name="this_redirect_url" id="this_redirect_url"  value="administrator/main/product/PO/Publish/" />
						<a href="<?=base_url()?>administrator/main/product/PO/Publish/" class="btn btn-lg btn-default m-b-mini">Cancel</a>
					<?php } ?>
				</div>
	    	</form>
		</div>
	</div>
</div>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/upload_image.php' ?>
<script type="text/javascript">


    $('#deskripsi').on('keyup',function(){

        var maxlength = $(this).attr('maxlength');
        var val = $(this).val();
         var styles = {
            backgroundColor : "#e6584e",
            border: "1px solid #e6584e",
            color: "#fff"
            };

        var styles2 = {
            backgroundColor : "#fff",
            border: "1px solid #ccc",
            color: "#333"
            };

          if (val.length == maxlength) {
            $(this).css(styles);
          }else{

            $(this).css(styles2);

          };
    });

	$("#add-back").click(function(){

		var this_redirect_url = $("#this_redirect_url").val();
		$("#redirect_url").val(this_redirect_url);

		$("#form-add-product").submit();

		return false;

	});

	<?php foreach ($customer_type as $row) { ?>
	$('#view_discount_price_<?= $row->id ?>').click(function () {
	    $("#harga-diskon-<?= $row->id ?>").toggle(this.checked);
	});
	<?php } ?>


	var stock_value = '<?=$dst?>';

	if(stock_value == 3){

		    $(".add-variant").click(function(){
		        var no = $(".variant-list").length + 1;
		        var content = '<div class="col-md-12 variant-list coloumn-'+no+'">';
		        content += '<div class="row">';
		        content +='<div class="col-md-4 col-sm-6 col-xs-6">';
		        content +='<input type="hidden" name="jml_input[]">';
		        content += '<input type="text" id="variant_'+no+'" name ="variant_product[]" class="form-control m-b input-variant" title="silahkan masukkan variant produk anda" >';
		        content += '</div>';
		        content +='<div class="col-md-4 col-sm-6 col-xs-6">';
		        content += '<button class="delete-variant btn btn-danger m-b"><i class="fa fa-times"></i> Delete</button>';
		        content += '</div>';
		        content += '</div></div>';

		         $(".variant-wrapper").append(content);
		         $(".coloumn-"+no+" .delete-variant").click(function(){
		         	$(".coloumn-"+no).remove();
		         });
		        return false;
		    });


	}else{

	    $(".add-variant").click(function(){
	        var no = $(".variant-list").length + 1;
	        var content = '<div class="col-md-12 variant-list coloumn-'+no+'">';
	        content += '<div class="row">';
	        content +='<div class="col-md-4 col-sm-6 col-xs-6">';
	        content +='<input type="hidden" name="jml_input[]">';
	        content += '<input type="text" id="variant_'+no+'" name ="variant_product[]" class="form-control m-b input-variant" title="silahkan masukkan variant produk anda" >';
	        content += '</div>';
	        content += '<div class="col-md-4 col-sm-6 col-xs-6">';
	        content += '<input type="number" id="stock_'+no+'" name="stock_product[]" class="form-control m-b input-stock" min="0">';
	        content += '</div>';
	        content +='<div class="col-md-4 col-sm-6 col-xs-6">';
	        content += '<button type="button" class="delete-variant btn btn-danger m-b"><i class="fa fa-times"></i> Delete</a>';
	        content += '</div>';
	        content += '</div></div>';

	         $(".variant-wrapper").append(content);

	         $(".coloumn-"+no+" .delete-variant").click(function(){
	         	$(".coloumn-"+no).remove();
	         });

	        return false;
	    });

	}

	$('#add-harga-grosir').click(function() {
		var no = $('.baris').length + 1;
		var row = '<tr class="baris" id="baris-'+no+'">';
		row += '<td><input type="text" name="qty_awal[]" class="form-control"></td>';
		row += '<td><input type="text" name="qty_akhir[]" class="form-control"></td>';
		<?php foreach ($customer_type as $row) { ?>
		row += '<td><input type="text" name="price_grosir_<?= $row->id ?>" class="form-control"></td>';
		<?php } ?>
		row += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm" id="hapus-baris-'+no+'"><i class="fa fa-times"></i> Hapus Harga</a></td></tr>';

		$('.table-grosir').append(row);

		$('#hapus-baris-'+no).click(function() {
			$('#baris-'+no).remove();
		});
	});

	$('#notif-select').change(function() {
		var value = $(this).val();
		if (value == 'Ya') {
			$('#value-notif').removeClass('hidden');
		} else {
			$('#value-notif').addClass('hidden');
		}
	});

	var i = 0;;
	var price = 0;
	var diskon = 0;

	$('#fine-uploader-gallery').fineUploader({
        template: 'qq-template-gallery',
        request: {
            endpoint: '<?= base_url('administrator/main/upload') ?>',
        },
        thumbnails: {
            placeholders: {
                waitingPath: '<?= base_url("application/views/administrator/assets/fine-uploader/placeholders/waiting-generic.png") ?>',
                notAvailablePath: '<?= base_url("application/views/administrator/assets/fine-uploader/placeholders/not_available-generic.png"); ?>'
            }
        },
        autoUpload: false,
        debug: true,
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'png'],
            itemLimit: 10,
            sizeLimit: 512000 // 50 kB = 50 * 1024 bytes
        },
        callbacks: {
        	onComplete: function (event, id, name, responseJSON) {
        		var i = $('.image-file').length;
        		$('#foto').append('<input type="hidden" class="image-file" id="image_' + event + '" name="image_' + i + '" value="' + name.data + '">');
        	},
        	onSubmitDelete: function(id) {
        		var filename = $('#image_'+id).val();
	            this.setDeleteFileParams({filename: filename}, id);
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
	        endpoint: '<?= base_url('administrator/main/delete_foto') ?>',
	        method: 'POST'
	    }
    });

    $('#trigger-upload').click(function() {
    	$('#fine-uploader-gallery').fineUploader('uploadStoredFiles');
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

	/*var site ="<?=base_url()?>";
	$("#singleupload").uploadFile({
      	url:site+"administrator/main/add_product_prosecess/"+nm,
      	allowedTypes:"png,gif,jpg,jpeg",
      	dragDrop:true,
	    fileName: "image_product",
	    returnType:"json",
	    showDone:false,
	    multiple:false,
		 onSuccess:function(files,data,xhr)
	    {
	       $("#upload-wrapper").html('<img src=\"'+site+'media/images/original/'+data+'\" alt=\"\"/>');
	    },
	    onError: function(files, status, message)
		{

			alert(message);
		}
    });*/

</script>