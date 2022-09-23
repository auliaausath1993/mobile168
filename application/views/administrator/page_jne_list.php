<?php include "includes/header.php"; ?>
<!-- Content
================================================== -->
<div id="page-wrapper">

    <div class="container-fluid">

	    	<!-- Page Heading -->
	    <div class="row">
	        <div class="col-lg-12">
	            <h1 class="page-header">
					<?php if($this->uri->segment(3) == 'tarif_ekspedisi'){?>
	                Data Tarif JNE
					<?php }else{?>
	                Data JNE
					<?php }?>
	            </h1>
	            <ol class="breadcrumb">
	                <li class="active">
	                    <i class="fa fa-fw fa-edit"></i> Data JNE
	                </li>
	            </ol>
	        </div>
			<?php if($this->uri->segment(3) != 'tarif_ekspedisi'){?>
	        <div class="col-lg-12" style="margin-bottom:25px;">
				<a href="<?=base_url()?>administrator/main/tarif_ekspedisi" class="btn btn-success">Edit Data Kecamatan</a>
	        </div>
			<?php }?>
	    </div>
	    <!-- /.row -->

    	<?=$this->session->flashdata('message') ?>

		<?= $output->output; ?>
    </div>

 </div>

<?php include "includes/footer.php"; ?>
<?php if($output->output != null) { ?>
	<?php foreach($output->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
	<?php } ?>