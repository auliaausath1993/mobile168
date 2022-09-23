<?php include "includes/header.php"; ?>

<script type="text/javascript">

  var table_name = '<?= $table_name; ?>';

</script>

<!-- Content

  ================================================== -->

  <div id="page-wrapper">



    <div class="container-fluid">



      <!-- Page Heading -->

      <div class="row">

        <div class="col-lg-12">

          <h1 class="page-header">

            Lihat laporan<small> Tutup Buku</small>

          </h1>

          <ul id="submenu-container" class="nav nav-tabs" style="margin-bottom: 20px;">
           <!--  <li><a href="<?=base_url()?>administrator/main/report_tutup_buku_v2" ><b>Laporan Belum Tutup Buku</b></a></li> -->
           <li><a style="
           background: black;
           color: white;
           "href="<?=base_url()?>administrator/main/report_tutup_buku_lock" ><b>Laporan Tutup Buku</b></a></li>
         </ul>

         <ol class="breadcrumb">

          <li class="active">

            <i class="fa fa-list"></i> Laporan Tutup Buku

          </li>

        </ol>

      </div>

    </div>

    <!-- /.row -->

    <div class="row">

      <div class="col-lg-12">

       <?=form_open('administrator/main/report_tutupbuku_process') ?>

       Bulan &nbsp; <input type="text" name="month" class="datepicker" data-date-format="yyyy-mm" data-date-viewmode="years" data-date-minviewmode="months" readonly /> 

       <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> LIHAT LAPORAN</button>

       <?=form_close()?>

     </div>

   </div>

 </div>

</div>



<?php include "includes/footer.php"; ?>

