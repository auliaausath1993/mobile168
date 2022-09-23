<?php include "includes/header.php";
$data_info_kontak = $this->main_model->get_detail('content',array('name' => 'kontak'));
$data_info_rekening = $this->main_model->get_detail('content',array('name' => 'rekening'));
$data_info_nota = $this->main_model->get_detail('content',array('name' => 'nota'));
$data_info_footer = $this->main_model->get_detail('content',array('name' => 'footer'));
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
$data_value_img = $this->main_model->get_detail('content',array('name' => 'name_img_setting'));
$data_logo_nota = $this->main_model->get_detail('content',array('name' => 'image_nota'));
$data_info = $this->main_model->get_detail('content',array('name' => 'info'));
$status_aplication = $this->main_model->get_detail('content',array('name' => 'status_aplication'));
$message_off = $this->main_model->get_detail('content',array('name' => 'message_off'));
$due_date = $this->main_model->get_detail('content',array('name' => 'due_date_setting'));
$tool_tips = $this->main_model->get_detail('content',array('name' => 'tool_tips'));
$status_due_date = $this->main_model->get_detail('content',array('name' => 'status_due_date'));
$aktif_logo = $this->main_model->get_detail('content',array('name' => 'aktif_logo'));
$data_shipping = $this->main_model->get_detail('content',array('name' => 'shipping_show'));
$data_link_android = $this->main_model->get_detail('content',array('name' => 'link_android'));
$data_link_blackberry = $this->main_model->get_detail('content',array('name' => 'link_blackberry'));
$data_link_windows_phone = $this->main_model->get_detail('content',array('name' => 'link_windows_phone'));
$jne_status = $this->main_model->get_detail('content',array('name' => 'jne_status'));
$tiki_status = $this->main_model->get_detail('content',array('name' => 'tiki_status'));
$pos_status = $this->main_model->get_detail('content',array('name' => 'pos_status'));
$wahana_status = $this->main_model->get_detail('content',array('name' => 'wahana_status'));
$jnt_status = $this->main_model->get_detail('content',array('name' => 'jnt_status'));
$sicepat_status = $this->main_model->get_detail('content',array('name' => 'sicepat_status'));
$lion_status = $this->main_model->get_detail('content',array('name' => 'lion_status'));
$origin_city_id = $this->main_model->get_detail('content',array('name' => 'origin_city_id'));
$origin_city_name = $this->main_model->get_detail('content',array('name' => 'origin_city_name'));
$login_background_image = $this->main_model->get_detail('content',array('name' => 'login_background_image'));
$font_color = $this->main_model->get_detail('content',array('name' => 'font_color'));
$header_color = $this->main_model->get_detail('content',array('name' => 'header_color'));
$stok_limited = $this->main_model->get_detail('content',array('name' => 'stok_limited'));
$batas_persentase = $this->main_model->get_detail('content',array('name' => 'batas_persentase'));
$langsung_dashboard = $this->main_model->get_detail('content',array('name' => 'langsung_dashboard'));
$fitur_rekap = $this->main_model->get_detail('content',array('name' => 'fitur_rekap'));
$notifikasi = $this->main_model->get_detail('content',array('name' => 'notifikasi'));
$pincode = $this->main_model->get_detail('content',array('name' => 'pincode'));
$no_wa = $this->main_model->get_detail('content',array('name' => 'no_wa'));
$expired_point = $this->main_model->get_detail('content',array('name' => 'expired_point'));
$format_ekspedisi = $this->main_model->get_detail('content',array('name' => 'format_ekspedisi'));
$order_non_pelanggan_status = $this->main_model->get_detail('content', array('name' => 'order_non_pelanggan_status'));
$jatuh_tempo_rekap_dropship = $this->main_model->get_detail('content', array('name' => 'jatuh_tempo_rekap_dropship'));
$show_id_print_nota = $this->main_model->get_detail('content', array('name' => 'show_id_print_nota'));
$pajak_status = $this->main_model->get_detail('content', array('name' => 'pajak_status'));
$show_estimasi_print_nota = $this->main_model->get_detail('content', array('name' => 'show_estimasi_print_nota'));
$show_price = $this->main_model->get_detail('content', array('name' => 'show_price_on_print_expedition'));
$non_tarif_status = $this->main_model->get_detail('content',array('name' => 'non_tarif_status'));
?>
<!-- Content================================================== -->
<div id="page-wrapper">
	<form role="form" action="<?= base_url('administrator/main/edit_info_process/'.$this->uri->segment(4)) ?>" method="post" enctype="multipart/form-data" >
		<div class="container-fluid">
			<!-- Page Heading -->
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
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<?= $this->session->flashdata('message'); ?>
					<?php echo validation_errors(); ?>
					<ul id="myTab" class="nav nav-tabs nav-justified">
						<?php $url = $this->uri->segment(4);
						if ($url == 'toko') { ?>
							<li class="active"><a href="<?=base_url()?>administrator/main/edit_info/toko">
								<b>Informasi Website</b></a>
							</li>
							<li class=""><a href="<?=base_url()?>administrator/main/edit_info/stok">
								<b>Info Stok</b></a>
							</li>
							<li class=""><a href="<?=base_url()?>administrator/main/edit_info/image">
								<b>Info Image</b></a>
							</li>
							<li class=""><a href="<?=base_url()?>administrator/main/edit_info/nota">
								<b>Info Nota</b></a>
							</li>
							<!-- <li class=""><a href="<?=base_url()?>administrator/main/edit_info/link">
								<b>Info Link</b></a>
							</li> -->
							<li class=""><a href="<?=base_url()?>administrator/main/edit_info/aplikasi">
								<b>Info Aplikasi</b></a>
							</li>
							<li class=""><a href="<?=base_url()?>administrator/main/edit_info/ekspedisi">
								<b>Info Ekspedisi</b></a>
							</li>
						<?php } ?>
					</ul>
					<div id="myTabContent" class="tab-content" >
						<?php if ($url == 'toko') { ?>
							<div class="tab-pane active in" id="kontak">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="user_name">Info</label>
										<div class="col-lg-6">
											<textarea name="info_toko" rows="5" class="form-control col-lg-8"><?=$data_info['value'] ?></textarea>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2" for="user_name">Info Kontak</label>
										<div class="col-lg-6">
											<textarea name="info_kontak" rows="5" class="form-control col-lg-8"><?=$data_info_kontak['value'] ?></textarea>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2" for="user_email">Info Rekening</label>
										<div class="col-lg-6">
											<textarea name="info_rekening" rows="5" class="form-control col-lg-8"><?=$data_info_rekening['value'] ?></textarea>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2" for="no_wa">No. Whatsapp</label>
										<div class="col-lg-6">
											<div class="input-group col-lg-12">
												<span class="input-group-addon">+62</span>
												<input id="no_wa" type="text" class="form-control" name="no_wa" placeholder="8xxxxxx" value="<?= $no_wa['value'] ?>">
											</div>
										</div>
										<div class="clearfix"></div>
									</div>

									<!-- <div class="form-group">
										<label class="control-label col-lg-2" for="no_wa">Expired Point</label>
										<div class="col-lg-6">
											<div class="input-group col-lg-12">
												<input id="expired_point" type="date" class="form-control" name="expired_point" value="<?= $expired_point['value'] ?>">
											</div>
										</div>
										<div class="clearfix"></div>
									</div> -->

									<div class="form-group">
										<label class="control-label col-lg-2" for="background_login">Gambar Background Login</label>
										<div class="col-lg-6">
											<?php if ($login_background_image['value'] == null) { ?>
												<input type="file" name="background_login" class="form-control col-lg-8">
												<span style="display: inline-block; color: red; margin-top: 10px;">* Untuk ukuran gambar diharapkan 1440px x 900px</span>
											<?php }else{ ?>
												<div class="view-background">
													<div class="col-md-6">
														<img width="400" src="<?=base_url();?>media/images/<?=$login_background_image['value'] ?>">
													</div>
													<div class="col-md-3">
														<button type="button" class="btn btn-danger" id="delete-background"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
													</div>
													<div class="clearfix"></div>
												</div>
											<?php } ?>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2">Warna Header</label>
										<div class="col-lg-6 form-inline">
											<input class="form-control" id="header-color" type="color" name="header_color" value="<?=$header_color['value']?>" onchange="javascript:document.getElementById('chosen-header-color').value = document.getElementById('header-color').value;" required style="width: 50px">
											<input id="chosen-header-color"  type="text"  class="form-control" value="<?= $header_color['value'] ?>" required>

											<script>
												$("#chosen-header-color").change(function(){
													var value_color = $(this).val();
													$("#header-color").val(value_color);
												});
											</script>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2">Warna Huruf</label>
										<div class="col-lg-6 form-inline">
											<input id="font-color" type="color" name="font_color" value="<?=$font_color['value']?>" onchange="javascript:document.getElementById('chosen-font-color').value = document.getElementById('font-color').value;" required style="width: 50px" class="form-control">
											<input id="chosen-font-color"  type="text"  class="form-control" value="<?= $font_color['value'] ?>" required>

											<script>
												$("#chosen-font-color").change(function(){
													var value_color = $(this).val();
													$("#font-color").val(value_color);
												});
											</script>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2">Suara Notifikasi</label>
										<div class="col-lg-6">
											<div class="checkbox">
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Chatting') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Chatting">Chatting
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Keep') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Keep">Keep
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Rekap') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Rekap">Rekap
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Piutang') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Piutang">Piutang
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Dropship') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Dropship">Dropship
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Konfirmasi') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Konfirmasi">Konfirmasi
												</label>
											</div>
										</div>
									</div>
									<hr>
									<?php
									$user_level = $this->session->userdata('webadmin_user_level');
									if ($user_level == 'Superuser' || $user_level == 'Owner') { ?>
										<div class="form-group">
											<label class="control-label col-lg-2">Pincode</label>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="pincode" value="<?= $pincode['value'] ?>" required>
											</div>
										</div>
										<hr>
									<?php } ?>
									<div class="form-group">
										<label class="control-label col-lg-2">Status Buat Order Non Pelanggan</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="order_non_pelanggan_status" id="order_non_pelanggan_status1" value="on" <?php if($order_non_pelanggan_status['value'] == 'on') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="order_non_pelanggan_status" id="order_non_pelanggan_status2" value="off" <?php if($order_non_pelanggan_status['value'] == 'off') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
									</div>


								</div>
							</div>
							<!-- <div class="tab-pane" id="website_setting">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="background_login">Gambar Background Login</label>
										<div class="col-lg-6">
											<?php if ($login_background_image['value'] == null) { ?>
												<input type="file" name="background_login" class="form-control col-lg-8">
												<span style="display: inline-block; color: red; margin-top: 10px;">* Untuk ukuran gambar diharapkan 1440px x 900px</span>
											<?php }else{ ?>
												<div class="view-background">
													<div class="col-md-6">
														<img width="400" src="<?=base_url();?>media/images/<?=$login_background_image['value'] ?>">
													</div>
													<div class="col-md-3">
														<button type="button" class="btn btn-danger" id="delete-background"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
													</div>
													<div class="clearfix"></div>
												</div>
											<?php } ?>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2">Warna Header</label>
										<div class="col-lg-6 form-inline">
											<input class="form-control" id="header-color" type="color" name="header_color" value="<?=$header_color['value']?>" onchange="javascript:document.getElementById('chosen-header-color').value = document.getElementById('header-color').value;" required style="width: 50px">
											<input id="chosen-header-color"  type="text"  class="form-control" value="<?= $header_color['value'] ?>" required>

											<script>
												$("#chosen-header-color").change(function(){
													var value_color = $(this).val();
													$("#header-color").val(value_color);
												});
											</script>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2">Warna Huruf</label>
										<div class="col-lg-6 form-inline">
											<input id="font-color" type="color" name="font_color" value="<?=$font_color['value']?>" onchange="javascript:document.getElementById('chosen-font-color').value = document.getElementById('font-color').value;" required style="width: 50px" class="form-control">
											<input id="chosen-font-color"  type="text"  class="form-control" value="<?= $font_color['value'] ?>" required>

											<script>
												$("#chosen-font-color").change(function(){
													var value_color = $(this).val();
													$("#font-color").val(value_color);
												});
											</script>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2">Suara Notifikasi</label>
										<div class="col-lg-6">
											<div class="checkbox">
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Chatting') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Chatting">Chatting
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Keep') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Keep">Keep
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Rekap') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Rekap">Rekap
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Piutang') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Piutang">Piutang
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Dropship') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Dropship">Dropship
												</label>
												<label>
													<input type="checkbox" <?= strpos($notifikasi['value'], 'Konfirmasi') !== FALSE ? 'checked' : '' ?> name="notifikasi[]" value="Konfirmasi">Konfirmasi
												</label>
											</div>
										</div>
									</div>
									<hr>
									<?php
									$user_level = $this->session->userdata('webadmin_user_level');
									if ($user_level == 'Superuser' || $user_level == 'Owner') { ?>
										<div class="form-group">
											<label class="control-label col-lg-2">Pincode</label>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="pincode" value="<?= $pincode['value'] ?>" required>
											</div>
										</div>
										<hr>
									<?php } ?>
									<div class="form-group">
										<label class="control-label col-lg-2">Status Buat Order Non Pelanggan</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="order_non_pelanggan_status" id="order_non_pelanggan_status1" value="on" <?php if($order_non_pelanggan_status['value'] == 'on') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="order_non_pelanggan_status" id="order_non_pelanggan_status2" value="off" <?php if($order_non_pelanggan_status['value'] == 'off') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
									</div>
									<hr>
								</div>
							</div> -->
						<?php } else if ($url == 'nota') { ?>
							<ul id="myTab" class="nav nav-tabs nav-justified">
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/toko">
									<b>Informasi Website</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/stok">
									<b>Info Stok</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/image">
									<b>Info Image</b></a>
								</li>
								<li class="active"><a href="<?=base_url()?>administrator/main/edit_info/nota">
									<b>Info Nota</b></a>
								</li>
								<!-- <li class=""><a href="<?=base_url()?>administrator/main/edit_info/link">
									<b>Info Link</b></a>
								</li> -->
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/aplikasi">
									<b>Info Aplikasi</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/ekspedisi">
									<b>Info Ekspedisi</b></a>
								</li>
							</ul>
							<div class="tab-pane active in" id="nota">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Tampilkan Data Shipping</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="data_shipping" id="data_shipping1" value="1" <?php if($data_shipping['value'] == 1) { echo 'checked="checked"'; } ?> >
												ON
											</label>
											<label>
												<input type="radio" name="data_shipping" id="data_shipping2" value="2" <?php if($data_shipping['value'] == 2) { echo 'checked="checked"'; } ?> >
												OFF
											</label><br>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="show_price1">Tampilkan Harga Pada Label Shipping</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="show_price" id="show_price1" value="ON" <?php if($show_price['value'] == 'ON') { echo 'checked="checked"'; } ?> >
												ON
											</label>
											<label>
												<input type="radio" name="show_price" id="show_price2" value="OFF" <?php if($show_price['value'] == 'OFF') { echo 'checked="checked"'; } ?> >
												OFF
											</label><br>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Format Data Ekspedisi</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="format_ekspedisi" id="format_ekspedisi1" value="1" <?php if($format_ekspedisi['value'] == 1) { echo 'checked="checked"'; } ?> >
												Versi 1
											</label>&nbsp;&nbsp;
											<label>
												<input type="radio" name="format_ekspedisi" id="format_ekspedisi2" value="2" <?php if($format_ekspedisi['value'] == 2) { echo 'checked="checked"'; } ?> >
												Versi 2
											</label>&nbsp;&nbsp;
											<label>
												<input type="radio" name="format_ekspedisi" id="format_ekspedisi2" value="3" <?php if($format_ekspedisi['value'] == 3) { echo 'checked="checked"'; } ?> >
												Versi 3
											</label><br>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Tampilkan ID Pesanan (Pada Format Ekspedisi Versi 2)</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="show_id_print_nota" id="show_id_print_nota1" value="on" <?php if($show_id_print_nota['value'] == 'on') { echo 'checked="checked"'; } ?> >
												ON
											</label>
											<label>
												<input type="radio" name="show_id_print_nota" id="show_id_print_nota2" value="off" <?php if($show_id_print_nota['value'] == 'off') { echo 'checked="checked"'; } ?> >
												OFF
											</label><br>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Tampilkan Estimasi Ongkir (Pada Format Ekspedisi Versi 2)</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="show_estimasi_print_nota" id="show_estimasi_print_nota1" value="on" <?php if($show_estimasi_print_nota['value'] == 'on') { echo 'checked="checked"'; } ?> >
												ON
											</label>
											<label>
												<input type="radio" name="show_estimasi_print_nota" id="show_estimasi_print_nota2" value="off" <?php if($show_estimasi_print_nota['value'] == 'off') { echo 'checked="checked"'; } ?> >
												OFF
											</label><br>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Tampilkan Logo pada Nota</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="aktif_logo" id="aktif_logo1" value="1" <?php if($aktif_logo['value'] == 1) { echo 'checked="checked"'; } ?> >
												ON
											</label>
											<label>
												<input type="radio" name="aktif_logo" id="aktif_logo2" value="2" <?php if($aktif_logo['value'] == 2) { echo 'checked="checked"'; } ?> >
												OFF
											</label><br>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="logo_nota">Logo Nota</label>
										<div class="col-lg-6">
											<?php if ($data_logo_nota['value'] == null) { ?>
												<input type="file" name="logo_nota" class="form-control col-lg-8">
												<span style="display: inline-block; color: red; margin-top: 10px;">* Untuk ukuran logo nota diharapkan 150px x 100px</span>
											<?php }else{ ?>
												<div class="view-images">
													<div class="col-md-2">
														<img src="<?=base_url();?>media/images/<?=$data_logo_nota['value'] ?>">
													</div>
													<div class="col-md-3">
														<button type="button" class="btn btn-danger" id="delete-logo"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
													</div>
													<div class="clearfix"></div>

												</div>
											<?php } ?>
										</div>
										<div class="clearfix"></div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-2" for="user_email">Header Struk/Nota</label>
										<div class="col-lg-6">
											<textarea name="info_nota" rows="5" class="form-control col-lg-8"><?=$data_info_nota['value'] ?></textarea>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2" for="user_email">Footer Struk/Nota</label>
										<div class="col-lg-6">
											<textarea name="footer_nota" rows="5" class="form-control col-lg-8"><?=$data_info_footer['value'] ?></textarea>
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						<?php } else if ($url == 'image') { ?>
							<ul id="myTab" class="nav nav-tabs nav-justified">
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/toko">
									<b>Informasi Website</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/stok">
									<b>Info Stok</b></a>
								</li>
								<li class="active"><a href="<?=base_url()?>administrator/main/edit_info/image">
									<b>Info Image</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/nota">
									<b>Info Nota</b></a>
								</li>
								<!-- <li class=""><a href="<?=base_url()?>administrator/main/edit_info/link">
									<b>Info Link</b></a>
								</li> -->
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/aplikasi">
									<b>Info Aplikasi</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/ekspedisi">
									<b>Info Ekspedisi</b></a>
								</li>
							</ul>
							<div class="tab-pane active in" id="image">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Pengaturan Nama Image</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="name_image" id="name_image1" value="1" <?php if($data_value_img ['value'] == 1) { echo 'checked="checked"'; } ?> >
												Sesuai dengan nama image waktu anda upload.
											</label><br>
											<label>
												<input type="radio" name="name_image" id="name_image2" value="2" <?php if($data_value_img ['value'] == 2) { echo 'checked="checked"'; } ?> >
												Berdasarkan nama toko anda.
											</label><br>
											<label>
												<input type="radio" name="name_image" id="name_image2" value="3" <?php if($data_value_img ['value'] == 3) { echo 'checked="checked"'; } ?> >
												Berdasarkan nama produk anda.
											</label><br>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
								</div>
							</div>
						<?php } else if ($url == 'stok') { ?>
							<ul id="myTab" class="nav nav-tabs nav-justified">
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/toko">
									<b>Informasi Website</b></a>
								</li>
								<li class="active"><a href="<?=base_url()?>administrator/main/edit_info/stok">
									<b>Info Stok</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/image">
									<b>Info Image</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/nota">
									<b>Info Nota</b></a>
								</li>
								<!-- <li class=""><a href="<?=base_url()?>administrator/main/edit_info/link">
									<b>Info Link</b></a>
								</li> -->
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/aplikasi">
									<b>Info Aplikasi</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/ekspedisi">
									<b>Info Ekspedisi</b></a>
								</li>
							</ul>
							<div class="tab-pane active in" id="stok">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Pengaturan Stock</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="view_stock" id="view_stock1" value="1" <?php if($data_value_stock ['value'] == 1) { echo 'checked="checked"'; } ?>>
												Tampilkan Stock
											</label><br>
											<label>
												<input type="radio" name="view_stock" id="view_stock2" value="2" <?php if($data_value_stock ['value'] == 2) { echo 'checked="checked"'; } ?> >
												Sembunyikan Stock pada aplikasi anda
											</label><br>
											<label>
												<input type="radio" name="view_stock" id="view_stock3" value="3" <?php if($data_value_stock ['value'] == 3) { echo 'checked="checked"'; } ?> >
												Non aktifkan fitur stock
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Pengaturan Stock Limited</label>
										<div class="col-lg-6">
											<input type="number" name="stok_limited" class="col-lg-1 form-control" value="<?=$stok_limited['value'] ?>">
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Batas persentase stok</label>
										<div class="col-lg-6">
											<input style="width: 10%" type="number" name="batas_persentase" class="col-lg-1 form-control" value="<?=$batas_persentase['value'] ?>"> <span style="font-size: 24px">%</span>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
						<?php } else if ($url == 'link') { ?>
							<ul id="myTab" class="nav nav-tabs nav-justified">
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/toko">
									<b>Informasi Website</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/stok">
									<b>Info Stok</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/image">
									<b>Info Image</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/nota">
									<b>Info Nota</b></a>
								</li>
								<!-- <li class="active"><a href="<?=base_url()?>administrator/main/edit_info/link">
									<b>Info Link</b></a>
								</li> -->
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/aplikasi">
									<b>Info Aplikasi</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/ekspedisi">
									<b>Info Ekspedisi</b></a>
								</li>
							</ul>
							<div class="tab-pane active in" id="aplikasi">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="link_android">Link Android</label>
										<div class="col-lg-6">
											<input type="text" name="link_android" class="form-control col-lg-8" value="<?=$data_link_android['value'] ?>">
										</div>
										<div class="clearfix"></div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-2" for="link_blackberry">Link Blackberry</label>
										<div class="col-lg-6">
											<input type="text"  name="link_blackberry"  class="form-control col-lg-8" value="<?=$data_link_blackberry['value'] ?>" >
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						<?php } else if ($url == 'aplikasi') { ?>
							<ul id="myTab" class="nav nav-tabs nav-justified">
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/toko">
									<b>Informasi Website</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/stok">
									<b>Info Stok</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/image">
									<b>Info Image</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/nota">
									<b>Info Nota</b></a>
								</li>
								<!-- <li class=""><a href="<?=base_url()?>administrator/main/edit_info/link">
									<b>Info Link</b></a>
								</li> -->
								<li class="active"><a href="<?=base_url()?>administrator/main/edit_info/aplikasi">
									<b>Info Aplikasi</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/ekspedisi">
									<b>Info Ekspedisi</b></a>
								</li>
							</ul>
							<div class="tab-pane active in" id="aplikasi_setting">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Pengaturan Masa jatuh Tempo</label>
										<?php $due_date = explode(' ', $due_date['value']); ?>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="status_due_date" id="status_due_date1" value="ON" <?php if($status_due_date ['value'] == 'ON') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="status_due_date" id="status_due_date2" value="OFF" <?php if($status_due_date ['value'] == 'OFF') { echo 'checked="checked"'; } ?> >
												Off
											</label>
											<br>
											<?php if ($status_due_date ['value'] == 'ON') { ?>
												<input type="number" class="form-control" name="due_date" value="<?= $due_date[0] ?>">
												<label>
													<input type="radio" name="due_date_tipe" id="due_date_tipe1" value="days" <?php if($due_date[1] == 'days') { echo 'checked="checked"'; } ?> >
													Hari
												</label>
												<label>
													<input type="radio" name="due_date_tipe" id="due_date_tipe2" value="hour" <?php if($due_date[1] == 'hour') { echo 'checked="checked"'; } ?> >
													Jam
												</label>
											<?php }else{ ?>
												<div  class="due_date_select" hidden>
													<input type="number" name="due_date" value="<?= $due_date[0] ?>">
													<label>
														<input type="radio" name="due_date_tipe" id="due_date_tipe1" value="days" <?php if($due_date[1] == 'days') { echo 'checked="checked"'; } ?> >
														Hari
													</label>
													<label>
														<input type="radio" name="due_date_tipe" id="due_date_tipe2" value="hour" <?php if($due_date[1] == 'hour') { echo 'checked="checked"'; } ?> >
														Jam
													</label>
												</div>
											<?php } ?>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Pengaturan Masa jatuh Tempo Rekap & Dropship</label>
										<?php $jatuh_tempo_rekap_dropship = explode(' ', $jatuh_tempo_rekap_dropship['value']); ?>
										<div class="col-lg-6">
											<input type="number" class="form-control" name="jatuh_tempo_rekap_dropship" value="<?= $jatuh_tempo_rekap_dropship[0] ?>">
											<label>
												<input type="radio" name="jatuh_tempo_rekap_dropship_tipe" id="jatuh_tempo_rekap_dropship_tipe1" value="days" <?php if($jatuh_tempo_rekap_dropship[1] == 'days') { echo 'checked="checked"'; } ?> >
												Hari
											</label>
											<label>
												<input type="radio" name="jatuh_tempo_rekap_dropship_tipe" id="jatuh_tempo_rekap_dropship_tipe2" value="hour" <?php if($jatuh_tempo_rekap_dropship[1] == 'hour') { echo 'checked="checked"'; } ?> >
												Jam
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Tampilkan Panduan</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="tool_tips" id="tool_tips1" value="ON" <?php if($tool_tips ['value'] == 'ON') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="tool_tips" id="tool_tips2" value="OFF" <?php if($tool_tips ['value'] == 'OFF') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Pengaturan Status Tampilan Masuk Aplikasi Anda</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="langsung_dashboard" id="langsung_dashboard1" value="ON" <?php if($langsung_dashboard ['value'] == 'ON') { echo 'checked="checked"'; } ?> >
												On (Langsung Masuk Aplikasi)
											</label>
										</div>
										<div class="col-lg-6">

											<label>
												<input type="radio" name="langsung_dashboard" id="langsung_dashboard2" value="OFF" <?php if($langsung_dashboard ['value'] == 'OFF') { echo 'checked="checked"'; } ?> >
												Off (Harus Login)
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Pengaturan Fitur Rekap / COD</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="fitur_rekap" id="fitur_rekap1" value="ON" <?php if($fitur_rekap ['value'] == 'ON') { echo 'checked="checked"'; } ?> >
												On
											</label>
										</div>
										<div class="col-lg-6">

											<label>
												<input type="radio" name="fitur_rekap" id="fitur_rekap2" value="OFF" <?php if($fitur_rekap ['value'] == 'OFF') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Pengaturan Status Aplikasi Anda</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="status_aplication" id="status_aplication1" value="ON" <?php if($status_aplication ['value'] == 'ON') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="status_aplication" id="status_aplication2" value="OFF" <?php if($status_aplication ['value'] == 'OFF') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="user_email">Pesan Saat Aplikasi OFF</label>
										<div class="col-lg-6">
											<textarea name="message_off" rows="5" class="form-control col-lg-8"><?=$message_off['value'] ?></textarea>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<!-- <div class="form-group">
										<label class="control-label col-lg-2" for="pajak_status">Pengaturan Status Pajak</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="pajak_status" id="pajak_status1" value="Enabled" <?php if($pajak_status['value'] == 'Enabled') { echo 'checked="checked"'; } ?> >
												Enabled
											</label>
											<label>
												<input type="radio" name="pajak_status" id="pajak_status2" value="Disabled" <?php if($pajak_status['value'] == 'Disabled') { echo 'checked="checked"'; } ?> >
												Disabled
											</label>
										</div>
										<div class="clearfix"></div>
									</div> -->
								</div>
							</div>
						<?php } else if ($url == 'ekspedisi') { ?>
							<ul id="myTab" class="nav nav-tabs nav-justified">
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/toko">
									<b>Informasi Website</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/stok">
									<b>Info Stok</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/image">
									<b>Info Image</b></a>
								</li>
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/nota">
									<b>Info Nota</b></a>
								</li>
								<!-- <li class=""><a href="<?=base_url()?>administrator/main/edit_info/link">
									<b>Info Link</b></a>
								</li> -->
								<li class=""><a href="<?=base_url()?>administrator/main/edit_info/aplikasi">
									<b>Info Aplikasi</b></a>
								</li>
								<li class="active"><a href="<?=base_url()?>administrator/main/edit_info/ekspedisi">
									<b>Info Ekspedisi</b></a>
								</li>
							</ul>
							<div class="tab-pane active in" id="ekspedisi_setting">
								<?php if ($jne_status['value'] == 'not_available' && $tiki_status['value'] == 'not_available' && $pos_status['value'] == 'not_available' && $wahana_status['value'] == 'not_available' && $jnt_status['value'] == 'not_available' && $sicepat_status['value'] == 'not_available' && $lion_status['value'] == 'not_available' && $lion_status['value'] == 'not_available') { ?>
									<div style="margin-top:10px;" class="alert alert-danger">Available ekspedisi tidak ada, data Ekspedisi akan kosong dan tarif akan bernilai kosong / Nol => '0'</div>
								<?php } ?>
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Ekspedisi JNE</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="jne_status" id="tool_tips1" value="available" <?php if($jne_status ['value'] == 'available') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="jne_status" id="tool_tips2" value="not_available" <?php if($jne_status ['value'] == 'not_available') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Ekspedisi Tiki</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="tiki_status" id="tool_tips1" value="available" <?php if($tiki_status ['value'] == 'available') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="tiki_status" id="tool_tips2" value="not_available" <?php if($tiki_status ['value'] == 'not_available') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Ekspedisi POS</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="pos_status" id="tool_tips1" value="available" <?php if($pos_status ['value'] == 'available') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="pos_status" id="tool_tips2" value="not_available" <?php if($pos_status ['value'] == 'not_available') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Ekspedisi Wahana</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="wahana_status" id="tool_tips1" value="available" <?php if($wahana_status ['value'] == 'available') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="wahana_status" id="tool_tips2" value="not_available" <?php if($wahana_status ['value'] == 'not_available') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Ekspedisi J&T</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="jnt_status" id="tool_tips1" value="available" <?php if($jnt_status ['value'] == 'available') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="jnt_status" id="tool_tips2" value="not_available" <?php if($jnt_status ['value'] == 'not_available') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Ekspedisi Sicepat</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="sicepat_status" id="tool_tips1" value="available" <?php if($sicepat_status ['value'] == 'available') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="sicepat_status" id="tool_tips2" value="not_available" <?php if($sicepat_status ['value'] == 'not_available') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Ekspedisi Lion</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="lion_status" id="tool_tips1" value="available" <?php if($lion_status ['value'] == 'available') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="lion_status" id="tool_tips2" value="not_available" <?php if($lion_status ['value'] == 'not_available') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2">Non Tarif</label>
										<div class="col-lg-6">
											<label>
												<input type="radio" name="non_tarif_status" value="available" <?php if($non_tarif_status ['value'] == 'available') { echo 'checked="checked"'; } ?> >
												On
											</label>
											<label>
												<input type="radio" name="non_tarif_status" value="not_available" <?php if($non_tarif_status ['value'] == 'not_available') { echo 'checked="checked"'; } ?> >
												Off
											</label>
										</div>
										<div class="clearfix"></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="control-label col-lg-2" for="stock_setting">Daerah Asal Pengiriman</label>
										<div class="col-lg-6" >
											<div class="col-lg-4" style="margin-left:-15px;">
												<select name="origin_city_id" class="form-control" id="origin_city_id">

												</select>
												<input type="hidden" name="origin_city_name" id="origin_city_name" value="<?=$origin_city_name['value']?>">
											</div>
										</div>
										<div class="clearfix"></div>
									</div>

									<hr>
								</div>
							</div>
						<?php } ?>
					</div>
					<input type="hidden" name="logo_lama" value="<?=$data_logo_nota['value'] ?>">
					<input type="hidden" name="background_lama" value="<?=$login_background_image['value'] ?>">
					<button type="submit" name="update_settings" class="btn btn-success">Update Settings</button>
				</div>
			</div>
			<!-- /.row -->
		</div>
	</form>
