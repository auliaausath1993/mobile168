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
	                Buat Pesanan Tamu Baru
	            </h1>
	            <ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
                  	<li><a href="<?=base_url()?>administrator/main/create_order" ><b>Pesanan Pelanggan</b></a></li>
                  	<li class="active"><a href="<?=base_url()?>administrator/main/create_order_tamu" ><b>Pesanan Non Pelanggan</b></a></li>
                </ul>
	        </div>
	         <?=validation_errors()?>
	    </div>
	    <!-- /.row -->

    	<?=$this->session->flashdata('message') ?>

		<form name="form1" id="form1" method="post" action="create_order_process" >

			<div class="customer_tamu">
			<p><b>Pembeli Tamu </b></p>
			<div class="data_customer form-horizontal">
				<div class="form-group">
					<label class="col-lg-1 control-label">Nama</label>
					<div class="col-lg-6">
						<input name="customer_id" class="customer_id" type="hidden" value="0"> <input type="text" name="name_customer" class="form-control" required/>
					</div>
				</div>
			</div>
			</div>

			<hr/>

			<p><b>Item Pesanan </b></p>

			<div class="table-responsive">
				<table class="table table-bordered table-striped">

					<thead>

						<tr class="btn-info">

							<th><br/><input type="checkbox" name="check_all" id="check_all" /></th>

							<th>Nama Produk</th>

							<th width="10%">Min Order</th>

							<th>Varian</th>

							<th>Qty</th>

							<th>Berat</th>

							<th>Subtotal</th>

						</tr>

					</thead>

					<tbody>



					<?php for($i=0;$i<10;$i++) { ?>

						<tr>

							<td><br/><input type="checkbox" name="check_list[]" class="check_list" id="<?=$i?>" value="<?=$i?>"/></td>

							<td>
								<span id="notif_product_<?=$i?>"></span><br/>

								<input name="product[<?=$i?>]" class="product_name list form-control list_<?=$i?>" rel="<?=$i?>" type="text" readonly>

								<input id="item_<?=$i?>" name="product[<?=$i?>]" class="product_item list form-control list_<?=$i?>" type="hidden" readonly>

								<input type="hidden" name="price[<?=$i?>]" id="price_item_<?=$i?>" class="price_qty_<?=$i?>"value=""/>

								<input type="hidden" name="weight[<?=$i?>]" id="weight_item_<?=$i?>" class="weight_qty_<?=$i?>"value=""/>

							</td>

							<td>
								<span id="notif_minimal_order_<?=$i?>"></span><br/>

								<input type="text" name="minimal[<?=$i?>]" id="minimal_item_<?=$i?>" class="minimal_qty_<?=$i?> form-control" value="0" readonly="readonly" />
							</td>

							<td>
								<span id="notif_variant_item_<?=$i?>"></span><br/>
								<select name="variant_id[<?=$i?>]" class="variant form-control list list_<?=$i?>" id="variant_item_<?=$i ?>" readonly >

									<option value="">- Pilih Varian -</option>

								</select>

							</td>

							<td>
								<span id="notif_qty_item_<?=$i?>"></span><br/>
								<input name="qty[<?=$i?>]" type="number" min="1" class="qty form-control list list_<?=$i?>" id="qty_<?=$i?>" readonly />

							</td>

							<td>
								<span id="notif_subtotal_weight_<?=$i?>"></span><br/>
								<input type="text" name="subtotal_weight[<?=$i?>]" value="0" id="subtotal_weight_item_<?=$i?>" class="form-control subtotal_weight_qty_<?=$i?>" readonly="readonly" />

							</td>

							<td>
								<span id="notif_subtotal_<?=$i?>"></span><br/>
								<input type="text" name="subtotal[<?=$i?>]" value="0" id="subtotal_item_<?=$i?>" class="form-control subtotal_qty_<?=$i?>" readonly="readonly" />

							</td>

						</tr>

					<?php } ?>

					</tbody>
					<tfoot>
						<tr>
							<td colspan="2" class="text-right">
								<input type="checkbox" name="diskon" id="diskon" class="check_list" value="1">
								<label for="diskon">Input Diskon</label>
							</td>
							<td colspan="5">
								<input type="number" min="0" value="0" name="diskon_val" id="diskon_val" class="form-control" readonly="readonly">
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<p><b>Status Pesanan </b></p>
			<p>Pilih salah satu</p>

			<div class="well">
				<div class="row">
					<div class="col-md-6">
						<label class="radio-inline">
						  <input type="radio" name="status_pesanan" id="inlineRadio2" class="check_status_pesanan"  value="Keep_Paid" checked> <strong>Pesanan Bayar Ditempat (Lunas)</strong><br/><small>Pesanan telah memiliki Nota / ID Pesanan dan Lunas</small>
						</label><br/><br/>
					</div>

					<div class="col-md-6">
							<label class="radio-inline">
							  <input type="radio" name="status_pesanan" id="inlineRadio4" class="check_status_pesanan" value="Dropship_Paid"> <strong>Pesanan Dropship Lunas)</strong><br/><small>Pesanan telah memiliki Nota / ID Pesanan, dengan metode Pengiriman Alamat, dan telah Lunas</small>
							</label>
					</div>
				</div>
			</div>

			<button type="submit" class="btn btn-success" name="go">Buat Pesanan</button>
			<hr/>
			<br/><br/>
	    </form>
    </div>
 </div>



<?php include "includes/footer.php"; ?>





<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.1.min.js"></script>

