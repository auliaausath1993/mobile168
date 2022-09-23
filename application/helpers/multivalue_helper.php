<?php

function get_client_name()
{
	$client = "Tokomobile";  // Client name
	return $client;
}

function get_package()
{
	$package = 'Personal'; // Package
	return $package;
}

function get_max_product($package)
{

	if($package == 'Personal')
	{
		$max_product = 200;
	}
	elseif($package == 'Business')
	{
		$max_product = 500;
	}
	elseif($package == 'Corporate')
	{
		$max_product = 'Unlimited';
	}
	return $max_product;
}

function get_order_status($order_status)
{
	if ($order_status == 'Keep') {
		$value = 'Transaksi ditempat';
	} else if ($order_status == 'Dropship') {
		$value = 'Pengiriman ke alamat';
	} else if ($order_status == 'Piutang') {
		$value = 'Pesanan Piutang';
	}

	return $value;
}

function get_order_payment($order_payment)
{
	if($order_payment == 'Unpaid')
	{
		$value = 'Belum Lunas';
	}
	else
	if($order_payment == 'Paid')
	{
		$value = 'Lunas';
	}

	return $value;
}

function get_order_payment_label($order_payment)
{
	if($order_payment == 'Unpaid')
	{
		$value = '<font color="red"><strong>Belum Lunas</strong></font>';
	}
	else
	if($order_payment == 'Paid')
	{
		$value = '<font color="green"><strong>Lunas</strong></font>';
	}

	return $value;
}

