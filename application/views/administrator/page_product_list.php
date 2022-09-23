<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
    <div class="container-fluid">
	    <div class="row">
	        <div class="col-lg-12">
	        	<?php if ($this->uri->segment(4) == 'Ready_Stock') { ?>
		            <h1 class="page-header">
		               	Data Produk <small>Ready Stock</small>
		            </h1>
		            <ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
	                  	<li><a href="<?=base_url()?>administrator/main/add_product/ready_stock" ><b>Tambah Produk</b></a></li>
	                  	<li <?php if($this->uri->segment(5) == 'Publish'){ echo 'class="active"';} ?>><a href="<?=base_url()?>administrator/main/product/Ready_Stock/Publish/" ><b>Data Produk (Publish)</b></a></li>
	                	<li <?php if($this->uri->segment(5) == 'Unpublish'){ echo 'class="active"';} ?>><a href="<?=base_url()?>administrator/main/product/Ready_Stock/Unpublish/" ><b>Data Produk (Unpublish)</b></a></li>
	                </ul>
	            <?php }elseif ($this->uri->segment(4) == 'PO') { ?>
	            	<h1 class="page-header">
		               	Data Produk <small>Pre Order</small>
		            </h1>
		            <ul id="submenu-container" class="nav nav-tabs" style="margin-bottom: 20px;">
	                  	<li><a href="<?=base_url()?>administrator/main/add_product/pre_order" ><b>Tambah Produk</b></a></li>
	                  	<li <?php if($this->uri->segment(5) == 'Publish'){ echo 'class="active"';} ?>><a href="<?=base_url()?>administrator/main/product/PO/Publish/" ><b>Data Produk (Publish)</b></a></li>
	                	<li <?php if($this->uri->segment(5) == 'Unpublish'){ echo 'class="active"';} ?>><a href="<?=base_url()?>administrator/main/product/PO/Unpublish/" ><b>Data Produk (Unpublish)</b></a></li>
	                </ul>
	            <?php } ?>

	            <ol class="breadcrumb">
	                <li class="active">
	                	<?php if ($this->uri->segment(5) == 'Publish') { ?>
	                		<i class="fa fa-fw fa-list"></i> Data Produk Publish
	                	<?php }else{ ?>
	                		<i class="fa fa-fw fa-list"></i> Data Produk Unpublish
	                	<?php } ?>
	                </li>
	            </ol>
	            <div class="pull-right m-b">
		            <?php if ($this->uri->segment(4) == 'Ready_Stock') { ?>
	                	<a href="<?=base_url()?>administrator/main/add_product/ready_stock" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add Product</a>
	                <?php }elseif ($this->uri->segment(4) == 'PO') { ?>
	                	<a href="<?=base_url()?>administrator/main/add_product/pre_order" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add Product</a>
	                <?php } ?>
	            </div>
	        </div>
	    </div>
	    <!-- /.row -->

    	<?=$this->session->flashdata('message') ?>
    	<?= validation_errors() ?>
		<div id="list_ubah_status">
		<?= $output->output; ?>
		</div>
		<br/>
		<form method="post" id="form_change_supplier" action="<?= base_url('administrator/main/change_supplier') ?>">
			<input type="hidden" class="supplier_id" name="supplier_id">
			<input type="hidden" class="product_id" name="product_id">
		</form>
		<form method="post" id="form_change_invoice" action="<?= base_url('administrator/main/change_invoice') ?>">
			<input type="hidden" class="no_invoice" name="no_invoice">
			<input type="hidden" class="product_id" name="product_id">
		</form>
		<div class="col-sm-2 select-menu">
				<select class="select_action form-control">
					<option value="">-Pilih Action-</option>
					<?php if($type == 'ready' and $stat != 'Unpublish'){?>
						<option value="po">Jadikan PO</option>
					<?php } if($type == 'po' and $stat != 'Unpublish'){?>
						<option value="ready">Jadikan Ready</option>
					<?php }?>
					<?php if($stat == 'Publish'){?>
						<option value="unpublish">Unpublish</option>
					<?php }else{?>
						<option value="publish">Publish</option>
					<?php }?>
				</select>
			</div>
				<button type="button" class="btn btn-md btn-success" id="btn_check"> SUBMIT </button>
				<!-- <button type="button" class="btn btn-md btn-danger" id="del_pro"> DELETE ALL PRODUCT </button> -->
    </div>

 </div>

 <div class="modal fade" id="change-category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    	<form name="form" id="form1" method="post" action="<?=base_url()?>administrator/main/update_product_type" >
	        <div class="modal-content">
	            <div class="modal-header" style="background-color: rgb(40, 169, 131); color: white;">
	                <h4 class="modal-title" id="myModalLabel">Pilih Kategori Produk PO</h4>
	            </div>

	            <div class="modal-body">

	            </div>
	            <div class="modal-footer text-right">
	            	<?php if ($this->uri->segment(4) == 'Ready_Stock') { ?>
	            		<input type="hidden" name="product-type" value="PO">
	            	<?php }elseif ($this->uri->segment(4) == 'PO') { ?>
	            		<input type="hidden" name="product-type" value="Ready Stock">
	            	<?php }?>
	            	<button type="submit" class="btn btn-md btn-success" id="btn_update_pro_type"> SUBMIT </button>
	            	<button type="button" class="btn btn-md btn-default " id="close_popup"> CANCEL </button>
	            </div>
	        </div>
        </form>
    </div>
  </div>

