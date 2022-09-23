<?php include "includes/header.php";

$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));

 ?>



<!-- Content



================================================== -->



<div id="page-wrapper">





    <div class="container-fluid">



	   <!-- Page Heading -->



	    <div class="row">



	        <div class="col-lg-12">



	            <?php if ($this->uri->segment(4) == 'ready_stock') { ?>

		    		<h1 class="page-header">

		        		Data Produk <small>Ready Stock</small>

		        	</h1>

		        	<ul id="submenu-container" class="nav nav-tabs" style="margin-bottom: 20px;">

	                  	<li class="active"><a href="<?=base_url()?>administrator/main/add_product/ready_stock" ><b>Tambah Produk </b></a></li>

	                  	<li><a href="<?=base_url()?>administrator/main/product/Ready_Stock/Publish/" ><b>Data Produk (Publish)</b></a></li>

	                	<li><a href="<?=base_url()?>administrator/main/product/Ready_Stock/Unpublish/" ><b>Data Produk (Unpublish)</b></a></li>

	                </ul>

		        	<ol class="breadcrumb">

		        		<li class="active">

		        			<i class="glyphicon glyphicon-plus"></i> Add Produk Ready Stock

		        		</li>

		        	</ol>

		        <?php }elseif ($this->uri->segment(4) == 'pre_order') { ?>

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

		        			<i class="glyphicon glyphicon-plus"></i> Add Produk Pre Order

		        		</li>

		        	</ol>

		        <?php } ?>



	             <?php if (validation_errors()) { ?>

		            <?=validation_errors()?>

		        <?php }else { ?>

		        <?php } ?>



	        </div>



	    </div>



	    <div class="row">

	    	<div class="col-lg-12">

	    		<?=$this->session->flashdata('message') ?>

	    		<?= $image_error ?>

	    		<?php if ($this->uri->segment(4) == 'ready_stock')  { ?>

	    		<form role="form" action="<?= base_url() ?>administrator/main/add_product_process/ready_stock" method="post" enctype="multipart/form-data" id="form-add-product">

		    	<?php }elseif ($this->uri->segment(4) == 'pre_order') { ?>

		    	<form role="form" action="<?= base_url() ?>administrator/main/add_product_process/pre_order" method="post" enctype="multipart/form-data" id="form-add-product">

		    	<?php } ?>

		    		<div class="table-responsive add-product-wrapper">

					  <table class="table table-bordered table-striped">

					  	<tbody>

					  		<tr>

					  			<td width="25%">Nama Item*</td>

					  			<td><input type="text" id="name-item" name ="name-item" class="input-text" title="masukkan nama produk anda" value="<?php echo set_value('name-item'); ?>"></td>

					  		</tr>

					  		<tr>

					  			<td width="25%">Kategori* : </td>

					  			<td>

					  				<select id="category-select"  class="select-text" name="category" title="pilih category product anda">

					                  	<option value="">Pilih Category Product Anda</option>

					                  	<?php foreach($category->result() as $category) : ?>

											<option value="<?= $category->id ?>" <?php if(set_value('category') == $category->id) { echo 'selected="selected" '; } ?>><?= $category->name ?></option>

										<?php endforeach; ?>

					                </select>

					  			</td>

					  		</tr>

					  		<tr>

					  			<td width="25%">Harga Modal*</td>

					  			<td><input type="text" id="harga-modal" name ="harga-modal" class="input-text" title="masukkan harga modal anda" value="<?php echo set_value('harga-modal'); ?>" ></td>

					  		</tr>

					  		<?php $no=1; foreach($customer_type as $row) { ?>
					  		<tr>
					  			<td width="25%">Harga Jual (<?= $row->name ?>)*</td>
					  			<td><input type="text" name ="price_<?= $row->id ?>" id="price-<?=$no?>" class="price input-text" title="masukkan harga jual <?= $row->name ?> anda" value="<?= set_value('price_'.$row->id) ?>" ></td>
					  		</tr>
					  		<tr>
					  			<td width="25%">Harga Diskon (<?= $row->name ?>)</td>
					  			<td>
					  				<input id="view_discount_price_<?= $row->id ?>" type="checkbox" class="checkbox" name="view_discount_price_<?= $row->id ?>" value="1" style="display:inline-block; vertical-align:middle;" <?php if($this->session->flashdata('view_discount_price_'.$row->id) != null ) { echo 'checked="checked"'; }?>>
					  				<label>Tampilkan Harga Diskon <?= $row->name ?>(<span style="color:red;">*jika tidak ada diskon lewati kolom ini</span>)</label><br/>
					  				<input type="text" id="harga-diskon-<?= $row->id ?>" name ="harga_diskon_<?= $row->id ?>" class="harga-diskon-<?= $no++ ?> input-text" title="masukkan harga diskon anda" value="<?=$this->session->flashdata('harga_diskon_'.$row->id) ?>" hidden><br/>
					  			</td>
					  		</tr>
					  		<?php } ?>

					  		<tr>
					  			<td colspan="2" style="max-width: 200px; overflow: auto">
					  				<label>Harga Pembelian Grosir</label>
					  				<div class="pull-right" style="margin: 0px 20px 10px;">
						  				<a href="javascript:void(0)" class="btn btn-primary btn-sm" id="add-harga-grosir">
				  							<i class="fa fa-plus"></i> Tambah Harga
				  						</a>
				  					</div>
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
					  			</td>
					  		</tr>

					  		<tr>

					  			<td width="25%">Berat*</td>

					  			<td>

					  				<input type="text" id="berat-product" name ="berat-product" class="input-text" title="masukkan berat produk anda" value="<?php echo set_value('berat-product'); ?>"  ><br/>

					  				<span>*Berat dalam format Kg<strong>(hanya angka saja)</strong>, example: 0.1</span>

					  			</td>

					  		</tr>

					  		<tr>

					  			<td width="25%">Deskripsi</td>

					  			<td>

					  				<textarea id="deskripsi" name ="deskripsi" class="input-text" title="masukkan deskripsi produk anda" maxlength="500" ><?php echo set_value('deskripsi'); ?></textarea><br/>

					  				<span>*Maksimal menggunakan <strong>500 Character</strong></span>

					  			</td>

					  		</tr>

					  		<tr>

					  			<td width="25%">Minimal Order*</td>

					  			<td>

					  				<input type="text" id="minimal-order" name ="minimal-order" class="input-text" title="masukkan minimal order produk anda" value="<?php echo set_value('minimal-order'); ?>" >

					  			</td>

					  		</tr>



					  		<tr>

					  			<td colspan="8">

					  				<div class="alert bg-warning" style="margin-bottom:0;">

					  					<span>Gunakan Button dibawah ini untuk mengunggah Gambar. Note : hindari penggunaan karakter Titik ( . ) , Kurung () , dan Koma ( , ) pada nama File Gambar Anda, simbol yang kami sarankan antara lain <strong>Add @ , Strip - , dan Underscore _ </strong>. </span>

					  				</div>

					  			</td>

					  		</tr>

					  		<tr>
					  			<td>Upload Foto</td>
					  			<td id="foto">
					  				<div id="fine-uploader-gallery"></div>
					  			</td>
					  		</tr>

					  		

					  		<tr>

					  			<td width="25%">Variant*</td>

					  			<td>

					  				<div class="variant-wrapper">

					  					<div class="col-md-8 variant-list">

						  					<div class="row">

						  						<div class="col-md-6">

								  					<h5><strong>Isikan varian anda</strong></h5>

								  					<input type="hidden" name="jml_input[]" id="jml_input">

								  					<input type="text" id="variant_1" name ="variant_product[]" class="input-text input-variant" title="silahkan masukkan variant produk anda">

								  				</div>

								  				<?php if ($data_value_stock['value'] != 3) { ?>

								  				<div class="col-md-6">

								  					<h5><strong>Isikan jumlah Stock</strong></h5>

								  					<input type="number" id="stock_1" name="stock_product[]" class="input-text input-stock" min="0">

								  				</div>

								  				<?php } ?>

						  					</div>

						  				</div>

						  				<div class="col-md-4 text-right">

						  					<a href="#" class="add-variant btn btn-info"><i class="glyphicon glyphicon-plus"></i> Add Variant</a>

						  				</div>

					  				</div>

					  				<div class="clearfix"></div>

					  			</td>

					  		</tr>

					  		<tr>
					  			<td width="25%">Promo : </td>
					  			<td>
					  				<select class="select-text" name="promo" title="Status promo product anda">
											<option value="Tidak"<?php if(set_value('promo') == 'Tidak') { echo 'selected="selected" '; } ?>>Tidak</option>
											<option value="Ya"<?php if(set_value('promo') == 'Ya') { echo 'selected="selected" '; } ?>>Ya</option>
					                </select>
					  			</td>
					  		</tr>
					  		<tr>
					  			<td width="25%">Best Seller : </td>
					  			<td>
					  				<select class="select-text" name="best_seller" title="Status Best Seller product anda">
											<option value="Tidak"<?php if(set_value('best_seller') == 'Tidak') { echo 'selected="selected" '; } ?>>Tidak</option>
											<option value="Ya"<?php if(set_value('best_seller') == 'Ya') { echo 'selected="selected" '; } ?>>Ya</option>
					                </select>
					  			</td>
					  		</tr>

					  		<tr>

					  			<td width="25%">Status* : </td>

					  			<td>

					  				<select id="status-select"  class="select-text" name="status" title="pilih status product anda">

											<option value="">Pilih Status Produk Anda</option>

											<option value="Publish"<?php if(set_value('status') == 'Publish') { echo 'selected="selected" '; } ?>>Publish</option>

											<option value="Unpublish"<?php if(set_value('status') == 'Unpublish') { echo 'selected="selected" '; } ?>>Unpublish</option>

					                </select>

					  			</td>

					  		</tr>

					  		<tr>
					  			<td width="25%">Notifikasi* : </td>
					  			<td>
					  				<select id="notif-select"  name="notif-select" class="select-text">
										<option value="Tidak"<?php if($this->session->flashdata('notif-select') == 'Tidak') { echo 'selected="selected" '; } ?>>Tidak</option>
										<option value="Ya"<?php if($this->session->flashdata('notif-select') == 'Ya') { echo 'selected="selected" '; } ?>>Ya</option>
					                </select>
					                <textarea style="width: 75%; margin-top: 15px" rows="3" class="input-text hidden" id="value-notif" name="value_notif" placeholder="Text untuk notifikasi"></textarea>
					  			</td>
					  		</tr>

					  	</tbody>

					  </table>

					</div>

					<div class="buttons-addproduct-wrapper">





						<input type="hidden" name="redirect_url" id="redirect_url"  value="" />

						<button type="submit" id="add-product" class="btn btn-lg btn-default">Save</button>

						<a href="#" id="add-back" class="btn btn-lg btn-default">Save and Back to List</a>

						<?php if ($this->uri->segment(4) == 'ready_stock')  { ?>

							<input type="hidden" name="this_redirect_url" id="this_redirect_url"  value="administrator/main/product/Ready_Stock/Publish/" />	

	                    	<a href="<?=base_url()?>administrator/main/product/Ready_Stock/Publish/" class="btn btn-lg btn-default">Cancel</a>

	                    <?php }elseif ($this->uri->segment(4) == 'pre_order') { ?>

	                    	<input type="hidden" name="this_redirect_url" id="this_redirect_url"  value="administrator/main/product/PO/Publish/" />	

	                    	<a href="<?=base_url()?>administrator/main/product/PO/Publish/" class="btn btn-lg btn-default">Cancel</a>

	                    <?php } ?>

	                </div>

            	</form>

	    	</div>

	    </div>



	    <!-- /.row -->

	



    </div>