<script type="text/javascript">



		var base_url = "<?=base_url()?>";

		var stock_value = '<?=$data_value_stock["value"] ?>';

		$('#check_all').click(function() {

    		 $('.check_list').prop('checked', this.checked);

		  if($(this).attr('checked')){

				$('.list').removeAttr('readonly');

			}

			else

			{

				$('.list').attr('readonly','readonly');

			}

 		});

		$('#diskon').click(function() {
 			if ($(this).attr('checked')) {
 				$('#diskon_val').removeAttr('readonly');
 			} else {
 				$('#diskon_val').attr('readonly', 'readonly');
 				$('#diskon_val').val('0');
 			}
 		});

		$('.check_list').click(function() {

			var x = this.id;



			if($(this).attr('checked'))
			{

				$('.list_'+x).removeAttr('readonly');
				$('#qty_'+x).attr("required","true");
				$('#item_'+x).attr("required","true");
				$('#variant_item_'+x).attr("required");

			}

			else

			{

				$('.list_'+x).attr('readonly','readonly');
				$('.qty_'+x).removeAttr("required");
				$('#variant_item_'+x).removeAttr("required");

			}

		});



		$(".product_item").change(function(){

			var a = this.id;

			var prod_id = $(this).val();

			$("#variant_"+a).html("<option>Loading....</option>");

			$.post(base_url+"administrator/main/get_variant_create_order", { prod_id: prod_id, <?=$this->security->get_csrf_token_name()?>: "<?=$this->security->get_csrf_hash()?>"},

			   function(data){

				$("#price_"+a).val(data.price);

				$("#weight_"+a).val(data.weight);

				$("#minimal_"+a).val(data.min_order);

				$("#variant_"+a).html("<option value=''>- Pilih Variant -</option>");

				$("#notif_variant_"+a).html("<font color='red'>Pilih variant</font>");
				$("#notif_qty_"+a).html("<font color='red'>Isi QTY</font>");

				for (var i=0;i<data.variant.length;i++)

				{

					if(stock_value == 3){
						var x = "<option value='"+data.variant[i].variant_id+"'>"+data.variant[i].variant_name+"</option>";

						$("#variant_"+a).append(x);
					}else{
						var x = "<option value='"+data.variant[i].variant_id+"'>"+data.variant[i].variant_name+" (stock: "+data.variant[i].stock+")</option>";

						$("#variant_"+a).append(x);
					}

				}


			}, "json");


		});

		$(".variant").change(function(){

			var variant_id  = $(this).val();

			$("#add_form_qty").val("");

			$.post(base_url+"administrator/main/order_detail_get_variant_detail",{variant_id: variant_id},
			function(data){

				if(stock_value == 3){

				}else{
					$("#add_form_qty").attr("max",data.stock);
					$("#notif_stock").html("<font color='red'>Sisa Stock : <strong>"+data.stock+"</strong></font>")
				}
			}, "json");

		});

		$(".qty").change(function(){

			var this_id = this.id;

			var this_val = $(this).val();

			var this_price = $(".price_"+this_id).val();

			var this_weight = $(".weight_"+this_id).val();

			var subtotal = this_val * this_price;

			var weight = this_val * this_weight;

			$(".subtotal_"+this_id).val(subtotal);

			$(".subtotal_weight_"+this_id).val(weight);

		});


</script>
<!--- AUTO COMPLETE --->
<!--- DATA CUSTOMER --->
	<script type='text/javascript' src='<?php echo base_url();?>assets/js/jquery.autocomplete.js'></script>
	 <script type='text/javascript'>
		var site = "<?php echo site_url();?>";

		$(function(){
			$('.autocomplete').autocomplete({
				serviceUrl: site+'administrator/main/search_id_customer',
				onSelect: function (suggestion) {
					document.form1.id.value = suggestion.data;
				}
			});
		});

	</script>

<!--- DATA PRODUCT --->
	 <script type='text/javascript'>
		var site = "<?php echo site_url();?>";

		$(function(){
			$('.product_name').autocomplete({
				serviceUrl: site+'administrator/main/search_id_produk_item',
				onSelect: function (suggestion) {
					var f =  $(this).attr("rel");
					$('#item_'+f).val(suggestion.data);
					get_variant(f,suggestion.data);
				}
			});
		});


		function get_variant(this_id,prod_id)
		{
			var a = "item_"+this_id;

			$("#variant_"+a).html("<option>Loading....</option>");

			$.post(base_url+"administrator/main/get_variant_create_order", { prod_id: prod_id, <?=$this->security->get_csrf_token_name()?>: "<?=$this->security->get_csrf_hash()?>"},

			   function(data){

				$("#price_"+a).val(data.price);

				$("#weight_"+a).val(data.weight);

				$("#minimal_"+a).val(data.min_order);

				$("#variant_"+a).html("<option value=''>- Pilih Variant -</option>");

				$("#notif_variant_"+a).html("<font color='red'>Pilih variant</font>");
				$("#notif_qty_"+a).html("<font color='red'>Isi QTY</font>");

				for (var i=0;i<data.variant.length;i++)

				{

					if(stock_value == 3){
						var x = "<option value='"+data.variant[i].variant_id+"'>"+data.variant[i].variant_name+"</option>";

						$("#variant_"+a).append(x);
					}else{
						var x = "<option value='"+data.variant[i].variant_id+"'>"+data.variant[i].variant_name+" (stock: "+data.variant[i].stock+")</option>";

						$("#variant_"+a).append(x);
					}

				}


			}, "json");
		}

	</script>
	<!--- END AUTO COMPLETE --->

