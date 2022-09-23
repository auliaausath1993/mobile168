<?php include 'includes/header.php'; ?>
<div id="report" class="container-fluid">
	<div class="panel">
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-12">
					<div class="search_report">
						<div class="col-lg-12">
							<h1 class="page-header">
								Laporan Jumlah Laku Terjual
							</h1>
						</div>
						<div class="body-report">

							

							<form name="form1" id="form1" method="post" action="<?=base_url()?>administrator/main/get_jumlah_laku_process" >
								<input type="text" name="month" placeholder="Pilih Bulan" autocomplete="off" class="form-control datepicker" data-date-format="yyyy-mm" data-date-viewmode="years" data-date-minviewmode="months" style="width: 40%" />
								<br>
								<input type='text' id='name_product' class="form-control autocomplete_pro input-text col-sm-10" placeholder="Nama Produk" style="width: 40%" />
								<input id="id" name='prod_id' class="form-control" type="hidden" readonly>&nbsp;
								<br><br><br>
								<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> LIHAT LAPORAN</button>
								<a href="<?=base_url() ?>administrator/main/get_jumlah_laku_v2" class="btn btn-danger">RESET</a>
								
							</form>


							<!-- <div class="col-sm-2">
								<form action="<?=base_url()?>administrator/main/report_jumlah_laku_eksport" method="post" target="_new">
									<input class="month-input" type="hidden" name="month" value="<?=$this_month ?>"/>
									<button type="submit" id="exsport-laporan-day" class="btn btn-success" style="margin-bottom: 10px; margin-left: -15px; padding:4px 12px;"><i class="fa fa-file-excel-o"></i> Export Excel</button>
								</form>
							</div> -->



						</div>

					</div>

				</div>

			</div>

			<!-- /.row -->

			<div class="row">

				<div class="col-lg-12">

					<div class="table-responsive">
						<table id="table-laporan" class="table table-bordered table-striped">

							<thead>

								<tr>

									<th>Bulan</th>
									<th>Nama Produk</th>
									<th>Stok Awal</th>
									<th>Jumlah Terjual</th>
									<th>Persentase Terjual</th>

								</tr>

							</thead>

							<tbody>

								<?php foreach($transaksi->result() as $trans):
									$persen=round($trans->jumlah_qty/$trans->jumlah_stock * 100,2);
									?>

									<tr>

										<td>#<?=$trans->bulan?></td>
										<td><?=$trans->name_item?></td>
										<td><?=$trans->jumlah_stock?></td>
										<td><?=$trans->jumlah_qty?></td>
										<td><span><?=$persen?> %</span></td>

									</tr>

								<?php endforeach; ?>

							</tbody>

						</table>
					</div>
				</div>

			</div>
		</div>
	</div>

</div>

</div>


<div class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">


			<div class="modal-body">

				<table class="table table-bordered">

					<thead>
						<tr>
							<th>Item</th>
							<th>Harga Modal</th>
							<th>QTY yang terjual</th>
							<th class="text-right">Modal</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($transaksi->result() as $trans):
							$data_items_trans = $this->main_model->get_list_where('orders_item',array('order_id' => $trans->id));
							$all_modal = 0;
							foreach($data_items_trans->result() as $list_item):

								$data_this_product = $this->main_model->get_detail('product',array('id' => $list_item->prod_id));

								$list_product = $data_this_product ['name_item'];

								$prod_price=$data_this_product['price_production'];

								$md = $prod_price * $list_item->qty;

								$all_modal = $all_modal + $md;
								?>


								<tr>
									<td><?= $list_product?></td>
									<td>Rp. <?=numberformat($prod_price)?></td>
									<td><?= $list_item->qty?></td>
									<td class="text-right">Rp. <?=numberformat($md)?></td>
								</tr>

								<?php
							endforeach; ?>
						<?php endforeach; ?>

					</tbody>
					<tfoot>
						<tr>
							<td colspan="3"><strong>TOTAL MODAL</strong></td>
							<td class="text-right">Rp. <?=numberformat($all_modal)?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
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
				/*if (prod_id != 0) {

					$('#form1 button').attr("disabled", false);

				};*/

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

		// $('#form1 button').attr("disabled", true);

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

