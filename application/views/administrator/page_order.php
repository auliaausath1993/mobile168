<?php include 'includes/header.php'; ?>
<?php $uri = $this->uri->segment(3); ?>
<div id="page-wrapper">
    <div class="container-fluid">
	    <div class="row">
	        <div class="col-lg-12">
	            <?php if ($uri == 'order_unpaid' || $uri == 'order_unpaid_expired' || $uri == 'order_rekap_unpaid_expired') { ?>
	            	<h1 class="page-header">
		                Pesanan <small> Dalam Proses </small>
		            </h1>
		            <ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
	                  	<li>
	                  		<a href="<?=base_url()?>administrator/main/last_order_process" ><b>Keep (Belum Lunas)</b></a>
	                  	</li>
	                  	<li>
	                  		<a href="<?=base_url()?>administrator/main/last_order_process_by_variant" ><b>Keep Per Produk</b></a>
	                  	</li>
	                	<?php if ($uri == 'order_unpaid') { ?>
						<li class="active"><a href="<?=base_url()?>administrator/main/order_unpaid" ><b>Dropship Belum Lunas</b></a>
						</li>
	                	 <li class="<?= $uri == 'order_rekap_unpaid_expired' ? 'active' : '' ?>">
		    		    	<a href="<?=base_url()?>administrator/main/order_rekap_unpaid_expired" >
		    		    		<b>Jatuh Tempo (Rekap / COD)</b>
		    		    	</a>
		    		    </li>
						<li class="<?= $uri == 'order_unpaid_expired' ? 'active' : '' ?>"><a href="<?=base_url()?>administrator/main/order_unpaid_expired" ><b>Jatuh Tempo (Dropship)</b></a></li>
						<?php }elseif($uri == 'order_unpaid_expired' || $uri == 'order_rekap_unpaid_expired') { ?>
						<li>
							<a href="<?=base_url()?>administrator/main/order_unpaid" ><b>Dropship Belum Lunas</b></a></li>
	                	 <li class="<?= $uri == 'order_rekap_unpaid_expired' ? 'active' : '' ?>">
		    		    	<a href="<?=base_url()?>administrator/main/order_rekap_unpaid_expired" >
		    		    		<b>Jatuh Tempo (Rekap / COD)</b>
		    		    	</a>
		    		    </li>
						<li class="<?= $uri == 'order_unpaid_expired' ? 'active' : '' ?>"><a href="<?=base_url()?>administrator/main/order_unpaid_expired" ><b>Jatuh Tempo (Dropship)</b></a></li>
						<?php } ?>
	                </ul>
	                <ol class="breadcrumb">
		                <li class="active">
		                    <i class="fa fa-fw fa-list"></i> Pesanan <?= $uri == 'order_unpaid' ? 'Dropship Belum Lunas' : ($uri == 'order_unpaid_expired' ? 'Dropship' : 'Rekap / COD') ?> <?= $uri == 'order_unpaid_expired' || $uri == 'order_rekap_unpaid_expired' ? 'Jatuh Tempo' : '' ?>
		                </li>
		            </ol>
	            <?php }else{
	            	if ($uri == 'report_autocancel') {
	            		$header_title = 'Laporan';
	            	} else {
	            		$header_title = 'Pesanan';
	            	}?>
	            	<h1 class="page-header">
		                <?= $header_title ?> <small> <?=$order_payment?> </small>
		            </h1>
	            <?php } ?>
	        </div>
	    </div>
	    <!-- /.row -->

    	<?=$this->session->flashdata('message') ?>

		<div id="list_orders">
		<?= $output->output; ?>
		</div>
		<br/>
		<?php if ($order_payment == 'Dropship Belum Lunas'){ ?>
		<input type="button" class="btn btn-md btn-success" id="btn_check_lunas" value="LUNAS CHECKED" />
			<?php if($uri == 'order_unpaid_expired' || $uri == 'order_rekap_unpaid_expired') { ?>
			<input type="button" class="btn btn-md btn-danger" id="btn_check_batal" value="BATALKAN CHECKED" />
			<?php }?>
		<?php }?>
    </div>
 </div>

<?php include "includes/footer.php"; ?>
<?php if($output->output != null) { ?>
	<?php foreach($output->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
	<?php } ?>
	<script src="<?= base_url() ?>application/views/administrator/assets/new-theme/js/bootstrap.js"></script>
<script type="text/javascript">
$("#btn_check_lunas").click(function(){
	var list = "";
	var base_url = "<?=base_url() ?>";

	var ch = new Array();

	$('#list_orders input:checkbox:checked').each(function() {

		ch.push($(this).val());
	});

	var list_order = "["+ch+"]";
	$.post( base_url+"administrator/main/order_process_to_paid", { list_order : list_order}, function( data ) {

		if(data.pesan == 'Success')
		{
			window.location.reload(true);
		} else {
			alert("Data pesanan gagal diubah");
		}
	}, "json");
});
$("#btn_check_batal").click(function(e){
	e.preventDefault();
	var checked = [];
	$('#list_orders input:checkbox:checked').each(function() {
		checked.push($(this).val());
	});
	if (!checked.length) {
		return alert('Pilih Terlebih dahulu data yang akan dibatalkan !!');
	}
	$('#modal-pincode').modal('show');
	var action_link = base_url + 'administrator/main/order_process_to_batal';
	var form = $('#modal-cancel-order form');
	form.attr('action', action_link);
	$('#order_item_id').html('');
	checked.map(function(id) {
		$('#order_item_id').append('<input type="hidden" name="order_item_id[]" value="' + id + '">');
	});
});

$(document).on('click', '.btn-cancel-order', function(e) {
	e.preventDefault();
	$('#modal-pincode').modal('show');
	var form = $('#modal-cancel-order form');
	var action_link = $(this).attr('href');
	form.attr('action', action_link);
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