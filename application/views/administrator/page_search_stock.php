<?php
include "includes/header.php";
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
 ?>

<!-- Content
================================================== -->
<section id="content" >

	<div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Stock <small> Untuk mengetahui stock produk</small>
                        </h1>
                        <h4>Kontrol Stok</h4>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Stok
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row">
                	<div class="col-lg-12">

						<div class="row search-stock-wrapper">

							<div class="col-sm-4">
								<div class="row">
									<div class="col-sm-6" style=" margin-right: -25px;">
										<select  class="view-pages-select" name="view-pages" title=" Pilih jumlah pages yang ingin ditampilkan">
						                  <option value="">--- View Pages ---</option>
						                  <option value="10" <?php if($this->session->userdata('perpage') == 10) { echo 'selected="selected" '; }?>>10 Pages</option>
						                  <option value="20" <?php if($this->session->userdata('perpage') == 20) { echo 'selected="selected" '; }?>>20 Pages</option>
						                  <option value="30" <?php if($this->session->userdata('perpage') == 30) { echo 'selected="selected" '; }?>>30 Pages</option>
						                  <option value="50" <?php if($this->session->userdata('perpage') == 50) { echo 'selected="selected" '; }?>>50 Pages</option>
						                  <option value="all" <?php if($this->session->userdata('perpage') == 1000) { echo 'selected="selected" '; }?>>View All</option>
						                </select>
									</div>
									<div class="col-sm-6">
										<a id="exsport" class="btn btn-success" href="#"><i class="fa fa-file-excel-o"></i> Export Excel</a>
									</div>
								</div>
							</div>

							<div class="search-name-wrapper col-sm-4">
								<form name="form1" id="form1" method="post" action="<?=base_url()?>administrator/main/search_product_session" >
									<input type='text' id='name_product' class="autocomplete_pro input-text col-sm-10" placeholder="Nama Produk..." />
									<input id="id" name='prod_id' class="form-control" type="hidden" readonly>
									<button  type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
								</form>
							</div>
							<div class="search-category-wrapper col-sm-4 text-right">
								<form name="form2" id="form2" method="post" action="<?=base_url()?>administrator/main/search_category_session" >
									<input type='text' id='name_product' class="autocomplete_cat input-text col-sm-10" placeholder="Nama Category Produk..." />
									<input id="cat" name='cat_id' class="form-control" type="hidden" readonly>
									<button  type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
								</form>
							</div>
						</div>

						<?=form_open('administrator/main/stock_update') ?>

						<?=$this->session->flashdata('message') ?>
						<table id="table-exsport" class="table table-bordered table-striped">
							<thead>


								<tr>
									<th width="5%">No</th>
									<th>Produk</th>
									<th width="11%">Status Produk</th>
									<th width="13%">Kategori Produk</th>
									<th width="14%">Warna/Varian</th>
									<?php if ($data_value_stock['value'] != 3) { ?>
									<th width="8%">Keep</th>
									<th width="13%">Dropship</th>
									<th width="5%">Terjual</th>
									<th width="10%">Sisa Stok</th>
									<th></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
							<?php

							$i = 1 * ($offset + 1);

							$total_stock = 0;

							$total_terjual = 0;

							$total_keep = 0;

							$total_dropship = 0;

							foreach($list_stock->result() as $items) :

							$data_product = $this->main_model->get_detail('product',array('id' => $items->prod_id));

							$category_id = $data_product ['category_id'];

							$data_category = $this->main_model->get_detail('product_category',array('id' => $category_id));

							// SUM

							$this->db->select_sum('qty');

							$this->db->where('variant_id',$items->id);

							$this->db->where('order_status !=', 'Cancel');

							$this->db->where('order_payment','Paid');

							$orders_items = $this->db->get('orders_item')->row_array();

							$this->db->select_sum('qty');

							$this->db->where('variant_id',$items->id);

							$this->db->where('order_status','Keep');
							$this->db->where('order_payment','Unpaid');

							$order_keep = $this->db->get('orders_item')->row_array();


							$this->db->select_sum('qty');

							$this->db->where('variant_id',$items->id);

							$this->db->where('order_status','Dropship');
							$this->db->where('order_payment','Unpaid');

							$order_dropship = $this->db->get('orders_item')->row_array();


							$total_terjual = $total_terjual + $orders_items['qty'];
							$total_keep = $total_keep + $order_keep['qty'];

							$total_dropship = $total_dropship + $order_dropship['qty'];

							$total_stock = $total_stock + $items->stock;
							?>
								<tr>
									<td><?=$i ?></td>
									<td><?=$data_product['name_item'] ?></td>
									<td><?=$data_product['status'] ?></td>
									<td><?=$data_category['name'] ?></td>
									<td><?=$items->variant ?></td>
									<?php if ($data_value_stock ['value'] != 3) { ?>

									   <?php if ($order_keep['qty'] == 0) { ?>
									   	<td>
											-
										</td>
									   <?php }else{ ?>
									    <td>
											<?= $order_keep['qty'] ?>
										</td>
									   <?php } ?>

									   <?php if ($order_dropship['qty'] == 0) { ?>
									   	<td>
											-
										</td>
									   <?php }else{ ?>
									    <td>
											<?= $order_dropship['qty'] ?>
										</td>
									   <?php } ?>

									   <?php if ($orders_items['qty'] == 0) { ?>
									   	<td>
											-
										</td>
									   <?php }else{ ?>
									    <td>
											<?= $orders_items['qty'] ?>
										</td>
									   <?php } ?>
										<td>
											<input type="hidden" name="item_id[]"  value="<?=$items->id ?>"/>
											<input type="number" name="stock[]" class="form-control stock-control input-small" id="stock_<?= $items->id?>" placeholder="0" value="<?=$items->stock ?>" readonly />
											<span style="display: none;"><?=$items->stock ?></span>
										</td>
										<td>
											<button type="button" no="<?=$items->id ?>" class="btn btn-primary btn-edit-stock">Edit</button>
										</td>
									<?php } ?>
								</tr>
							<?php $i++; endforeach; ?>
							<?php if ($data_value_stock ['value'] != 3) { ?>
								<tr>
									<th colspan="5">TOTAL</th>
									<th><?= $total_keep; ?></th>
									<th><?= $total_dropship; ?></th>
									<th><?=$total_terjual; ?></th>
									<th><?=$total_stock?></th>
									<th></th>
								</tr>
							<?php } ?>
							</tbody>
						</table>

							<?=$this->pagination->create_links(); ?>

						<p>
							<button type="submit" name="go" class="btn btn-success" >UPDATE STOCK</button>
							<a href="stock" class="btn btn-default"> RESET SEARCH </a>
						</p>
						<?=form_close()?>
                	</div>
                </div>
                <!-- /.row -->
            </div>
    </div>
<?php include "includes/footer.php"; ?>
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.1.min.js"></script> -->
<script type='text/javascript' src='<?php echo base_url();?>assets/js/jquery.autocomplete.js'></script>
 <script type='text/javascript'>
	var site = "<?php echo site_url();?>";

	$('.view-pages-select').change(function(){
		var base_url = "<?=base_url()?>";
    	var view_pages =  $('.view-pages-select :selected').val();

    	$.ajax({
		    url : base_url+"administrator/main/search_product_session",
		    type: "POST",
		    data: {
		            view_pages: view_pages
		        },
		    success: function(data)
		    {
		    	window.location = base_url+"administrator/main/search_product_stock";
		    }
		});

    });

	$(function(){
		$('.autocomplete_pro').autocomplete({
			serviceUrl: site+'administrator/main/search_product_id',
			onSelect: function (suggestion) {
				document.form1.id.value = suggestion.data;
				var prod_id = $('#id').val();
				if (prod_id != 0) {

					$('#form1 button').attr("disabled", false);

				};

			}
		});

		$('.autocomplete_cat').autocomplete({
			serviceUrl: site+'administrator/main/search_category_id',
			onSelect: function (suggestion) {
				document.form2.cat.value = suggestion.data;
				var cat_pro = $('#cat').val();
				if (cat_pro != 0) {

					$('#form2 button').attr("disabled", false);

				};
			}
		});
	});

	var prod_id = $('#id').val();
	if (prod_id == 0) {

		$('#form1 button').attr("disabled", true);

	};
	var cat_pro = $('#cat').val();
	if (cat_pro == 0) {

		$('#form2 button').attr("disabled", true);

	};

	$('.btn-edit-stock').click(function() {
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
				var newStock = '<input type="number" name="stock[]" class="form-control" min="0" placeholder="Stok baru" required>';
				$('#stock_' + no).after(newStock);
				$('#stock_' + no).removeAttr('name');
			} else {
				alert('Pincode salah');
			}
		}, 'json');
    });

</script>