<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">

  <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/bootstrap/css/bootstrap.css" media="screen">
  <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/css/login.css" media="all">
  <link href="<?= base_url() ?>application/views/administrator/assets/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
     <![endif]-->
</head>

<body style="padding-top: 100px;">
    <div class="container">
      <?php $hide_failed = $this->session->flashdata('hide_failed');
      if($this->session->flashdata('success')) { ?>
      <div class="alert alert-success" role="alert" style="text-align: center;">
        <?= $this->session->flashdata('success') ?>
      </div>
      <?php } if($this->session->flashdata('failed')) { ?>
      <div class="alert alert-warning" role="alert" style="text-align: center;">
        <?= $this->session->flashdata('failed'); ?>
      </div>
      <?php } if($message != 'Success' && $hide_failed == 'false') { ?>
      <div class="alert alert-danger" role="alert" style="text-align: center;">
        <?= $message ?>
      </div>
      <?php } if ($message == 'Success' && !$this->session->flashdata('success')) { ?>
      <form method="post" class="form-signin" style="display: block;">
        <h4>Reset Password</h4>
        <div class="form-group">
          <label>Password Baru</label>
          <input type="password" class="input-block-level" name="password" placeholder="Password Baru Anda">
        </div>
        <div class="form-group">
          <label>Konfirmasi Password Baru</label>
          <input type="password" class="input-block-level" name="confirm_password" placeholder="Konfirmasi Password Baru Anda">
        </div>
        <button class="btn btn-large btn-inverse" type="submit" data-loading-text="Loading.. ">Reset</button>
      </form>
      <?php } ?>
      <center>
        <p class="powered" style="display: block;">Powered by <b><?=$this->config->item('tokomobile_online_shop'); ?> <br>
          Aplikasi Smartphone Online Shop
        </p>
      </center>
    </div>
    <div class="bottom-navbar">
      <strong><?=$this->config->item('tokomobile_online_shop'); ?></strong>
    </div>
    <script src="<?= base_url() ?>application/views/administrator/assets/js/jquery.js"></script>
    <script src="<?= base_url() ?>application/views/administrator/assets/bootstrap/js/bootstrap.js"></script>
</body>
</html>