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

		            <?=validation_errors()?>

		        <?php }else { ?>

		        <?php } ?>



	        </div>



	    </div>



	    <div class="row">

	    	<div class="col-lg-12">

	    		<?php if ($this->uri->segment(4) == 'ready_stock')  { ?>

	    		<form role="form" action="<?= base_url() ?>administrator/main/update_product_process/ready_stock" method="post" enctype="multipart/form-data" id="form-update-product">

		    	<?php }elseif ($this->uri->segment(4) == 'pre_order') { ?>

		    	<form role="form" action="<?= base_url() ?>administrator/main/update_product_process/pre_order" method="post" enctype="multipart/form-data" id="form-update-product">

		    	<?php } ?>

		    		<div class="table-responsive add-product-wrapper">

					  <table class="table table-bordered table-striped">

					  	<tbody>

					  		<tr>

					  			<td width="25%">Nama Item*</td>

					  			<td><input type="text" id="name-item" name ="name-item" class="input-text" title="masukkan nama produk anda" value="<?php echo set_value('name-item'); ?>" ></td>

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
						  		}?>
						  			<tr>
							  			<td width="25%">Harga Jual (<?= $row->name ?>)*</td>
							  			<td><input type="text" name="price_<?= $row->id ?>" class="input-text" title="masukkan harga jual <?= $row->name ?> anda" value="<?= $price; ?>"></td>
							  		</tr>
							  		<tr>
							  			<td width="25%">Harga Diskon (<?= $row->name ?>)</td>
							  			<td>
							  				<input id="view_discount_price_<?= $row->id ?>" type="checkbox" class="checkbox" name="view_discount_price_<?= $row->id ?>" value="1" style="display:inline-block; vertical-align:middle;" <?php if($old_price > 0) { echo 'checked="checked"'; }?>>
							  				<label>Tampilkan Harga Diskon <?= $row->name ?> (<span style="color:red;">*jika tidak ada diskon lewati kolom ini</span>)</label><br/>
							  				<input type="text" id="harga-diskon-<?= $row->id ?>" name ="harga_diskon_<?= $row->id ?>" class="input-text" title="masukkan harga diskon anda" value="<?= $old_price ?>" <?php if($old_price == 0) { echo 'hidden'; } ?>><br/>
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
			  					<?php if ($harga_grosir->num_rows() > 0) {
			  						$no = 1; foreach ($harga_grosir->result() as $harga) { ?>
			  							<tr class="baris" id="baris-<?= $no ?>">
			  								<td>
			  									<input type="text" name="qty_awal[]" class="input-text" value="<?= $harga->qty_awal ?>">
			  									</td>
			  								<td>
			  									<input type="text" name="qty_akhir[]" class="input-text" value="<?= $harga->qty_akhir ?>">
			  								</td>
			  								<?php foreach($customer_type as $row) {
			  									$price_grosir = $this->main_model->get_detail('harga_grosir',array(
			  										'prod_id' => $product['id'],
			  										'cust_type_id' => $row->id,
			  										'qty_awal' => $harga->qty_awal
			  									))?>
			  								<td>
			  									<input type="text" name="price_grosir_<?= $row->id?>[]" class="input-text" value="<?= $price_grosir['price'] ?>">
			  								</td>
			  								<?php } ?>
			  								<td>
			  									<a href="javascript:void(0)" class="btn btn-danger btn-sm hapus-baris" baris="<?= $no++ ?>">
			  										<i class="fa fa-times"></i> Hapus Harga
			  									</a>
			  								</td>
			  							</tr>
			  					<?php }
			  					} ?>
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
					  			<td>Upload Image*</td>
					  			<td id="foto">
					  				<div id="fine-uploader-gallery"></div>
					  			</td>
					  		</tr>

					  		<tr>

					  			<td width="25%">Variant*</td>

					  			<td>

					  				<div class="variant-wrapper">

					  					<div class="col-md-8 texthelper">

						  					<div class="row">

						  						<div class="col-md-6">

								  					<h5><strong>Isikan varian anda</strong></h5>

								  				</div>

								  				<?php if ($data_value_stock['value'] != 3) { ?>

								  				<div class="col-md-6">

								  					<h5><strong>Isikan jumlah Stock</strong></h5>

								  				</div>

								  				<?php } ?>

						  					</div>

						  				</div>

						  				<div class="col-md-4 text-right">

						  					<a href="#" class="add-variant-update btn btn-info"><i class="glyphicon glyphicon-plus"></i> Add Variant</a>

						  				</div>

						  				<?php $i = 1; foreach($variant_pro->result() as $variant) : ?>

						  				

						  				<?php if ($i != 1) { ?>



							  				<div class="col-md-12 variant-list column-<?= $variant->id; ?>">

							  					<div class="row">

							  						<div class="col-md-4">

							  							<input id="jml_input_<?=$i ?>" type="hidden" name="jml_input[]">

							  							<input type="hidden" name="id_variant[]" id="id_variant_<?= $variant->id; ?>" class="number_variant_<?= $variant->id; ?>"  value="<?= $variant->id; ?>" >

							  							<input type="text" id="variant_<?=$i ?>" name ="variant_product[]" class="input-text input-variant" title="silahkan masukkan variant produk anda" value="<?= $variant->variant; ?>" >

									  				</div>

									  				<?php if ($data_value_stock['value'] != 3) { ?>

									  				<div class="col-md-5">

									  					<input type="number" id="stock_<?=$i ?>" name="stock_product[]" class="input-text input-stock" min="0" readonly value="<?= $variant->stock; ?>" style="width: 37% !important; margin-right: 10px;">
														<button type="button" class="btn btn-primary btn-edit-var edit-var-<?= $i ?>" no="<?= $i ?>">Edit</button>
									  				</div>

									  				<?php } ?>

									  				<div class="col-md-3">

									  					<a class="delete-variant-<?= $variant->id; ?> btn btn-danger"><i class="fa fa-times"></i> Delete</a>

									  				</div>

									  				<script type="text/javascript">

									  					$('.delete-variant-<?= $variant->id; ?>').click(function () {

														    var id_variant = $('.number_variant_<?= $variant->id; ?>').val();

														    var base_url = "<?= base_url() ?>";

														    $.ajax({

															    url : base_url+"administrator/main/delete_variant/<?= $variant->id ?>",

															    type: "POST",

															    data: {

															            id_variant: id_variant

															        },

															    success: function(data)

															    {

															        $('.column-<?= $variant->id; ?>').remove();

															    	$( ".variant-list" ).focus();

															    }

															});

														});

									  				</script>

							  					</div>

							  				</div>

							  			<?php }else{ ?>

							  				<div class="col-md-12 variant-list">

							  					<div class="row">

							  						<div class="col-md-4">

							  							<input id="jml_input_<?=$i ?>" type="hidden" name="jml_input[]">

							  							<input type="hidden" name="id_variant[]" id="id_variant_<?= $variant->id; ?>" class="number_variant_<?= $variant->id; ?>"  value="<?= $variant->id; ?>" >

							  							<input type="text" id="variant_<?=$i ?>" name ="variant_product[]" class="input-text input-variant" title="silahkan masukkan variant produk anda" value="<?= $variant->variant; ?>" >

									  				</div>

									  				<?php if ($data_value_stock['value'] != 3) { ?>

									  				<div class="col-md-6">

									  					<input type="number" id="stock_<?=$i ?>" name="stock_product[]" class="input-text input-stock" min="0" readonly value="<?= $variant->stock; ?>" style="width: 30% !important; margin-right: 10px;">

									  					<button type="button" class="btn btn-primary btn-edit-var edit-var-<?= $i ?>" no="<?= $i ?>">Edit</button>

									  				</div>

									  				<?php } ?>

							  					</div>

							  				</div>

							  			<?php } ?>

						  				<?php $i++; endforeach; ?>

					  				</div>

					  				<div class="clearfix"></div>

					  			</td>

					  		</tr>
					  		<tr>
					  			<td width="25%">Promo : </td>
					  			<td>
					  				<select class="select-text" name="promo" title="Status promo product anda">
											<option value="Tidak"<?php if($product['promo'] == 'Tidak') { echo 'selected="selected" '; } ?>>Tidak</option>
											<option value="Ya"<?php if($product['promo'] == 'Ya') { echo 'selected="selected" '; } ?>>Ya</option>
					                </select>
					  			</td>
					  		</tr>

					  		<tr>
					  			<td width="25%">Best Seller : </td>
					  			<td>
					  				<select class="select-text" name="best_seller" title="Status Best Seller product anda">
											<option value="Tidak"<?php if($product['best_seller'] == 'Tidak') { echo 'selected="selected" '; } ?>>Tidak</option>
											<option value="Ya"<?php if($product['best_seller'] == 'Ya') { echo 'selected="selected" '; } ?>>Ya</option>
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

					  		<tr>

					  			<td width="25%">Status* : </td>

					  			<td>

					  				<select id="status-select"  class="select-text" name="status" title="pilih status product anda">

											<option value="">Pilih Status Produk Anda</option>

											<option value="Publish" <?php if($product['status'] == "Publish") { echo 'selected="selected" '; } ?> >Publish</option>

											<option value="Unpublish" <?php if($product['status'] == "Unpublish") { echo 'selected="selected" '; } ?> >Unpublish</option>

					                </select>

					  			</td>

					  		</tr>

					  	</tbody>

					  </table>

					</div>

					<div class="buttons-addproduct-wrapper">



						<input type="hidden" name="id_prod" id="id_prod"  value="<?= $product['id']; ?>" />

						<input type="hidden" name="img_lama" id="img_lama"  value="<?= $product['image']; ?>" />

						<input type="hidden" name="redirect_url" id="redirect_url"  value="" />

						<button type="submit" id="update-product" class="btn btn-lg btn-default">Update</button>

						<a href="#" id="update-back" class="btn btn-lg btn-default">Update and Back to List</a>

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



	var base_url = '<?php echo base_url(); ?>';

	$('.delete-img').click(function(event){		
		var urutan_id = $(this).attr('rel');		
		var title = $(this).attr('title');    
		var image_id = $('#img_id_'+urutan_id).val();		
		//alert(base_url); 
		$.post(base_url+"administrator/main/delete_img_produk", {title: title, image_id: image_id,urutan:urutan_id },		   	function(data){			
			if(data.status == 'Success')			
				{				
					var html_upload = '<div class="col-md-4">'+								
									'<input type="file" class="col-md-12" name="image_'+urutan_id+'" id="input_image">'+
									'</div>';
						$("#upload-wrapper_"+urutan_id).html(html_upload);			
				}
		},"json");	
	});


	$("#update-back").click(function(){



		var this_redirect_url = $("#this_redirect_url").val();

		$("#redirect_url").val(this_redirect_url);



		$("#form-update-product").submit();



		return false;



	});

	<?php foreach ($customer_type as $row) { ?>
	$('#view_discount_price_<?= $row->id ?>').click(function () {
	    $("#harga-diskon-<?= $row->id ?>").toggle(this.checked);
	    if (!this.checked) {
	    	console.log('uncheked');
	    	$("#harga-diskon-<?= $row->id ?>").val(0);
	    } else {
	    	console.log('cheked');
	    }
	});
	<?php } ?>



	var stock_value = '<?php $data_value_stock["value"] ?>';



	if(stock_value == 3){



		$(".add-variant-update").click(function(){

		    var no = $(".variant-list").length + 1;

		    var content = '<div class=\"col-md-12 variant-list coloumn-'+no+'\">';

		    content += '<div class=\"row\">';

		    content +='<div class=\"col-md-4\">';

		    content +='<input type=\"hidden\" name=\"jml_input_update[]\">';

		    content += '<input type=\"text\" id=\"variant_'+no+'\" name =\"variant_product_update[]\" class=\"input-text input-variant\" title=\"silahkan masukkan variant produk anda\" >';

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



		$(".add-variant-update").click(function(){

		    var no = $(".variant-list").length + 1;

		    var content = '<div class=\"col-md-12 variant-list coloumn-'+no+'\">';

		    content += '<div class=\"row\">';

		    content +='<div class=\"col-md-4\">';

		    content +='<input type=\"hidden\" name=\"jml_input_update[]\">';

		    content += '<input type=\"text\" id=\"variant_'+no+'\" name =\"variant_product_update[]\" class=\"input-text input-variant\" title=\"silahkan masukkan variant produk anda\" >';

		    content += '</div>';

		    content += '<div class=\"col-md-4\">';

		    content += '<input type=\"number\" id=\"stock_'+no+'\" name=\"stock_product_update[]\" class=\"input-text input-stock\" min=\"0\">';

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

	$(document).on('click', '#add-harga-grosir', function() {
		var no = $('.baris').length + 1;
		var row = '<tr class="baris" id="baris-'+no+'">';
		row += '<td><input type="text" name="qty_awal[]" class="input-text"></td>';
		row += '<td><input type="text" name="qty_akhir[]" class="input-text"></td>';
		row += '<td><input type="text" name="price_lokal[]" class="input-text"></td>';
		row += '<td><input type="text" name="price_luar[]" class="input-text"></td>';
		row += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm hapus-baris" baris="'+no+'"><i class="fa fa-times"></i> Hapus Harga</a></td></tr>';

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
            endpoint: '<?= base_url('administrator/main/update_image/'.$product['id']) ?>',
        },
        session: {
        	endpoint: '<?= base_url('administrator/main/get_foto/'.$product['id']) ?>'
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
        		if (filename !== undefined) {
	            	this.setDeleteFileParams({
	            		filename: filename,
	            		prod_id : <?= $product['id'] ?>
	            	}, id);
        		} else {
        			this.setDeleteFileParams({
	            		prod_id : 0
	            	}, id);
        		}
	        },
        	// onDelete: function(id) {
	        //     this.setDeleteFileParams(name, id);
	        // },
	        onDeleteComplete: function(id) {
	        	$('#image_'+id).remove();
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

	$('.btn-edit-var').click(function() {
    	var no = $(this).attr('no');
    	$('#var-no').val(no);
    	$('#modal-pincode').modal('show');
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
				if (no == 1) {
					var width = 29;
				} else {
					var width = 35;
				}
				var newStock = '<input type="number" name="stock_product[]" class="input-text input-stock" min="0" style="width: ' + width + '% !important; margin-right: 10px;" placeholder="Stok baru" required>';
				$('.edit-var-' + no).before(newStock);
				$('#stock_' + no).removeAttr('name');
			} else {
				alert('Pincode salah');
			}
		}, 'json');
    });

</script>