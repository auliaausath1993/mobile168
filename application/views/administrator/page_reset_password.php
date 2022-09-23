<!DOCTYPE html>

<html lang="en"><head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <title>Forgot Password Admin</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="utf-8">



  <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/new-theme/css/bootstrap.css">
  <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/new-theme/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/new-theme/css/font.css">
  <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/new-theme/css/style.css">



    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->

    <!--[if lt IE 9]>

      <script src="../assets/js/html5shiv.js"></script>

    <![endif]-->



    <!-- Fav and touch icons -->

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">

    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">

    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">

    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">

    <link rel="shortcut icon" href="../assets/ico/favicon.png">

    <style>
        body {
          <?php
          $login_background_image = $this->main_model->get_detail('content',array('name' => 'login_background_image'));
          if (!$login_background_image['value']) {
            $img = base_url('media/images/bg_login.jpg');
          } else {
            $img = base_url('media/images/'.$login_background_image['value']);
          } ?>
          background: url('<?= $img ?>') no-repeat center top; /* Old browsers */
          min-height: 100%;
          min-width: 1024px;
          width: 100%;
          height: auto;
          position: fixed;
          top: 0;
          left: 0;
        }

        .navbar {
      <?php
      $header_color = $this->main_model->get_detail('content',array('name' => 'header_color'));
      $font_color = $this->main_model->get_detail('content',array('name' => 'font_color')); ?>

      background:  <?= $header_color['value'] ?> !important;
      color: <?= $font_color['value'] ?> !important;
      display: flex;
      justify-content: center;
    }

    .navbar-brand {
      color: <?= $font_color['value'] ?> !important;
    }
  </style>

  </head>

  <?php
    $data_version = $this->main_model->get_list('data_version_update',array('perpage' => 1,'offset' => 0),array('by' => 'id','sorting' => 'DESC'));
    $data_array_version_last = $data_version->row_array();
    $name_last_vertion = $data_array_version_last['name_version'];
  ?>


  <body>

    <header id="header" class="navbar bg bg-black text-center">
    <a class="navbar-brand" href="<?= base_url() ?>"><?=$this->config->item('tokomobile_online_shop'); ?></a>
  </header>

  <section id="content">
    <div class="main padder">
      <div class="row">
        <div class="col-lg-4 col-lg-offset-4 m-t-large" style="margin-top: 50px;">
          <?= $this->session->flashdata('message'); ?>
          <section class="panel">
            <header class="panel-heading text-center">
              Reset Password
            </header>
            <form action="<?= base_url('administrator/forgot/reset_password_process') ?>" method="post" class="panel-body">
              <input type="hidden"  name="code" value="<?=$code?>" >
              <div class="block">
                <label class="control-label">Masukan Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Password baru anda">
              </div>
              <div class="block">
                <label class="control-label">Ulangi Password</label>
                <input type="password" name="password_confirm" class="form-control" placeholder="Ulangi password anda">
              </div>
              <button type="submit" class="btn btn-info">Kirim</button>
            </form>
          </section>
        </div>
      </div>
    </div>
  </section>
</body>

</html>

