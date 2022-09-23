<?php include "includes/header.php"; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Input Pengeluaran
				</h1>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?= $this->session->flashdata('message') ?>
				<?= validation_errors('<div class="alert alert-warning">', '</div>') ?>
				<form class="col-md-6" method="POST" action="<?= base_url('administrator/main/add_input_pengeluaran') ?>">
					<div class="row" style="margin-bottom: 10px">
						<div class="col-md-3">
							<label for="date">Tanggal</label>
						</div>
						<div class="col-md-3">
							<input type="text" id="date" name="date" class="datepicker form-control" data-date-format="yyyy-mm-dd" readonly required style="background-color: #FFF">
						</div>
					</div>
					<div class="row" style="margin-bottom: 10px">
						<div class="col-md-3">
							<label for="jenis_biaya_id">Jenis Biaya</label>
						</div>
						<div class="col-md-6 col-sm-10 col-xs-9">
							<select name="jenis_biaya_id" id="jenis_biaya_id" class="form-control" required>
								<option value="">Jenis Biaya</option>
								<?php foreach ($jenis_biaya as $biaya) { ?>
									<option value="<?= $biaya->id ?>"><?= $biaya->nama ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-3 col-sm-2 col-xs-3">
							<a href="#" data-toggle="modal" data-target="#modal-add-jenis-biaya" class="btn btn-primary">Add</a>
						</div>
					</div>
					<div class="row" style="margin-bottom: 10px">
						<div class="col-md-3">
							<label for="catatan">Catatan</label>
						</div>
						<div class="col-md-9">
							<textarea name="catatan" id="catatan" class="form-control" placeholder="Catatan" required></textarea>
						</div>
					</div>
					<div class="row" style="margin-bottom: 10px">
						<div class="col-md-3">
							<label for="nominal">Nominal</label>
						</div>
						<div class="col-md-6">
							<input type="number" name="nominal" id="nominal" class="form-control" placeholder="Masukan Nominal" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3 col-xs-6 text-right">
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
						<div class="col-md-4 col-xs-6">
							<button type="reset" class="btn btn-default">Reset</button>
						</div>
					</div>
				</form>
				<div class="col-md-6">
					<div class="checkbox">
						<label for="toggle-jenis-biaya">
							<input type="checkbox" value="1" id="toggle-jenis-biaya">Tampilkan Jenis Biaya
						</label>
					</div>
					<table class="table table-bordered" id="table-jenis-biaya" style="display: none">
						<thead>
							<tr>
								<th class="text-center">No.</th>
								<th class="text-center">Jenis Biaya</th>
								<th class="text-center">Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($jenis_biaya)) {
							$i = 1; foreach ($jenis_biaya as $biaya) { ?>
							<tr>
								<td class="text-center"><?= $i++ ?></td>
								<td><?= $biaya->nama ?></td>
								<td class="text-center">
									<a href="#" class="btn btn-success btn-edit-biaya" data-toggle="modal" data-target="#modal-edit-jenis-biaya" jenis-biaya-id="<?= $biaya->id ?>" jenis-biaya="<?= $biaya->nama ?>">Edit</a>
									<a href="<?= base_url('administrator/main/delete_jenis_biaya/' . $biaya->id) ?>" onclick="return confirm('Hapus jenis biaya ini ?')" class="btn btn-danger">Delete</a>
								</td>
							</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td colspan="3" class="text-center">Jenis Biaya Kosong</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<hr>
		<div id="list-pengeluaran">
			<a href="<?= base_url('administrator/main/export_input_pengeluaran') ?>" class="btn btn-danger pull-right" style="margin-bottom: 10px" target="_blank">
				<i class="fa fa-file-pdf-o"></i> Export PDF
			</a>
			<?= $output->output; ?>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-add-jenis-biaya" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Tambah Jenis Biaya</h4>
			</div>
			<div class="modal-body">
				<form action="<?= site_url('administrator/main/add_jenis_biaya') ?>" method="post">
					<div class="form-group">
						<label for="jenis_biaya">Jenis Biaya</label>
						<input type="text" class="form-control" name="jenis_biaya" id="jenis_biaya" required>
					</div>
					<button type="submit" class="btn btn-primary">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-edit-jenis-biaya" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Edit Jenis Biaya</h4>
			</div>
			<div class="modal-body">
				<form action="<?= site_url('administrator/main/edit_jenis_biaya') ?>" method="post">
					<div class="form-group">
						<input type="hidden" name="jenis_biaya_id" id="jenis-biaya-id">
						<label for="edit_jenis_biaya">Jenis Biaya</label>
						<input type="text" class="form-control" name="jenis_biaya" id="edit_jenis_biaya" required>
					</div>
					<button type="submit" class="btn btn-primary">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include 'includes/footer.php'; ?>

<script type="text/javascript">
	$('.btn-edit-biaya').click(function() {
		var id = $(this).attr('jenis-biaya-id');
		var nama = $(this).attr('jenis-biaya');
		$('#jenis-biaya-id').val(id);
		$('#edit_jenis_biaya').val(nama);
	});

	$(document).on('click', '#list-pengeluaran .delete-pengeluaran', function() {
		return confirm('Hapus pengeluaran ini ?');
	});

	$('#toggle-jenis-biaya').click(function() {
		var checked = $(this).prop('checked');
		if (checked) {
			$('#table-jenis-biaya').show();
		} else {
			$('#table-jenis-biaya').hide();
		}
	});
</script>

<?php foreach($output->js_files as $file) { ?>
	<script src="<?= $file; ?>"></script>
<?php } ?>