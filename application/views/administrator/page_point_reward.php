<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
	<form role="form" action="<?= base_url('administrator/main/update_point_reward') ?>" method="post">
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
					<?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
					<ul id="myTab" class="nav nav-tabs nav-justified">
						<li class="active"><a href="#point-reward-settings" data-toggle="tab">
							<b>Pengaturan Point & Reward</b></a>
						</li>
					</ul>
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane active in" id="point-reward-settings">
							<div class="panel">
								<div class="panel-body">
									<div class="form-horizontal">
										<div class="form-group">
											<label class="control-label col-lg-2" for="no_wa">Expired Point</label>
											<div class="col-lg-6">
												<div class="input-group col-lg-12">
													<input id="expired_point" type="date" class="form-control" name="expired_point" value="<?= $expired_point ?>"> 
													<button onclick="return confirm('Reset point keseluruhan ?');" type="submit" name="submitreset" class="btn btn-danger">Reset Point</button>
												</div>
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Status Point & Reward</label>
											<div class="col-lg-6">
												<label class="radio-inline">
													<input type="radio" value="on" name="point_reward_status" <?= $point_reward_status == 'on' ? 'checked' : '' ?>>On
												</label>
												<label class="radio-inline">
													<input type="radio" value="off" name="point_reward_status" <?= $point_reward_status == 'off' ? 'checked' : '' ?>>off
												</label>
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2" for="nominal_to_point">Nilai Nominal Belanja Ke Point</label>
											<div class="col-lg-6">
												<div class="input-group col-lg-6">
													<input id="nominal_to_point" type="number" min="1" class="form-control" name="nominal_to_point" value="<?= $nominal_to_point ?>" placeholder="1000">
													<span class="input-group-addon">Rupiah Per Point</span>
												</div>
												<span class="text-muted">Nilai belanja yang diperlukan pelanggan untuk mendapatkan 1 point. Jika diisi dengan 1000, artinya jika pelanggan belanja senilai Rp. 1.000.000 maka pelanggan mendapatkan 1000 point.</span>
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2" for="point_to_nominal">Nilai Point Ke Nominal Belanja</label>
											<div class="col-lg-6">
												<div class="input-group col-lg-6">
													<input id="point_to_nominal" type="number" min="1" class="form-control" name="point_to_nominal" value="<?= $point_to_nominal ?>" placeholder="1000">
													<span class="input-group-addon">Rupiah Per Point</span>
												</div>
												<span class="text-muted">Nilai rupiah yang didapatkan pelanggan untuk penggunan 1 point. Jika diisi dengan 100, artinya jika pelanggan menggunakan 50 point maka pendapatkan potongan senilai Rp. 5.000.</span>
											</div>
											<div class="clearfix"></div>
										</div>
										<button type="submit" class="btn btn-success">Simpan</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php include 'includes/footer.php'; ?>