</div>
<script src="<?= base_url() ?>application/views/backend/assets/js/nicedit/nicEdit.js" type="text/javascript"></script>
<script type="text/javascript">
	var token_api = "8abf4902a0db27dcb7f62a01c2fd0d0a";
	var domain_api = "ratuwedges.com";
	var base_url_api = "https://adminpanel.tokomobile.co.id/ongkir/development/api/";
	var origin_city_id = "<?=$origin_city_id['value']?>";
	var base_url = "<?=base_url()?>";
	$("#origin_city_id").html("<option>Loading ...</option>");
	$.get(base_url_api+"/city",{token : token_api,domain : domain_api},

		function(data)
		{
			if(data.status == 'Success')
			{
				var list_data = data.result;
				var list_length = data.result.length;
				$("#origin_city_id").html("");
				for(var i = 0; i < list_length; i++)
				{
					if(origin_city_id == list_data[i].city_id)
					{
						var city = "<option value='"+list_data[i].city_id+"' selected='selected' selected='selected'>"+list_data[i].city_name+"</option>";
					}
					else
					{
						var city = "<option value='"+list_data[i].city_id+"'>"+list_data[i].city_name+"</option>";
					}
					$("#origin_city_id").append(city);
				}
			}

		},"json");


	$('#origin_city_id').change(function(){
		var kota_id = $(this).val();
		$.get(base_url_api+"/city", { domain: domain_api, token: token_api, city_id : kota_id },
			function(data){
				var name = data.result.city_name;
				$("#origin_city_name").val(name);
			}, "json");
	});

	$('#delete-logo').click(function(event){
		$(".view-images").html("<input type=\"file\" name=\"logo_nota\" class=\"form-control col-lg-8\">");
	});

	$('#delete-background').click(function(event) {
		if (confirm('Hapus gambar ?')) {
			$.get(base_url + 'administrator/main/delete_background', function() {
				alert('Gambar berhasil dihapus');
				$(".view-background").html('<input type="file" name="background_login" class="form-control col-lg-8">');
			});
		}
	});

	$('#status_due_date1').click(function () {
		$(".due_date_select").show();
	});

	$('#status_due_date2').click(function () {
		$(".due_date_select").hide();});bkLib.onDomLoaded(function() {
			new nicEditor().panelInstance('signature');
			new nicEditor().panelInstance('new_member_registration');
			new nicEditor().panelInstance('new_order_invoice');
			new nicEditor().panelInstance('order_invoice_paid');
			new nicEditor().panelInstance('order_invoice_cancel');
			new nicEditor().panelInstance('forgot_password');
			new nicEditor().panelInstance('newsletter_registration');
			new nicEditor().panelInstance('admin_new_order');
			new nicEditor().panelInstance('admin_new_payment_confirmation');
		});
	</script>
	<?php include "includes/footer.php"; ?>