<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
	<form role="form" action="<?= base_url('administrator/main/update_discount') ?>" method="post">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">
						Settings Informasi<small> Untuk merubah informasi toko online</small>
					</h1>
					<ol class="breadcrumb">
						<li class="active">
							<i class="fa fa-list"></i> Settings Informasi
						</li>
					</ol>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<?= $this->session->flashdata('message'); ?>
					<ul id="myTab" class="nav nav-tabs nav-justified">
						<li class="active"><a href="#discount-settings" data-toggle="tab">
							<b>Pengaturan Diskon</b></a>
						</li>
					</ul>
					<div class="panel">
						<div class="panel-body">
							<a class="btn btn-primary m-b" href="<?= base_url('administrator/main/add_discount') ?>">Tambah</a>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th class="text-center">No.</th>
										<th class="text-center">Judul</th>
										<th class="text-center">Status</th>
										<th class="text-center">Aksi</th>
									</tr>
								</thead>
								<tbody>
								<?php $no = 1; foreach ($discounts as $discount) { ?>
									<tr>
										<td class="text-center"><?= $no++ ?></td>
										<td><?= $discount->title ?></td>
										<td class="text-center">
											<?= $discount->active ? 'Aktif' : 'Non-Aktif' ?>
										</td>
										<td class="text-center">
											<a class="btn btn-primary" href="<?= base_url('administrator/main/update_discount_status/' . $discount->id . '/' . $discount->active) ?>">
												<?= $discount->active ? 'Non-Aktifkan' : 'Aktifkan' ?>
											</a>
											<a class="btn btn-success" href="<?= base_url('administrator/main/edit_discount/' . $discount->id) ?>">Ubah</a>
											<a onclick="return confirm('Hapus diskon ini ?')" class="btn btn-danger" href="<?= base_url('administrator/main/delete_discount/' . $discount->id) ?>">Hapus</a>
										</td>
									</tr>
								<?php } if (count($discounts) == 0) { ?>
									<tr>
										<td colspan="4" class="text-center">
											Data Kosong
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php include 'includes/footer.php'; ?>