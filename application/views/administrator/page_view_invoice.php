<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Invoice</title>
<link href="<?= base_url() ?>application/views/backend/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<style>
body {
	margin:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
#sheet {
	background:#f6f6f6;
	padding:20px;
	width:800px;
	margin-left:auto;
	margin-right:auto;
	margin-top:10px;
	margin-bottom:20px;
}
</style>

<body>
<div id="sheet">
  <div class="row-fluid">
    <div class="span4">
      <h5>Invoice number <strong>#<?= $order['order_invoice'] ?></strong></h5>
     Order created : <i><?= date("D, d M Y H:i:s",$order['order_datetime']) ?></i>
     </div>
     <div class="span4">
     <h5>Customer <strong><?= $customer['customer_username'] ?></strong></h5>
     (<?= $customer['customer_email'] ?>)
     </div>
   	 <div class="span4">
      
        <h5>Total invoice <strong>Rp. <?= number_format($order['total_all']) ?></strong></h5>
        
        <?php
		if($order['status'] == 'Unpaid')
		{
			echo '<p><span class="label label-warning">Unpaid</span></p>';
		}
		else 
		if($order['status'] == 'Paid')
		{
      		echo '<p><span class="label label-success">Paid</span></p>';
		}
		else 
		if($order['status'] == 'Cancel')
		{
      		echo '<p><span class="label label-important">Canceled</span></p>';
		}
		?>
        
    </div>
  </div>
  <div class="row-fluid">
    <div class="span12">
      <h5>Order items</h5>
      <table class="table table-bordered table-striped">
        <thead>
          <tr class="alert alert-info">
            <th>No</th>
            <th>Product name</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          
          <?php 
		  $i = 1;
		  foreach($order_item as $items) : ?>
          <tr>
            <td><?= $i ?></td>
            <td><?= $items->product_name ?></td>
            <td><?= number_format($items->price) ?></td>
            <td><?= $items->qty ?></td>
            <td><?= number_format($items->price * $items->qty) ?></td>
          </tr>
          <?php 
		  $i++;
		  endforeach; 
		  ?>
          
        </tbody>
      </table>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span6">
      <table class="table table-bordered table-striped">
        <thead>
          <tr class="alert alert-success">
            <th>Billing information</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
            	<strong>Billing name</strong><br/>
                <?= $order['billing_name'] ?>
            </td>
          </tr>
          <tr>
            <td>
            	<strong>Billing email</strong><br/>
            	<?= $order['billing_email'] ?>
            </td>
          </tr>
          <tr>
            <td>
            	<strong>Billing address</strong><br/>
            	<?= $order['billing_address'] ?>
            </td>
          </tr>
          <tr>
            <td>
            	<strong>City</strong><br/>
      			<?= $order['billing_city'] ?>	
            </td>
          </tr>
         
          <tr>
            <td>
            	<strong>Postcode</strong><br/>
            	<?= $order['billing_postcode'] ?>
            </td>
          </tr>
          <tr>
            <td>
            	<strong>Phone</strong><br/>
                <?= $order['billing_phone']?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="span6">
      <table class="table table-bordered table-striped">
        <thead>
          <tr class="alert alert-success">
            <th>Shipping information</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
            	<strong>Name</strong><br/>
            	<?= $order['ship_name'] ?>
            </td>
          </tr>
          <tr>
            <td>
            	<strong>Address</strong><br/>
            	<?= $order['ship_address'] ?>
            </td>
          </tr>
          <tr>
            <td>
            	<strong>City</strong><br/>
                <?= $order['ship_city'] ?>
            </td>
          </tr>
          <tr>
            <td>
            	<strong>Country</strong><br/>
            	<?= $order['ship_country'] ?>
            </td>
          </tr>
          <tr>
            <td>
            	<strong>Postcode</strong><br/>
            	<?= $order['ship_postcode'] ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>