<?php include "includes/header.php"; ?>

<style>
    .ballon_customer {
        background-color: #ddebf9 !important;
        margin-bottom: 15px;
        padding-bottom: 15px;
        padding-top: 15px;
        padding-left: 15px;
        width: 51% !important;
    }

    .ballon_admin {
        background-color: #AAED93 !important;
        margin-bottom: 15px;
        padding-bottom: 15px;
        padding-top: 15px;
        padding-left: 15px;
        width: 51% !important;
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
	                Detail Chat
	            </h1>
	            <ol class="breadcrumb">
	                <li class="active">
	                    <a href="#" data-toggle="modal" data-target="#modal" class="list-modal btn btn-success">Replay Chat</a>
	                    <a href="<?=base_url()?>administrator/main/delete_chat/<?=$customer['id']?>" class="btn btn-danger">Delete Chat</a>
	                    <a href="<?=base_url()?>administrator/main/chatting" class="btn btn-default">Back to List</a>
	                </li>
	            </ol>
	        </div>
	    </div>
	    <!-- /.row -->

    	<?=$this->session->flashdata('message') ?>
		<div>
			<div class="container-fluid list_tiket">
				<?php foreach ($list_chat as $chat) { ?>
					<div class="row">
						<div class="col-md-12 <?= $chat->sender == 'Customer' ? 'ballon_customer' : 'ballon_admin pull-right'?>">
							<?php if ($chat->image != '') { ?>
							<a href="<?= base_url('media/images/chats/'.$chat->image) ?>" target="_blank">
								<img src="<?= base_url('media/images/chats/'.$chat->image) ?>" width="200">
							</a>
							<?php } ?>
							<p><?=$chat->pesan?></p>
							<i><?=$chat->tanggal?></i>
							<p><?=$chat->status?></p>
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
			<form method="post" action="<?= base_url('administrator/main/kirim_chat') ?>" enctype="multipart/form-data">
				<div class="modal-header" style="background-color: rgb(40, 169, 131); color: white;">
					<h4 class="modal-title">Balas chat dari <?=$customer['name']?> </h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Gambar</label>
						<input type="file" onchange="readURL(this);" id="image" name="image" accept=".png, .jpg, .jpeg">
						<img id="previewImage">
					</div>
					<div class="form-group">
						<label>Message</label>
						<textarea class="form-control" name="pesan" rows="5"></textarea>
					</div>
				</div>
				<div class="modal-footer" >
				   <input type="hidden" name="customer" value="<?=$customer['id']?>" >
				   <input type="submit" value="Balas Pesan" class="btn btn-info">
				</div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<script>
	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#previewImage')
                    .attr('src', e.target.result)
                    .width(200);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
