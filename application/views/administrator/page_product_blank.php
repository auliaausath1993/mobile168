<?php include "includes/header.php"; ?>



<!-- Content
================================================== -->

<div id="page-wrapper">
	<div class="container-fluid">

		<div class="row">
		   <div class="col-lg-12">
		    	<?php if ($this->uri->segment(4) == 'ready_stock') { ?>
		    		<h1 class="page-header">
		        		Data Produk <small>Ready Stock</small>
		        	</h1>
		        	<ul id="submenu-container" class="nav nav-tabs" style="margin-bottom: 20px;">
	                  	<li class="active"><a href="<?=base_url()?>administrator/main/add_product/ready_stock" ><b>Tambah Produk</b></a></li>
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
	        </div>
	    </div>

	    <div style="height: 300px;">
	    	<div id="alret_kt_pto" class="alert alert-danger" role="alert">
                <span><strong>Peringatan : </strong>Maaf Kuota Produk Yang Anda miliki telah habis, Silahkan hubungi Tim Marketing kami untuk memperbaharui paket anda</span>
                <a href="<?= base_url()?>administrator/main/info_paket" class="btn button-upgrade btn-danger offset10">Upgrade</a>
            </div>
	    </div>
	</div>
</div>
<?php include "includes/footer.php"; ?>