<?php include "includes/footer.php"; ?>
<?php if($output->output != null) { ?>
	<?php foreach($output->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
	<?php } ?>
<script type="text/javascript">


$(".select_action").change(function(){
	var nilai = $(this).val();

	var list = "";
	var base_url = "<?=base_url() ?>";

	var ch = new Array();

	$('#list_ubah_status input:checkbox:checked').each(function() {

		ch.push($(this).val());

	});

	var list_status = "["+ch+"]";

	if(nilai == 'ready'){
		$.post( base_url+"administrator/main/list_product_checked", { list_status : list_status}, function( data ) {
			$('#change-category').show().addClass('in').css('background-color','rgba(0,0,0,0.5)');
			$('body').addClass('modal-open');
			if(data.pesan == 'Success')
			{
				var table_content = '<table class="table table-striped">';
					table_content += '<thead>';
					table_content += '<tr>';
					table_content += '<th> Nama Produk </th>';
					table_content += '<th> Category Produk </th>';
					table_content += '<tr/>';
					table_content += '</thead>';
					table_content += '<tbody>';
					table_content += '</tbody>';
					table_content += '</table>';

				$('#change-category .modal-dialog .modal-body').removeClass('alert alert-danger text-center').html(table_content);
				$('#change-category .modal-dialog .modal-header').show();
				$('#change-category .modal-dialog .modal-footer').show();
				var product = data.product_list;
				for(var i=0;i< product.length;i++)
				{
					var content = '<tr>';
						 content += '<td><input type="hidden" name="jmlh[]" /><input type="hidden" name="id_product[]" value="'+product[i].id+'" />'+product[i].name+'</td>';
						 content += '<td>';
						 content += '<select name="category_product[]" id="select-cateogry" required><option value="">--- Pilih Category Product---</option></select>';
						 content += '</td>';
						 content += '</tr>';
					$('#change-category .modal-body table tbody' ).append(content);
				}

				var category = data.category;
				for(var i=0;i< category.length;i++)
				{
					var option = '<option value="'+category[i].id+'">'+category[i].name+'</option>';
					$('#change-category table tbody select ' ).append(option);
				}
			}
			else
			{
				$('#change-category .modal-dialog .modal-header').hide();

				$('#change-category .modal-dialog .modal-body').addClass('alert alert-danger text-center').css('margin-bottom',0).html(' <h4 class="modal-title" id="myModalLabel">'+data.alert+'</h4><hr/><button type="button" class="btn btn-lg btn-default" id="close_popup"> CANCEL </button>');
				$('#change-category .modal-dialog .modal-footer').hide();

				$("#close_popup").click(function(){
					$('body').removeClass('modal-open');
					$('#change-category').hide().removeClass('in');
					$(".select_action").val(null);
				});
			}
		}, "json");

	}

	if(nilai == 'po'){
		$.post( base_url+"administrator/main/list_product_checked", { list_status : list_status}, function( data ) {
			$('#change-category').show().addClass('in').css('background-color','rgba(0,0,0,0.5)');
			$('body').addClass('modal-open');
			if(data.pesan == 'Success')
			{
				var table_content = '<table class="table table-striped">';
					table_content += '<thead>';
					table_content += '<tr>';
					table_content += '<th> Nama Produk </th>';
					table_content += '<th> Category Produk </th>';
					table_content += '<tr/>';
					table_content += '</thead>';
					table_content += '<tbody>';
					table_content += '</tbody>';
					table_content += '</table>';

				$('#change-category .modal-dialog .modal-body').removeClass('alert alert-danger text-center').html(table_content);
				$('#change-category .modal-dialog .modal-header').show();
				$('#change-category .modal-dialog .modal-footer').show();
				var product = data.product_list;
				for(var i=0;i< product.length;i++)
				{
					var content = '<tr>';
						 content += '<td><input type="hidden" name="jmlh[]" /><input type="hidden" name="id_product[]" value="'+product[i].id+'" />'+product[i].name+'</td>';
						 content += '<td>';
						 content += '<select name="category_product[]" id="select-cateogry" required><option value="">--- Pilih Category Product---</option></select>';
						 content += '</td>';
						 content += '</tr>';
					$('#change-category .modal-body table tbody' ).append(content);
				}

				var category = data.category;
				for(var i=0;i< category.length;i++)
				{
					var option = '<option value="'+category[i].id+'">'+category[i].name+'</option>';
					$('#change-category table tbody select ' ).append(option);
				}
			}
			else
			{
				$('#change-category .modal-dialog .modal-header').hide();

				$('#change-category .modal-dialog .modal-body').addClass('alert alert-danger text-center').css('margin-bottom',0).html(' <h4 class="modal-title" id="myModalLabel">'+data.alert+'</h4><hr/><button type="button" class="btn btn-lg btn-default" id="close_popup"> CANCEL </button>');
				$('#change-category .modal-dialog .modal-footer').hide();

				$("#close_popup").click(function(){
					$('body').removeClass('modal-open');
					$('#change-category').hide().removeClass('in');
					$(".select_action").val(null);
				});
			}
		}, "json");
	}

	if(nilai == 'publish'){

		$("#btn_check").click(function(){
			$.post( base_url+"administrator/main/update_status_unpublish_to_publish", { list_status : list_status}, function( data ) {

				if(data.pesan == 'Success')
				{
					window.location.reload(true);
				}
				else
				{
					alert("Data produk gagal diubah");
				}


			}, "json");
		});

	}

	if(nilai == 'unpublish'){

		$("#btn_check").click(function(){
			$.post( base_url+"administrator/main/update_status_publish_to_unpublish", { list_status : list_status}, function( data ) {

				if(data.pesan == 'Success')
				{
					window.location.reload(true);
				}
				else
				{
					alert("Data produk gagal diubah");
				}


			}, "json");
		});

	}
});

$('#del_pro').click(function() {
    $('.check_list').attr("checked", true);
    var x = confirm('Anda yakin ingin membatalkan ?');

	if(x == true)
	{
		var nilai = $(this).val();
		var list = "";
		var base_url = "<?=base_url() ?>";
		var ch = new Array();

		$('#list_ubah_status input:checkbox:checked').each(function() {
			ch.push($(this).val());
		});

		var list_status = "["+ch+"]";

		$.post( base_url+"administrator/main/update_status_all_delete_product", { list_status : list_status}, function( data ) {

			if(data.pesan == 'Success')
			{
				window.location.reload(true);
			}
			else
			{
				alert("Data produk gagal diubah");
			}
		}, "json");
	}
	else
	{
		$('.check_list').attr("checked", false);
	}

});

$("#close_popup").click(function(){
	$('body').removeClass('modal-open');
	$('#change-category').hide().removeClass('in');
	$(".select_action").val(null);
});

$(document).on('click', '.btn-save-supplier', function() {
	var url = base_url + 'administrator/main/change_supplier';
	var product_id = $(this).attr('product-id');
	var supplier_id = $('#supplier-' + product_id + ' select').val();
	if (supplier_id) {
		$('#form_change_supplier .supplier_id').val(supplier_id);
		$('#form_change_supplier .product_id').val(product_id);
		$('#form_change_supplier').submit();
	}
});
$(document).on('click', '.btn-save-invoice', function() {
	var url = base_url + 'administrator/main/change_no_invoice';
	var product_id = $(this).attr('product-id');
	var no_invoice = $('#invoice-' + product_id + ' input').val();
	if (no_invoice) {
		$('#form_change_invoice .no_invoice').val(no_invoice);
		$('#form_change_invoice .product_id').val(product_id);
		$('#form_change_invoice').submit();
	}
});
</script>



