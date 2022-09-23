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
							Menu <small> Untuk update harga</small>
						</h1>
						<h4>Kontrol Harga</h4>
						<ol class="breadcrumb">
							<li class="active">
								<i class="fa fa-dashboard"></i> Harga
							</li>
						</ol>
					</div>
				</div>
				<!-- /.row -->
				<div class="row">
					<div class="col-lg-12">

						<div class="row search-stock-wrapper">

							<div class="search-name-wrapper col-sm-4">
								<form name="form1" id="form1" method="post" action="<?=base_url()?>administrator/main/search_product_session_harga" >
									<input type='text' id='name_product' class="form-control autocomplete_pro input-text col-sm-10" placeholder="Nama Produk" style="width: 60%" />
									<input id="id" name='prod_id' class="form-control" type="hidden" readonly>&nbsp;
									<button  type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
								</form>
							</div>
						</div>

						<form onsubmit="return confirm('Update Harga ?');" action="<?= base_url('administrator/main/process_update_harga_umum') ?>" method="post">


						<?=$this->session->flashdata('message') ?>						
						<table id="table-exsport" class="table table-bordered table-striped">
							<thead>

								
								<tr>
									<th>Produk</th>
									<th width="11%">Tipe Customer</th>
									<th>Harga Jual</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($list_item->result() as $items) { 
									?>
									<tr>
										<td><?=$items->name_item ?></td>
										<td><?=$items->name ?></td>
										<td>
											<input type="hidden" name="item_id[]"  value="<?=$items->id ?>"/>
											<input type="number" name="price[]" class="form-control price-control input-small" value="<?=$items->price ?>" />
											<span style="display: none;"><?=$items->price ?></span>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<?=$this->pagination->create_links(); ?>

						<p><button type="submit" name="go" class="btn btn-success" >UPDATE HARGA</button>
							<a href="update_harga_umum" class="btn btn-danger"> RESET SEARCH </a>
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
</script>
	<form onsubmit="return validate(this);">