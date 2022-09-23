<?php include 'includes/header.php'; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">List Chat Produk</h1>
			<ol class="breadcrumb">
				<li class="active">
					<i class="fa fa-dashboard"></i> List Chat Produk
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
						<th>Produk</th>
						<th>Isi Pesan</th>
						<th>Last Chat</th>
						<th>Action</th>
					</tr>
					<?php
					$i = 1;
					if (!empty($data_chat)) {
						foreach ($data_chat as $chat) { ?>
						<tr>
							<td width="4%" class="text-center"><?= $i++ ?></td>
							<td>
								<?= $chat->customer_name.' ('.$chat->customer_id.')'; ?>
								<?= $chat->count_chat > 0 ? '<span class="label label-danger">'.$chat->count_chat.'</span>' : '' ?>
							</td>
							<td><?= $chat->name_item; ?></td>
							<td><?= word_limiter($chat->content, 20) ?></td>
							<td><?= date_format(date_create($chat->create_at),'d-m-Y H:i') ?></td>
							<td width="18%">
								<a href="<?= base_url('administrator/main/detail_chat_product/'.$chat->customer_id.'/'.$chat->prod_id) ?>" class="btn btn-success btn-sm">Detail Chat</a>
								<a onclick="return confirm('Apakah yakin ingin menghapus chat ini ?')" href="<?= base_url('administrator/main/delete_chat_product/'.$chat->customer_id.'/'.$chat->prod_id)?>" class="btn btn-danger btn-sm">Delete Chat</a>
							</td>
						</tr>
						<?php }
					} else { ?>
					<tr>
						<td colspan="6" class="text-center">Tidak Ada Chat</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include 'includes/footer.php'; ?>
