

jQuery(document).ready(function ($) {

    // $(".sroll-bar, .side-nav-wrapper").mCustomScrollbar({
    //   theme:"minimal-dark",
    //   autoDraggerLength:true,
    // });

    $('#admin_info').modal('show');

    $( "#exsport" ).click(function() {
      $('#table-exsport').tableExport({type:'excel', escape:'false'});
    });


    var url = window.location.pathname,
    urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); // create regexp to match current url pathname and remove trailing slash if present as it could collide with the link in navigation in case trailing slash wasn't present there
    // now grab every link from the navigation
    $('.side-nav a').each(function(){
        // and test its normalized href against the url pathname regexp
        if(urlRegExp.test(this.href.replace(/\/$/,''))){
            $(this).addClass('active');
        }
    });


	 var site = "<?php echo site_url();?>";

			$('#customer_name').autocomplete({
				serviceUrl: base_url+'administrator/main/search_id_customer',
				onSelect: function (suggestion) {
					$('#customer_id').val(suggestion.data);
				}
			});



			$('#product_name').autocomplete({
				serviceUrl: base_url+'administrator/main/search_id_produk_item',
				onSelect: function (suggestion) {
					$('#product_id').val(suggestion.data);
				}
			});

			$('.datepicker').datepicker();

});



