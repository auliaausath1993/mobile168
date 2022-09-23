<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// NAMA ONLINE SHOP
$config['tokomobile_online_shop'] = 'Fresha';

// NAMA DOMAIN
$config['tokomobile_domain'] = 'mettaabadijaya.com';

// NAMA EXPERIED DATE
$config['tokomobile_experied_date'] = '2023-04-22';

// NAMA ACTIVATION CODE
$config['tokomobile_activation_code'] = 'D0D7E51467B8E234458E';

// PACKAGE (Personal, Business, Corporate, Enterprise)
$config['tokomobile_package'] = 'Enterprise';

// REGISTRASI MEMBER BARU (Moderate, Active)
$config['tokomobile_member_reg'] = 'Moderate';

// TARIF JNE (reg, oke, yes)
$config['tokomobile_tarif_jne'] = 'reg';

// TOKEN
$config['tokomobile_token'] = '602b6defc842336e32c04d4d1743fcf8';

// TYPE OF TOKOMOBILE REQUEST (Standar, Custom)
$config['tokomobile_type'] = 'Standar';

// WHITE LABEL OF TOKOMOBILE (Yes, No)
$config['tokomobile_white_label'] = 'No';

/****************************************/

$config['FCM_APIKEY'] = 'AAAAGqoD4tM:APA91bH01aoXT99QOIXnz1ORZla5w1x8NgPznINZxmH_pHUd7LEa2wHM-AYrONeLZNIVBHrkmDuKx2a85uenk63-q0St7CCggyWAQ2MDPxgvawK9DnM-I_EhXYjtiL91aV7_I02RLujz';

/* Warning : Bagian dibawah Tidak perlu diganti */
// Limiter
if($config['tokomobile_package'] == 'Personal')
{
	$config['tokomobile_product_limit'] = 200;
	$config['tokomobile_customer_limit'] = 300;
	
}
elseif($config['tokomobile_package'] == 'Business')
{
	$config['tokomobile_product_limit'] = 500;
	$config['tokomobile_customer_limit'] = 600;
}
elseif($config['tokomobile_package'] == 'Corporate')
{
	$config['tokomobile_product_limit'] = 'Unlimited';
	$config['tokomobile_customer_limit'] = 1000;
}
elseif($config['tokomobile_package'] == 'Enterprise')
{
	$config['tokomobile_product_limit'] = 'Unlimited';
	$config['tokomobile_customer_limit'] = 'Unlimited';
}

