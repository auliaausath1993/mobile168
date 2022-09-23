<?php include "includes/header.php"; ?>

<!-- Content
================================================== -->
<section id="content">

	<!-- Headings & Paragraph Copy -->
	<div class="row">

    <div class="span12">
	
	<h4>Pesanan Terakhir (Dropship)</h4>
	<br/>
	<div>
	
		<div class="row-fluid">
			<div class="span12">
			<?=$this->session->flashdata('message') ?>
			<table class="table table-bordered">
					<thead>
					
						<tr class="btn-inverse">
							
							<th>Cust. ID</th>
							<th>Cust. Name</th>
							<th>Item</th>
							<th>Color/Variant</th>
							<th>QTY</th>
							<th>Subtotal</th>
							<th>Time Order</th>
							<th>Action</th>
						</tr>
						<?php 
						$i = 1;
						foreach($last_order->result() as $orders): 
	
						$data_product = $this->main_model->get_detail('product',array('id' => $orders->prod_id));
						$data_color = $this->main_model->get_detail('rel_color_prod',array('id' => $orders->color_id));
						$data_customer = $this->main_model->get_detail('customer',array('id' => $orders->customer_id));
						?>	
						<tr>
							
							<td><?=$orders->customer_id ?></td>
							<td><?=$data_customer['name'] ?></td>
							<td><?=$data_product['name_item'] ?></td>
							<td><?=$data_color['color'] ?></td>
							<td><?=$orders->qty?></td>
							<td><?=$orders->subtotal?></td>
							<td><?=date('D, d-M-Y H:i:s',strtotime($orders->datetime));?></td>
							<td><a href="<?=base_url()?>backend/crud/cancel_order/<?=$orders->id?>" class="btn btn-danger">Cancel</a></td>
						<?php 
						$i++;
						endforeach; ?>
					</thead>
				</table>
				
				<?=$this->pagination->create_links(); ?>
			</div>
			
		</div>
		<hr/>
		
	</div>
	</div>
	</div>
  
	

</section>

<?php include "includes/footer.php"; ?>