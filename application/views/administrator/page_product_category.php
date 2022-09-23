<?php include "includes/header.php"; ?>
<div id="page-wrapper">



    <div class="container-fluid">



	    	<!-- Page Heading -->

	    <div class="row">

	        <div class="col-lg-12">

	            <h1 class="page-header">

	                Data Kategori

	            </h1>

	            <ol class="breadcrumb">

	                <li class="active">

	                    <i class="fa fa-fw fa-list"></i> Data Kategori

	                </li>

	            </ol>

	        </div>

	    </div>

	    <!-- /.row -->



    	<?=$this->session->flashdata('message') ?>


    	<div id="list-category-product">
    		<?= $output->output; ?>
    	</div>

    </div>



 </div>



<?php include "includes/footer.php"; ?>

<script type="text/javascript">

$("#list-category-product .product_category_delete").click(function(){

	var x = confirm('Anda yakin ingin membatalkan ?');

	if(x == true)
	{
		return true;
	}
	else
	{
		return false;
	}

});

</script>

<?php if($output->output != null) { ?>

	<?php foreach($output->js_files as $file): ?>

	<script src="<?php echo $file; ?>"></script>

	<?php endforeach; ?>

	<?php } ?>