<?php include "includes/header1.php"; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Update All Price (+)
				</h1>
				<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
					<li class="active"><a href="<?=base_url()?>administrator/main/get_category" ><b>Tambah Harga</b></a></li>
					<li class=""><a href="<?=base_url()?>administrator/main/get_category_min/" ><b>Kurang Harga</b></a></li>
				</ul>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?= $this->session->flashdata('message') ?>
				<?= validation_errors('<div class="alert alert-warning">', '</div>') ?>
				

				<?=form_open('administrator/main/edit_price_process');?>

				<table class="table table-bordered">
					<tbody>
						<tr style="background:#2b8ee4">
							<td style="color:white"><strong>Category</strong></td>
							<td style="color:white"><strong>Harga</strong></td>
						</tr>
						<?php foreach ($category->result() as $key): ?>
							<!--<tr>-->
								<!--	<td><?php echo $key->name ?></td>-->
								<!--	<td>-->
									<!--	    <input type="hidden" name="id_category[]" class="form-control" value="<?=$key->id?>">-->
									<!--	    <input type="text" name="update_harga[]" class="form-control" placeholder="Nominal / Persen. Ex : (2500 atau 10%)">-->
									<!--	</td>-->
									<!--</tr>-->
									<tr>
						<td><?php echo $key->name ?></td>
						<td>
							<div class="input-group" style="width: 100%; margin-top: 10px">
								<input type="hidden" name="id_category[]" class="form-control" value="<?=$key->id?>">
								<input type="text" name="update_harga[]" class="form-control" style="border-right: none;">
								<div class="input-group-btn" style="width: 18%;">
									<select class="form-control" name="tipe_update[]" style="border-top-right-radius: 4px; border-bottom-right-radius: 4px">
										<option value="Persen">%</option>
										<option value="Rupiah">Rupiah</option>
									</select>
								</div>
							</div>
						</td>
					</tr>
								<?php endforeach ?>
								<tr>
									<td></td>
									<td><button type="submit" name="submit" class="btn-primary">Update</button></td>
								</tr>
							</tbody>
						</table>
						<!--<div class="row">-->
							<!--<div class="col">-->

								<!--    <?php echo $pagination; ?>-->
								<!--</div>-->
							</div>
							<?=form_close()?>



						</div>
					</div>
					<hr>


					<?php include 'includes/footer.php'; ?>

