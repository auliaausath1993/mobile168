<?php include 'includes/header.php'; ?>

<div id="page-wrapper">
	<div class="container-fluid">
	    <div class="row">
	    	<div class="col-lg-12">
	    		<h1 class="page-header">
	    		 Pesanan <small> Dalam Proses</small>
	    		</h1>
	    		<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
	    		    <li class="active">
	    		    	<a href="<?=base_url()?>administrator/main/last_order_process" >
	    		    		<b>Keep (Belum Lunas)</b>
	    		    	</a>
	    		    </li>
	    		    <li>
	    		    	<a href="<?=base_url()?>administrator/main/last_order_process_by_variant" >
	    		    		<b>Keep Per Produk</b>
	    		    	</a>
	    		    </li>
	    		    <li>
	    		    	<a href="<?=base_url()?>administrator/main/order_unpaid" >
	    		    		<b>Dropship Belum Lunas</b>
	    		    	</a>
	    		    </li>
	    		    <li>
	    		    	<a href="<?=base_url()?>administrator/main/order_rekap_unpaid_expired" >
	    		    		<b>Jatuh Tempo (Rekap / COD)</b>
	    		    	</a>
	    		    </li>
					<li><a href="<?=base_url()?>administrator/main/order_unpaid_expired" ><b>Jatuh Tempo (Dropship)</b></a></li>
	    		</ul>
	    		<ol class="breadcrumb">
	    			<li class="active">
	    				<i class="fa fa-fw fa-list"></i>
	    				Keep (Belum Lunas)
	    			</li>
	    		</ol>
	    	</div>
	    </div> <!-- /.row -->
	    <div class="panel">
	    	<div class="panel-body">
			    <div>
			    	<?=$this->session->flashdata('message') ?>
			    	<form method="post" action="<?=base_url()?>administrator/main/last_order_session">
			    		<div class="col-sm-2">
			    			<div class="form-group">
			    				<input name="customer_name" id="customer_name" class="customer_name form-control" type="text" value="<?php if(!empty($arr['customer_name'])){echo $arr['customer_name'];}else{echo '';}?>" placeholder="Nama Pelanggan" >
			    				<input type="hidden" class="form-control" id="customer_id" name="customer_id" value="<?php if(!empty($arr['customer_id'])){echo $arr['customer_id'];}else{echo '';}?>" >
			    			</div>
			    		</div>
			    		<div class="col-sm-2">
			    			<div class="form-group">
			    				<input name="product_name" id="product_name" class="product_name form-control" type="text" value="<?php if(!empty($arr['product_name'])){echo $arr['product_name'];}else{echo '';}?>" placeholder="Nama Produk" >
			    				<input type="hidden" class="form-control" id="product_id" name="product_id" value="<?php if(!empty($arr['product_id'])){echo $arr['product_id'];}else{echo '';}?>">
			    			</div>
			    		</div>
			    		<div class="col-sm-2">
			    			<div class="form-group">
			    				<input class="datepicker form-control" name="date_awal" placeholder="Pilih Tanggal" value="<?php if(!empty($arr['date_awal'])){echo $arr['date_awal'];}else{echo '';}?>">
			    			</div>
			    		</div>
			    		<div class="col-sm-2">
			    			<div class="form-group">
			    				<input name="qty" id="qty" class="qty form-control" type="text" value="<?php if(!empty($arr['qty'])){echo $arr['qty'];}else{echo '';}?>" placeholder="Quantity" >
			    			</div>
			    		</div>

			    		<input type="hidden" name="cari" value="<?php echo "cari";?>" >
			    		<div class="col-sm-4">
			    			<div class="form-group">
			    				<input type="submit" value="FILTER" class="btn btn-primary">
			    				<a href="<?=base_url() ?>administrator/main/last_order_process" class="btn btn-danger">RESET</a>
			    			</div>
			    		</div>
			    		<div class="clearfix"></div>
			    	</form>
			    </div>

			    <div id="list_ubah_status" class="table-responsive">
			    	<table class="table table-bordered">
			    		<thead>
			    			<tr class="btn-info">
			    				<th><input type="checkbox" name="check_all" id="check_all" /></th>
			    				<th width="3%">No</th>
			    				<th width="10%">Pelanggan</th>
			    				<th width="12%">Produk</th>
			    				<th width="10%">Tgl Pesan</th>
			    				<th width="10%">Varian</th>
			    				<th width="2%">QTY</th>
			    				<th width="13%">Harga Jual</th>
			    				<th width="13%">Subtotal</th>
			    				<th width="10%">Tipe Produk</th>
			    				<th width="40%">Action</th>
			    			</tr>
			    		</thead>
			    		<?php if($list_orders->result() !=null){ $i = 1 * ($offset + 1);
			    		foreach($list_orders->result() as $items):
			    		$data_customer = $this->main_model->get_detail('customer',array('id' => $items->customer_id));
			    		$data_product = $this->main_model->get_detail('product',array('id' => $items->prod_id));
			    		$data_variant= $this->main_model->get_detail('product_variant',array('id' => $items->variant_id));
			    		?>
			    		<tbody>
			    			<tr>
			    				<td>
			    					<input type="checkbox" name="order_item_id[]" class="check_list" value="<?=$items->id?>" />
			    					<input type="hidden" name="id_order_item" value="<?=$items->id?>">
			    				</td>
			    				<td><?=$i?></td>
			    				<td>
			    					<?=$data_customer['name']." (".$data_customer['id'].")"?>
			    				</td>
			    				<td><?=$data_product['name_item']?></td>
			    				<td><?=$items->order_datetime?></td>
			    				<td><?=$data_variant['variant']?></td>
			    				<td><?=$items->qty ?></td>
			    				<td>Rp <?=numberformat($items->price) ?></td>
			    				<td>Rp <?=numberformat($items->subtotal) ?></td>
			    				<td><?=$items->tipe?></td>
			    				<td>
			    					<form method="post" action="<?=base_url()?>administrator/main/order_per_customer/<?=$data_customer['id'] ?>">
			    						<input type="hidden" name="halaman" value="view">
			    						<input type="submit" class="btn btn-primary" name="page" value="View">
			    						<button class="btn btn-danger btn-cancel-item" link="cancel_order_process/<?=$items->id ?>">Batal</button>
			    					</form>
			    				</td>
			    			</tr>
			    		</tbody>
			    	<?php $i++; endforeach; ?>
			    </table>
			    <?php }else{?>
			    <table align="center">
			    	<tr>
			    		<td>
			    			<p>Data yang anda inginkan tidak ada</p>
			    		</td>
			    	</tr>
			    </table>
			    <?php }?>
			    <input type='button' class='btn btn-md btn-danger' id='btn_check_batal' value="BATALKAN">
			    <?=$this->pagination->create_links(); ?>
			    </div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.1.min.js"></script>
