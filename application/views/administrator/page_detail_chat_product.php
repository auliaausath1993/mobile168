<?php include 'includes/header.php'; ?>

<style>
    .ballon_customer {
        background-color: #ddebf9 !important;
        margin-bottom: 15px;
        padding-bottom: 15px;
        padding-top: 15px;
        padding-left: 15px;
        width: 70% !important;
    }

    .ballon_admin {
        background-color: #AAED93 !important;
        margin-bottom: 15px;
        padding-bottom: 15px;
        padding-top: 15px;
        padding-left: 15px;
        width: 70% !important;
    }
</style>
<!-- Content
================================================== -->
<div id="page-wrapper">

    <div class="container-fluid">

	    	<!-- Page Heading -->
	    <div class="row">
	        <div class="col-lg-12">
	            <h1 class="page-header">
	                Detail Chat Produk
	            </h1>
	            <ol class="breadcrumb">
	            	<li class="active" style="display: block;padding-left: 120px;">
	            		<img src="<?= $product['img'] ?>" style="position: absolute; top: 10px; left: 10px;">
	            		<h3><?= $product['product_name'] ?></h3>
	            		<h4><?= $product['price'] ?></h4>
	            	</li>
	            </ol>
	            <ol class="breadcrumb">
	                <li class="active">
	                    <a href="#" data-toggle="modal" data-target="#modal" class="list-modal btn btn-success">Replay Chat</a>
	                    <a href="<?=base_url('administrator/main/delete_chat_product/'.$customer['id'].'/'.$product['id'])?>" class="btn btn-danger" onclick="return confirm('Hapus chat ?');">Delete Chat</a>
	                    <a href="<?=base_url('administrator/main/chat_product')?>" class="btn btn-default">Back to List</a>
	                </li>
	            </ol>
	        </div>
	    </div>
	    <!-- /.row -->
    
    	<?=$this->session->flashdata('message') ?>
		<div>
			<div class="container-fluid list_tiket">
				<?php foreach ($chat_product as $chat) { ?>
					<div class="row">
						<div class="col-md-12 <?= $chat['sender'] == 'Customer' ? 'ballon_customer' : 'ballon_admin pull-right'?>">
							<p><?= $chat['content']?></p>
							<i><?= $chat['create_at']?></i>
							<p><?= $chat['read_chat'] == 0 ? 'Belum dibaca' : 'Dibaca' ?></p>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		
    </div>

 </div>
 
<div class="modal fade" id="modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form method="post" action="<?=base_url()?>administrator/main/reply_chat_product">
				<div class="modal-header" style="background-color: rgb(40, 169, 131); color: white;">
					<h4 class="modal-title">Balas chat produk dari <?=$customer['name']?> </h4>
				</div>

				<div class="modal-body">
					<label>Message</label>
					<textarea class="form-control" name="content" rows="5"></textarea>
				</div>  
				<div class="modal-footer" >
				   <input type="hidden" name="customer" value="<?= $customer['id']?>">
				   <input type="hidden" name="prod_id" value="<?= $product['id']?>">
				   <input type="submit" value="Balas Pesan" class="btn btn-info">
				</div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
