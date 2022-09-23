<?php 
if(($this->session->userdata('webadmin_user_level') == 'Superuser') or ($this->session->userdata('webadmin_user_level') == 'Administrator'))
{
	include 'sidebar-menu-owner.php';
}
elseif($this->session->userdata('webadmin_user_level')== 'Staf_admin')
{
	include 'sidebar-menu-admin.php';
}
elseif($this->session->userdata('webadmin_user_level') == 'Staf_kasir')
{
	include 'sidebar-menu-kasir.php';
}

?>