<script type='text/javascript'>
var site = "<?php echo base_url() ?>";
$(function(){
	$('#customer_name').autocomplete({
		serviceUrl: base_url+'administrator/main/search_id_customer',
		onSelect: function (suggestion) {
			$('#customer_id').val(suggestion.data);
		}
	});
});

$(function(){
	$('#product_name').autocomplete({
		serviceUrl: site+'administrator/main/search_id_produk_item',
		onSelect: function (suggestion) {
			$('#product_id').val(suggestion.data);
		}
	});
});
$("#product_id").change(function(){
	var prod_id = $(this).val();
	$("#variant").html("<option>Loading....</option>");
	$.post(base_url+"administrator/main/get_variant_create_order", { prod_id: prod_id, <?=$this->security->get_csrf_token_name()?>: "<?=$this->security->get_csrf_hash()?>"},
		function(data){
			$("#variant").html("<option value=''>- Pilih Variant -</option>");
			$("#variant").html("<font color='red'>Pilih variant</font>");
			var x = "<option value='"+data.variant.variant_id+"'>"+data.variant.variant_name+" (stock: "+data.variant.stock+")</option>";
			$("#variant").append(x);
		}, "json");
});
$(function(){
	$('.datepicker').datepicker({
		format: 'yyyy-mm-dd'
	});
});

// Using jQuery.
$('#check_all').click(function() {
$('.check_list').prop('checked', this.checked);
if($(this).attr('checked')){
	$('.list').removeAttr('readonly');
}else{
	$('.list').attr('readonly','readonly');
}});
$("#btn_check_batal").click(function(e){
	e.preventDefault();
	var checked = [];
	$('#list_ubah_status .check_list:checked').each(function() {
		checked.push($(this).val());
	});
	if (!checked.length) {
		return alert('Pilih Terlebih dahulu data yang akan dibatalkan !!');
	}
	$('#modal-pincode').modal('show');
	var action_link = base_url + 'administrator/main/update_status_batal_pesanan';
	var form = $('#modal-cancel-order form');
	form.attr('action', action_link);
	$('#order_item_id').html('');
	checked.map(function(id) {
		$('#order_item_id').append('<input type="hidden" name="order_item_id[]" value="' + id + '">');
	});
});

$('.btn-cancel-item').click(function(e) {
	e.preventDefault();
	$('#modal-pincode').modal('show');
	var form = $('#modal-cancel-order form');
	var action_link = $(this).attr('link');
	form.attr('action', base_url + 'administrator/main/' + action_link);
});
$(document).on('click', '#submit-pincode', function() {
	var code = $('#pincode').val();
	$('.loading-pincode').show();
	$('.form-pincode').hide();
	$.post(base_url + 'administrator/main/check_pincode', { code: code }, function(data) {
		$('.form-pincode').show();
		$('.loading-pincode').hide();
		if (data.status == 'Success') {
			$('#modal-cancel-order').modal('show');
			$('#modal-pincode').modal('hide');
		} else {
			alert('Pincode salah');
		}
		$('#pincode').val('');
	}, 'json');
});
</script>
<?php include "includes/footer.php"; ?>