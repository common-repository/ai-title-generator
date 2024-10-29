"use strict";
jQuery(document).ready(function ($) {

  /*====== Short Description ======
  =============================*/
    //Generate Short Description
    $(".aitgen_shortDescription").on('click', function (event) {
      event.preventDefault();

      console.log("clicked");
  
      let id = $(this).data("id");
      let dataType = $(this).data("type");
      let dataTitle = $(this).data("title");
  
      // Perform the AJAX request
      // $.ajax({
      //     type: 'POST',
      //     url: ajax_object.ajaxurl,
      //     data: {
      //       action: 'aitgen_title_action',
      //       nonce: ajax_object.nonce,
      //       dataType,
      //       id,
      //       dataTitle
      //     },
      //     success: function (response) {
      //       let title = response.data.title;
      //       let id = response.data.id;
  
      //       if (title) {
  
      //         $(".generated_title").attr('dataType', dataType);
      //         $(".generated_title").attr('pid', id);
      //         $(".generated_title").val(title);
      //         $("#aitgen_customAlert").modal("show");
  
      //       }         
      //     },
      //     error: function (error) {
      //         console.error('AJAX error:', error);
      //     },
      // });
    });







}); // End of jQuery

















