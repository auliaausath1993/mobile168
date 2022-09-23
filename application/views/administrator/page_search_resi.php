<?php include "includes/header.php";
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
 ?>

<!-- Content
================================================== -->
<section id="content" >

	<div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Resi Pengiriman <small> Untuk memasukan no. resi pengiriman</small>
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Resi Pengiriman
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row">

                	
                	<div class="col-lg-12">

                		<div class="search-wrapper text-right" style="margin-bottom:10px;">

	                	</div>
					
						<?=form_open('administrator/main/resi_update') ?>
                		
						<?=$this->session->flashdata('message') ?>
						<!---<div class="button-top text-right">
							<a id="exsport-resi" class="btn btn-success" href="#"><i class="fa fa-file-excel-o"></i> Export Excel</a>
						</div>-->						
						<table id="table-exsport" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th width="3%">No</th>
									<th width="3%">Nota</th>
									<th width="14%">Nama Pelanggan </th>
									<th width="10%">TOTAL</th>
									<th>Data Pengiriman</th>
									<th width="20%">Status</th>
									<th width="15%">No Resi </th>
								</tr>
							</thead>
							<tbody>

								<?php 
								$i = 1 * ($offset + 1);
								foreach($orders->result() as $orders) :
								$customer = $this->main_model->get_detail('customer', array('id'=> $orders->customer_id)); 
								?>
									<tr>

										<td><?=$i?></td>
										<td> #<?= $orders->id ?></td>
									<td><?php if($orders->customer_id != 0 ){echo $customer['name'].' ('.$orders->customer_id.')';}else{echo "<span style=\"color:#e23427;\"><strong>".$orders->name_customer." (Guest)</strong>"; }?></td>
									
									<td>Rp.<?=numberformat($orders->total) ?></td>
										<td>
											<strong><?= $orders->shipping_to ?></strong> (<?= $orders->phone_recipient ?>),<br/> <?= $orders->address_recipient ?>
										<td>
											<select id="status-select"  class="select-text" name="status[]" title="pilih status product anda">
												<option value="">Pilih Status Pengiriman</option>
												<option value="Belum Dikirim"<?php if($orders->shipping_status == 'Belum Dikirim') { echo 'selected="selected" '; } ?>>Belum Dikirim</option>
												<option value="Dikirim"<?php if($orders->shipping_status == 'Dikirim') { echo 'selected="selected" '; } ?>>Dikirim</option>
												<option value="Terkirim"<?php if($orders->shipping_status == 'Terkirim') { echo 'selected="selected" '; } ?>>Terkirim</option>
						                	</select>
										</td>
										<td>
											<input type="hidden" value="<?= $orders->id ?>" name="item_id[]">
											<input type="text" class="input-text" name="no_resi[]" id="no_resi_<?= $orders->id ?>" value ="<?= $orders->resi ?>" style="text-transform: uppercase;" />
										</td>

									</tr>
								<?php
								$i++;
								endforeach; 
								?>
							</tbody>
						</table>
						
						<p><button type="submit" name="go" class="btn btn-success" >UPDATE RESI</button></p>
						<?=form_close()?>
                	</div>
                </div>
                <!-- /.row -->
            </div>
    </div>

<?php include "includes/footer.php"; ?>
<script type="text/javascript">

	$( "#exsport-resi" ).click(function() {
      $('#table-exsport').tableExport({type:'excel',tableName:'download_table', ignoreColumn: [5], escape:'false'});
    });
</script>
