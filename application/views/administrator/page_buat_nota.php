<?php include "includes/header.php"; ?>

<!-- Content
================================================== -->
<section id="content">

	<!-- Headings & Paragraph Copy -->
	<div class="row">

    <div class="span12">
	
	<h4>Welcome to Administrator Area</h4>
	<br/>
	<div>
		
		<form action="<?=base_url()?>administrator/main/buat_nota" >
		<p><button type="submit" class="btn btn-success" >BUAT NOTA</button><p/>
		
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td>Tanggal</td>
					<td><?=$orders['order_datetime'] ?></td>
				</tr>
				<tr>
					<td>Customer</td>
					<td><?=$customer['name'] ?></td>
				</tr>			
			</tbody>
		</table>
		
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					
					<th>#</th>
					<th>Customer ID</th>
					<th>Name</th>
					<th>Product</th>
					<th>Color</th>
					<th>QTY</th>
					<th>Subtotal</th>
				</tr>	
			</thead>
			<tbody>
			<?php 
			$i = 1;
			foreach($orders_item->result() as $orders) : 
			$data_customer = $this->main_model->get_detail('customer',array('id' => $orders->customer_id));
			$data_product = $this->main_model->get_detail('product',array('id' => $orders->prod_id));
			$data_color = $this->main_model->get_detail('rel_color_prod',array('id' => $orders->color_id));
			
			?>
				<tr>
					
					<td><?=$i?></td>
					<td><?=$data_customer['id']?></td>
					<td><?=$data_customer['name'] ?></td>
					<td><?=$data_product['name_item'] ?></td>
					<td><?=$data_color['color'] ?></td>
					<td><?=$orders->qty ?></td>
					<td>Rp<?=numberformat($orders->subtotal) ?></td>
					
				</tr>	
			<?php 
			$i++;
			endforeach; ?>	
			</tbody>
		</table>
		
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Total</th>
					<th><?=$total ?></th>
				</tr>
						
			</thead>
		</table>
		
		</form>
		<hr/>
	
	</div>
	</div>
	</div>
  
	

</section>

<?php include "includes/footer.php"; ?>