<?php include "includes/footer.php"; ?>
<?php include 'includes/upload_image.php' ?>

<script type="text/javascript">

	$('#notif-select').change(function() {
		var value = $(this).val();
		if (value == 'Ya') {
			$('#value-notif').removeClass('hidden');
		} else {
			$('#value-notif').addClass('hidden');
		}
	});

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



	var stock_value = '<?php echo $data_value_stock["value"] ?>';



	if(stock_value == 3){



		    $(".add-variant").click(function(){

		        var no = $(".variant-list").length + 1;

		        var content = '<div class=\"col-md-12 variant-list coloumn-'+no+'\">';

		        content += '<div class=\"row\">';

		        content +='<div class=\"col-md-4\">';

		        content +='<input type=\"hidden\" name=\"jml_input[]\">';

		        content += '<input type=\"text\" id=\"variant_'+no+'\" name =\"variant_product[]\" class=\"input-text input-variant\" title=\"silahkan masukkan variant produk anda\" >';

		        content += '</div>';

		        content +='<div class=\"col-md-4\">';

		        content += '<button class=\"delete-variant btn btn-danger\"><i class=\"fa fa-times\"></i> Delete</button>';

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

	        var content = '<div class=\"col-md-12 variant-list coloumn-'+no+'\">';

	        content += '<div class=\"row\">';

	        content +='<div class=\"col-md-4\">';

	        content +='<input type=\"hidden\" name=\"jml_input[]\">';

	        content += '<input type=\"text\" id=\"variant_'+no+'\" name =\"variant_product[]\" class=\"input-text input-variant\" title=\"silahkan masukkan variant produk anda\" >';

	        content += '</div>';

	        content += '<div class=\"col-md-4\">';

	        content += '<input type=\"number\" id=\"stock_'+no+'\" name=\"stock_product[]\" class=\"input-text input-stock\" min=\"0\">';

	        content += '</div>';

	        content +='<div class=\"col-md-4\">';

	        content += '<button class=\"delete-variant btn btn-danger\"><i class=\"fa fa-times\"></i> Delete</button>';

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
		row += '<td><input type="text" name="qty_awal[]" class="input-text"></td>';
		row += '<td><input type="text" name="qty_akhir[]" class="input-text"></td>';
		<?php foreach ($customer_type as $row) { ?>
		row += '<td><input type="text" name="price_grosir_<?= $row->id ?>" class="input-text"></td>';
		<?php } ?>
		row += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm" id="hapus-baris-'+no+'"><i class="fa fa-times"></i> Hapus Harga</a></td></tr>';

		$('.table-grosir').append(row);

		$('#hapus-baris-'+no).click(function() {
			$('#baris-'+no).remove();
		});
	});

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
        		$('#foto').append('<input type="hidden" id="image_'+event+'" name="image_'+event+'" value="'+name.data+'">');
        	},
        	onSubmitDelete: function(id) {
        		var filename = $('#image_'+id).val();
	            this.setDeleteFileParams({filename: filename}, id);
	        },
	        onDeleteComplete: function(id) {
	        	$('#image_'+id).remove();
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