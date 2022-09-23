<?php include 'includes/header.php'; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Name Tag
			</h1>
			<ol class="breadcrumb">
				<li class="active">
					<i class="fa fa-dashboard"></i> Name Tag
				</li>
			</ol>
		</div>
	</div>
	<div class="panel">
		<div class="panel-body">
			<?= $this->session->flashdata('message') ?>
			<?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
			<div class="m-b text-right">
				<button type="button" data-toggle="modal" data-target="#modalAddTag" class="btn btn-success">Tambah</button>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered">
					<tr class="btn-info">
						<th>No</th>
						<th>Nama</th>
						<th>Aksi</th>
					</tr>
					<?php
					$i = 1;
					foreach ($tags as $tag) { ?>
						<tr>
							<td width="4%"><?= $i++ ?></td>
							<td><?= $tag->name ?></td>
							<td width="18%">
								<button type="button" tag-id="<?= $tag->id ?>" tag-name="<?= $tag->name ?>" data-toggle="modal" data-target="#modalEditTag" class="btn-edit-tag btn btn-success btn-sm">Ubah</button>
								<?php if ($tag->id > 1) { ?>
									<a onclick="return confirm('Anda yakin akan menghapus tag ini ?')" href="<?=base_url()?>administrator/main/delete_tag/<?=$tag->id?>" class="btn btn-danger btn-sm">Hapus</a>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalAddTag" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form method="post" action="<?= base_url('administrator/main/save_tag') ?>">
				<div class="modal-header">
					<h4 class="modal-title">Tambah Tag</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name_tag">Name Tag</label>
						<input class="form-control" type="text" name="name_tag" id="name_tag" required>
					</div>
				</div>
				<div class="modal-footer" >
					<input type="submit" value="Simpan" class="btn btn-info">
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modalEditTag" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form method="post" action="<?= base_url('administrator/main/update_tag') ?>">
				<div class="modal-header">
					<h4 class="modal-title">Ubah Tag</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="edit_name_tag">Name Tag</label>
						<input class="form-control" type="text" name="edit_name_tag" id="edit_name_tag" required>
						<input type="hidden" name="id" id="edit_id">
					</div>
				</div>
				<div class="modal-footer" >
					<input type="submit" value="Simpan" class="btn btn-info">
				</div>
			</form>
		</div>
	</div>
</div>
<?php include 'includes/footer.php'; ?>
<script>
	$('.btn-edit-tag').click(function() {
		var id = $(this).attr('tag-id');
		var name = $(this).attr('tag-name');
		$('#edit_id').val(id);
		$('#edit_name_tag').val(name);
	});
</script>