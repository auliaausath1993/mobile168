<?php include 'includes/header.php'; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				List Chat
			</h1>
			<ol class="breadcrumb">
				<li class="active">
					<i class="fa fa-dashboard"></i> List Chat
				</li>
			</ol>
		</div>
	</div>
	<div class="panel">
		<div class="panel-body">
			<?= $this->session->flashdata('message') ?>
			<div class="table-responsive">
				<table class="table table-bordered">
					<tr class="btn-info">
						<th>No</th>
						<th>Pelanggan</th>
						<th>Last Chat</th>
						<th>Unread Chat</th>
						<th>Action</th>
					</tr>
					<?php
					$i = 1;
					foreach ($chats as $chat) { ?>
						<tr>
							<td width="4%"><?= $offset + $i++ ?></td>
							<td><?= $chat->name.' ('.$chat->customer_id.')' ?></td>
							<td><?= date_format(date_create($chat->tanggal),'d-m-Y H:i:s') ?></td>
							<td><?= $chat->count_unread ?></td>
							<td width="18%">
								<a href="<?=base_url()?>administrator/main/detail_chat/<?=$chat->customer_id?>" class="btn btn-success btn-sm">Detail Chat</a>
								<a href="<?=base_url()?>administrator/main/delete_chat/<?=$chat->customer_id?>" class="btn btn-danger btn-sm">Delete Chat</a>
							</td>
						</tr>
					<?php } ?>
				</table>
				<?= $this->pagination->create_links(); ?>
			</div>
		</div>
	</div>
</div>
<?php include 'includes/footer.php'; ?>