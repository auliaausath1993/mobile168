<?php include "includes/header.php";
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
							Menu <small> Untuk opname stok</small>
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

							<div class="search-name-wrapper col-sm-4">
								<form name="form1" id="form1" method="post" action="<?=base_url()?>administrator/main/search_product_session_opname" >
									<input type='text' id='name_product' class="form-control autocomplete_pro input-text col-sm-10" placeholder="Nama Produk" style="width: 60%" />
									<input id="id" name='prod_id' class="form-control" type="hidden" readonly>&nbsp;
									<button  type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
								</form>
							</div>
						</div>


						<!-- <form onsubmit="return confirm('Update Opname Stok ?');" action="<?= base_url('administrator/main/stock_update_opname') ?>" method="post"> -->

							<?=$this->session->flashdata('message') ?>						
							<table id="table-exsport" class="table table-bordered table-striped">
								<thead>


									<tr>
										<th>Produk</th>
										<th>Harga Modal</th>
										<th width="11%">Status Produk</th>
										<th width="13%">Kategori Produk</th>
										<th width="14%">Warna/Varian</th>
										<th width="14%">Sisa Stok</th>
									</tr>
								</thead>
								<tbody>
									<?php 

							// $i = 1 * ($offset + 1);

									$total_stock = 0;

									$total_terjual = 0;

									$total_keep = 0;

									$total_dropship = 0;

									foreach($list_item->result() as $items) : 

										$data_product = $this->main_model->get_detail('product',array('id' => $items->prod_id));

										$category_id = $data_product ['category_id'];

										$data_category = $this->main_model->get_detail('product_category',array('id' => $category_id));

							// SUM

										$this->db->select_sum('qty');

										$this->db->where('variant_id',$items->id);

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
											<td><?=$data_product['name_item'] ?></td>
											<td><?=$data_product['price_production'] ?></td>
											<td><?=$data_product['status'] ?></td>
											<td><?=$data_category['name'] ?></td>
											<td class="td-variant"><?=$items->variant ?></td>
											<td class="td-stock" style="display: none;"><?=$items->stock ?></td>
											<td class="td-id" style="display: none;"><?=$items->id ?></td>

											<!-- <td><?=$items->variant ?></td> -->
											<td>
												<input type="hidden" name="item_id[]"  value="<?=$items->id ?>"/>
												<input type="number" name="stock[]" class="form-control stock-control input-small" value="<?=$items->stock ?>" />
												<span style="display: none;"><?=$items->stock ?></span>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>


							<!-- Alert Update Stock -->
							<div class="modal fade" id="alert-modal" tabindex="-1" role="dialog" aria-labelledby="alertModal" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<form action="<?= base_url('administrator/main/stock_update_opname') ?>" method="post">
											<div class="modal-header">
												<h3 class="text-center modal-title">Stock yang Akan diOpname</h3>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body body-alert-modal">
												<table id="table-alert-modal" class="table table-bordered table-striped">
													<tr id="title-table-alert">
														<th>Warna/Varian</th>
														<th>Stok Sekarang</th>
														<th>Opname Stok</th>
													</tr>
												</table>
											</div>
											<div class="modal-footer">
												<button type="submit" name="go" class="btn btn-success">UPDATE</button>
											</div>
										</form>
									</div>
								</div>
							</div>



							<?=$this->pagination->create_links(); ?>

							<p>
								<!-- <button type="submit" name="go" class="btn btn-success" >UPDATE STOK</button> -->
								<button type="button" id="button-alert-modal" class="btn btn-primary" data-toggle="modal" data-target="#alert-modal">
									OPNAME STOK
								</button>
							</p>

						</form>
					</div>
				</div>
				<!-- /.row -->
			</div>
		</div>
		<?php include "includes/footer.php"; ?>
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.1.min.js"></script>
		<script type='text/javascript' src='<?php echo base_url();?>assets/js/jquery.autocomplete.js'></script>
		<script type='text/javascript'>
			var site = "<?php echo site_url();?>";

			$('.view-pages-select').change(function(){
				var base_url = "<?=base_url()?>";
				var view_pages =  $('.view-pages-select :selected').val();

				$.ajax({
					url : base_url+"administrator/main/stock_session",
					type: "POST",
					data: {
						view_pages: view_pages
					},
					success: function(data)
					{
						window.location = base_url+"administrator/main/stock";
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

			function validate(form) {

				if(!valid) {
					alert('Please correct the errors in the form!');
					return false;
				}
				else {
					return confirm('Do you really want to submit the form?');
				}
			}

			// function alert before update
			$('#button-alert-modal').click(function(){
				var isReStock = false

				$('#table-alert-modal').find('tr:not(#title-table-alert)').remove();
				$('button[name="go"]').prop('disabled', false)
				$('input[name="stock[]"]').each(function (iRow, row) {
					
					const get_stock = $(row).parent('td').siblings('.td-stock').text();

					if (row.value != get_stock) {
						isReStock = true
						const variant = $(row).parent('td').siblings('.td-variant').text();
						const stock = $(row).parent('td').siblings('.td-stock').text();
						const id = $(row).parent('td').siblings('.td-id').text();
						const input_id = '<input type="hidden" name="item_id[]" value="' + id + '"/>'
						const input_stock = '<input type="hidden" name="stock[]" value="' + row.value + '"/>'

						const newRow = `<tr><td>${variant}</td><td>${stock}</td><td>${row.value}</td>${input_id}${input_stock}</tr>`;

						$('#table-alert-modal tr:last').after(newRow)
					}
				})
				if (!isReStock) {
					$('button[name="go"]').prop('disabled', true)
					$('#table-alert-modal').after('<div class="text-center">Tidak ada stok produk yang akan di update</div>')
				}
			})
		</script>
		<form onsubmit="return validate(this);">