"use strict";
jQuery(document).ready(function ($) {

  //Generate Title
  $(".aitgen_generate_title").on('click', function (event) {
    event.preventDefault();

    let id = $(this).data("id");
    let dataType = $(this).data("type");
    let dataTitle = $(this).data("title");

    // Perform the AJAX request
    $.ajax({
        type: 'POST',
        url: ajax_object.ajaxurl,
        data: {
          action: 'aitgen_title_action',
          nonce: ajax_object.nonce,
          dataType,
          id,
          dataTitle
        },
        success: function (response) {
          let title = response.data.title;
          let id = response.data.id;

          if (title) {

            $(".generated_title").attr('dataType', dataType);
            $(".generated_title").attr('pid', id);
            $(".generated_title").val(title);
            $("#aitgen_customAlert").modal("show");

          }         
        },
        error: function (error) {
            console.error('AJAX error:', error);
        },
    });
  });

  //Regenerate Title
  // Callback for 
  const  aitgen_suggestedTitleList_itemText = () => {

    $(".aitgen_suggestedTitleList_itemText").on('click', function (event) {
      event.preventDefault();
      // Get the text content of the clicked list item
      let listItemVal = $(this).text();
      listItemVal = listItemVal.substring(4, listItemVal.length - 1);
      
      $(".generated_title").val(listItemVal);
      $(".editor-post-title__input").text(listItemVal);
    });

  }
   
  aitgen_suggestedTitleList_itemText();


  $(".aitgen_regenerate_title").on('click', function (event) {
    event.preventDefault();
    // <ul> class
    let aitgen_suggestedLitle_list = $(".aitgen_suggested_title_list");

    let id = $(".generated_title").attr("pid");
    let generatedTitle = $(".generated_title").val();

    let ldsLoader = $(".lds-spinner");
    ldsLoader.css({ "display": "inline-block" });
    
    // Store reference to $(this)
    let $buttonUpdate = $(".aitgen_update_title");
    $buttonUpdate.prop("disabled", true);

    let $button = $(this);
    $button.text("Generating").prop("disabled", true);
    
      // Perform the AJAX request
      $.ajax({
          type: 'POST',
          url: ajaxObj.ajaxurl,
          data: {
            action: 'regenerate_title_action',
            nonce: ajaxObj.nonce,
            id,
            generatedTitle
          },
          success: function (response) {
            
            let titleData = response.data.title;

            if (titleData) {

              ldsLoader.css({ "display": "none" });
              $button.text("Regenerate").removeAttr("disabled");
              $buttonUpdate.removeAttr("disabled");
              // Slide up the suggestions list
              aitgen_suggestedLitle_list.slideUp(500, function () {
                // Clear previous suggestions
                aitgen_suggestedLitle_list.empty();
                
                $.each(titleData, function (index, value) {
                  let linkElement = $("<a>", {
                    "class": "aitgen_suggestedTitleList_itemText",
                    "href": '',
                    "text": value
                  });
                  let listItem = $("<li>", {
                    "class": "aitgen_suggestedTitleList_item"
                  }).append(linkElement);
                  aitgen_suggestedLitle_list.append(listItem);
                });
     
                // Show the list after appending items
                aitgen_suggestedTitleList_itemText();
                aitgen_suggestedLitle_list.slideDown(500);
              })

            }
          },
          error: function (error) {
              console.error('AJAX error:', error);
          },
      });
  });

  // Suggested Title Generator for post
  let aitgen_suggestedPostLitle_list = $(".aitgen_suggestedPost_title_list");
  $("#aitgen_generate_title_btn").on('click', function (event) {
    event.preventDefault();

    let id = $(".generated_title").data("id");
    let generatedTitleVal = $("#aitgen_title_field").val();
    
      // Perform the AJAX request
      $.ajax({
          type: 'POST',
          url: postAjaxObj.ajaxurl,
          data: {
            action: 'regenerate_postTitle_action',
            nonce: postAjaxObj.nonce,
            id,
            generatedTitleVal
          },
          success: function (response) {

          let titleData = response.data.title;

            if (titleData) {

              aitgen_suggestedPostLitle_list.slideUp(500, function () {
                // Clear previous suggestions
                $(this).empty();


                $.each(titleData, function (index, value) {
                  let linkElement = $("<a>", {
                    "class": "aitgen_suggestedTitleList_itemText",
                    "href": '',
                    "text": value
                  });
                  let listItem = $("<li>", {
                    "class": "aitgen_suggestedTitleList_item"
                  }).append(linkElement);
                  aitgen_suggestedPostLitle_list.append(listItem);
                });

                // Show the list after appending items
                aitgen_suggestedTitleList_itemText();
                aitgen_suggestedPostLitle_list.slideDown(500);


              })

            }

          },
          error: function (error) {
              console.error('AJAX error:', error);
          },
      });
  });


  // Update Generated Title
  $(".aitgen_update_title").on('click', function (event) {
    event.preventDefault();

    let product_id   = $(".generated_title").attr("pid");
    let dataType     = $(".generated_title").attr("dataType");
    let updatedTitle = $(".generated_title").val();

     // Perform the AJAX request
      $.ajax({
          type: 'POST',
          url: UpdateObj.ajaxurl,
          data: {
            action: 'update_regenerate_title_action',
            'nonce': UpdateObj.nonce,
            'dataType':dataType,
            'id': product_id,
            'updated_title': updatedTitle,
          },
          success: function (response) {
            let pageUrl = response?.data?.pageUrl;
            if (response.success) {
              window.location.href = pageUrl;
            }
          
          },
          error: function (error) {
              console.error('AJAX error:', error);
          },
      });
  });

  /*====== License Verify  ======
  =============================*/
  $("#openApiCheck").on('click', function (event) {
    let openApiKey = $("#openApiLicenseKey").val();
    let openApiCheckBtnId = $("#openApiCheck");  // Corrected variable name and added #
    let openApiKeyFieldId = $("#openApiLicenseKey");  // Corrected variable name and added #

    console.log($(this));

    $.ajax({
        type: 'POST',
        url: openApiObj.ajaxurl,
        data: {
            action: "aitgen_openApiCheck_action",
            'nonce': openApiObj.nonce,
            'apiKey': openApiKey,
        },
      success: function (response) {

            let status = response.data.status;
            if (response.success === true) {
              if (status === true) {
                  openApiCheckBtnId.removeClass('btn-primary').addClass('btn-success').text("Verified");
                  openApiKeyFieldId.addClass('is-valid');
              } else if (status === false) {
                  openApiCheckBtnId.removeClass('btn-primary').addClass('btn-warning').text("invalid");
                  openApiKeyFieldId.addClass('is-invalid');
              } else {
                openApiCheckBtnId.removeClass('btn-primary').addClass('btn-success').text("Verify");
                openApiKeyFieldId.removeClass('is-valid');
              }
          }
          
        },
        error: function (xhr, textStatus, error) {
            console.error("AJAX request failed with error:", error);
        }
    });
  });


  /*====== Short Description ======
  =============================*/
  //Generate Short Description
  $(".aitgen_shortDescription").on('click', function (event) {
    event.preventDefault();

    console.log("clicked");

    let id = $(this).data("id");
    // let dataTitle = $(this).data("title");

    // Perform the AJAX request
    $.ajax({
        type: 'POST',
        url: shortDsc.ajaxurl,
        data: {
          action: 'aitgen_shortDsc_action',
          nonce: shortDsc.nonce,
          id
        },
      success: function (response) {
          console.log(response);
          let description = response.data.desc;
          let id = response.data.id;

        if (description) {

            // Remove starting and ending double quotes
            description = description.replace(/^"|"$/g, '');

            $(".aitgen_short_description").attr('dataType', 'short-desc');
            $(".aitgen_short_description").attr('pid', id);
            $(".aitgen_short_description").val(description);
            $("#aitgen_shortDesc").modal("show");
          }         
        },
        error: function (error) {
            console.error('AJAX error:', error);
        },
    });
  });

  // Re-generate Short Description
  $(".aitgen_regenerate_shortDesc").on('click', function (event) {
    event.preventDefault();

    let id = $(".aitgen_short_description").attr("pid");
    let generated_shortDesc_container = $("#aitgen_short_description");
    let generatedShortDesc = $(".aitgen_short_description").val();

    let ldsLoader = $(".lds-spinner");
    ldsLoader.css({ "display": "inline-block" });
    
    // Store reference to $(this)
    let $buttonUpdate = $(".aitgen_update_shortDsc");
    $buttonUpdate.prop("disabled", true);

    let $button = $(this);
    $button.text("Generating..").prop("disabled", true);
    
      // Perform the AJAX request
      $.ajax({
          type: 'POST',
          url: reGenShortDsc.ajaxurl,
          data: {
            action: 'reGenShortDsc_action',
            nonce: reGenShortDsc.nonce,
            id,
            generatedShortDesc
          },
          success: function (response) {
          console.log(response);
            let shortDescription = response.data.shortDesc;
            // Remove starting and ending double quotes
            shortDescription = shortDescription.replace(/^"|"$/g, '');
      
            if (shortDescription) {
                ldsLoader.css({ "display": "none" });
                $button.text("Regenerate").removeAttr("disabled");
                $buttonUpdate.removeAttr("disabled");
                // Slide up the suggestions list
                generated_shortDesc_container.slideUp(500, function () {
                  // Clear previous suggestions
                  generated_shortDesc_container.empty();
                  generated_shortDesc_container.val(shortDescription);
                  generated_shortDesc_container.slideDown(500);
                })
            }
          },
          error: function (error) {
              console.error('AJAX error:', error);
          },
      });
  });


  $(".aitgen_update_shortDsc").on('click', function (event) {
    event.preventDefault();

    let product_id   = $(".aitgen_short_description").attr("pid");
    let dataType     = $(".aitgen_short_description").attr("dataType");
    let updated_desc = $(".aitgen_short_description").val();

     // Perform the AJAX request
      $.ajax({
          type: 'POST',
          url: updateShortDsc.ajaxurl,
          data: {
            action: 'updateShortDsc_action',
            'nonce': updateShortDsc.nonce,
            'dataType':dataType,
            'id': product_id,
            'updated_desc': updated_desc,
          },
        success: function (response) {
            
          console.log(response);
            let pageUrl = response?.data?.pageUrl;
            if (response.success) {
              window.location.href = pageUrl;
            }
          
          },
          error: function (error) {
              console.error('AJAX error:', error);
          },
      });
  });








}); // End of jQuery

















