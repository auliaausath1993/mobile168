<?php include "includes/header.php"; ?>


<!-- Content



================================================== -->



<div id="page-wrapper">



    <div class="container-fluid">



	    	<!-- Page Heading -->

	    <div class="row">

	        <div class="col-lg-12">

	            <h1 class="page-header">

	                Pesan

	            </h1>



	            <ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">

                  	<li <?php if($this->uri->segment(4) == 'add'){ echo 'class="active"';} ?>><a href="<?=base_url()?>administrator/main/message_add" ><b>Kirim Pesan</b></a></li>

                  	<li><a href="<?=base_url()?>administrator/main/message_to_multiple" ><b>Kirim Pesan (Multiple)</b></a></li>

                	<li <?php if($this->uri->segment(4) == null){ echo 'class="active"';} ?>><a href="<?=base_url()?>administrator/main/message" ><b>History Pesan</b></a></li>

                </ul>



	            <ol class="breadcrumb">

	                <li class="active">

	                    <i class="fa fa-fw fa-edit"></i> Pesan

	                </li>

	            </ol>

	        </div>

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