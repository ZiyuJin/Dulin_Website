jQuery( function ( $ ) { 
  "use strict";
  var amazonAuthYes = $("input[name='authorize-amazon-yes']");
  var amazonAuthNo = $("input[name='authorize-amazon-no']");
  var title = $( "input[name='book-title']" );
  var finishedYes = $("input[name='book-finished-yes']");
  var finishedNo = $("input[name='book-finished-no']");
  var signedYes = $("input[name='book-signed-yes']");
  var signedNo = $("input[name='book-signed-no']");
  var firstEditionYes = $("input[name='book-firstedition-yes']");
  var firstEditionNo = $("input[name='book-firstedition-no']");
  var useAmazonYes = $("input[name='use-amazon-yes']");
  var useAmazonNo = $("input[name='use-amazon-no']");
  var isbn = $( "input[name='book-isbn']" );
  var amazonAuthQuestion = $("#auth-amazon-question-label");
  var useAmazonYesLabel = $("label[for='use-amazon-yes']");
  var useAmazonNoLabel = $("label[for='use-amazon-no']");
  var useAmazonQuestion = $("#use-amazon-question-label");
  var titleLabel = $('#wpbooklist-addbook-label-booktitle');
  var isbnLabel = $("label[for='isbn']");
  var finishedYesLabel = $('#book-date-finished-label');
  var finishedYesInput = $('#wpbooklist-addbook-date-finished');
  var pubDate = $("input[name='book-pubdate']");
  var pageYes = $("#wpbooklist-addbook-page-yes");
  var pageNo = $("#wpbooklist-addbook-page-no");
  var postYes = $("#wpbooklist-addbook-post-yes");
  var postNo = $("#wpbooklist-addbook-post-no");
   
  // Initial check for Amazon Authorization
  if(amazonAuthYes.prop('checked') === true){
    amazonAuthYes.css({'opacity':'0.5', 'pointer-events':'none'});
    amazonAuthNo.css({'opacity':'0.5', 'pointer-events':'none'});
    $("label[for='authorize-amazon-no']").css({'opacity':'0.5', 'pointer-events':'none'});
    $("label[for='authorize-amazon-yes']").css({'opacity':'0.5', 'pointer-events':'none'});
    $('#wpbooklist-authorize-amazon-container p').css({'opacity':'0.5'});
  }

  // Reset Book Title color and font-weight
  title.click(function(){
    titleLabel.css({'color':'black', 'font-weight':'normal'});
    if(title.val() == 'Title Required!'){
      title.val('');
    }
  })

  // Toggle behavior for amazon authorization
  amazonAuthYes.click(function(e){
    amazonAuthQuestion.css({'color':'black', 'font-weight':'normal'});
    if($(this).prop('checked') === true){
      amazonAuthNo.prop('checked', false);
    }
  });
  amazonAuthNo.click(function(e){
    amazonAuthQuestion.css({'color':'black', 'font-weight':'normal'});
    if($(this).prop('checked') === true){
      isbnLabel.css({'color':'black', 'font-weight':'normal'});
      amazonAuthYes.prop('checked', false);
    }
  });

  // Toggle behavior for post
  $(document).on("click", '#wpbooklist-addbook-post-yes, #wpbooklist-editbook-post-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-post-no').prop('checked', false);
      $('#wpbooklist-editbook-post-no').prop('checked', false);
    }
  });
  $(document).on("click", '#wpbooklist-addbook-post-no, #wpbooklist-editbook-post-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-post-yes').prop('checked', false);
      $('#wpbooklist-editbook-post-yes').prop('checked', false);
    }
  });

  // Toggle behavior for page
  $(document).on("click", '#wpbooklist-addbook-page-yes, #wpbooklist-editbook-page-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-page-no').prop('checked', false);
      $('#wpbooklist-editbook-page-no').prop('checked', false);
    }
  });
  $(document).on("click", '#wpbooklist-addbook-page-no, #wpbooklist-editbook-page-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-page-yes').prop('checked', false);
      $('#wpbooklist-editbook-page-yes').prop('checked', false);
    }
  });

  // Toggle behavior for finished
$(document).on("click", '#wpbooklist-addbook-finished-yes, #wpbooklist-editbook-finished-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-finished-no').prop('checked', false);
      $('#wpbooklist-editbook-finished-no').prop('checked', false);
      $('#book-date-finished-label').animate({'opacity':1}, 500);
      $('#wpbooklist-addbook-date-finished').animate({'opacity':1}, 500);
      $('#wpbooklist-editbook-date-finished').animate({'opacity':1}, 500);
    }
  });
$(document).on("click", '#wpbooklist-addbook-finished-no, #wpbooklist-editbook-finished-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-finished-yes').prop('checked', false);
      $('#wpbooklist-editbook-finished-yes').prop('checked', false);
      $('#book-date-finished-label').animate({'opacity':0}, 500);
      $('#wpbooklist-addbook-date-finished').animate({'opacity':0}, 500);
      $('#wpbooklist-editbook-date-finished').animate({'opacity':0}, 500);
    }
  });


// Toggle behavior for lendable
$(document).on("click", '#wpbooklist-addbook-signed-yes, #wpbooklist-editbook-signed-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-signed-no').prop('checked', false);
      $('#wpbooklist-editbook-signed-no').prop('checked', false);
    }
  });
$(document).on("click", '#wpbooklist-addbook-signed-no, #wpbooklist-editbook-signed-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-signed-yes').prop('checked', false);
      $('#wpbooklist-editbook-signed-yes').prop('checked', false);
    }
  });


// Toggle behavior for lendable
$(document).on("click", '#wpbooklist-addbook-bookswapper-yes, #wpbooklist-editbook-bookswapper-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-bookswapper-no').prop('checked', false);
      $('#wpbooklist-editbook-bookswapper-no').prop('checked', false);
    }
  });
$(document).on("click", '#wpbooklist-addbook-bookswapper-no, #wpbooklist-editbook-bookswapper-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-bookswapper-yes').prop('checked', false);
      $('#wpbooklist-editbook-bookswapper-yes').prop('checked', false);
    }
  });


  // Toggle behavior for first edition
 $(document).on("click", '#wpbooklist-editbook-firstedition-yes, #wpbooklist-addbook-firstedition-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-firstedition-no').prop('checked', false);
      $('#wpbooklist-editbook-firstedition-no').prop('checked', false);
    }
  });
 $(document).on("click", '#wpbooklist-editbook-firstedition-no, #wpbooklist-addbook-firstedition-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-firstedition-yes').prop('checked', false);
      $('#wpbooklist-editbook-firstedition-yes').prop('checked', false);    
    }
  });

  // Toggle behavior for using Amazon
  $(document).on("click", 'input[name="use-amazon-yes"]', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-addbook-label-booktitle').css({'font-weight':'normal','color':'black'});
      $('#wpbooklist-editbook-label-booktitle').css({'font-weight':'normal','color':'black'});
      if($('#wpbooklist-editbook-label-booktitle').val() == 'Title Required!' ){
        $('#wpbooklist-editbook-label-booktitle').val('');
      }

      if($('#wpbooklist-addbook-label-booktitle').val() == 'Title Required!' ){
        $('#wpbooklist-addbook-label-booktitle').val('');
      }

      $('#use-amazon-question-label').css({'font-weight':'normal','color':'black'});
      $("input[name='use-amazon-no']").prop('checked', false);
    }
  });
  $(document).on("click", 'input[name="use-amazon-no"]', function(event){
    if($(this).prop('checked') === true){
      $("label[for='isbn']").css({'font-weight':'normal','color':'black'});
      if($("label[for='isbn']").val() == 'ISBN Required!' ){
        $("label[for='isbn']").val('');
      }
      $("#use-amazon-question-label").css({'font-weight':'normal','color':'black'});
      $("input[name='use-amazon-yes']").prop('checked', false);
    }
  });

  $(document).on("click", '#wpbooklist-editbook-isbn, #wpbooklist-addbook-isbn', function(event){
    if($('#wpbooklist-editbook-isbn').val() == 'ISBN Required!'){
      $('#wpbooklist-editbook-isbn').val('');
    }

    if($('#wpbooklist-addbook-isbn').val() == 'ISBN Required!'){
      $('#wpbooklist-addbook-isbn').val('');
    }

    $("label[for='isbn']").css({'color':'black', 'font-weight':'normal'});
  });

  // Toggle behavior for Amazon Authorization
  $(document).on("click", 'input[name="authorize-amazon-no"]', function(event){
    if($(this).prop('checked') === true){
      $('input[name="authorize-amazon-yes"]').prop('checked', false);
      $("#use-amazon-question-label").css({'font-weight':'normal','color':'black'});
      $("input[name='use-amazon-no']").css({'opacity':'0.5', 'pointer-events':'none'});
      $("input[name='use-amazon-yes']").css({'opacity':'0.5', 'pointer-events':'none'});
      $("label[for='use-amazon-yes']").css({'opacity':'0.5', 'pointer-events':'none'});
      $("label[for='use-amazon-no']").css({'opacity':'0.5', 'pointer-events':'none'});
      $("#use-amazon-question-label").css({'opacity':'0.5', 'pointer-events':'none'});
    }
  });

  // Toggle behavior for Amazon Authorization
  $(document).on("click", 'input[name="authorize-amazon-yes"]', function(event){
    if($(this).prop('checked') === true){
      $('input[name="authorize-amazon-no"]').prop('checked', false);
      $("input[name='use-amazon-no']").css({'opacity':'1', 'pointer-events':'all'});
      $("input[name='use-amazon-yes']").css({'opacity':'1', 'pointer-events':'all'});
      $("label[for='use-amazon-yes']").css({'opacity':'1', 'pointer-events':'all'});
      $("label[for='use-amazon-no']").css({'opacity':'1', 'pointer-events':'all'});
      $("#use-amazon-question-label").css({'opacity':'1', 'pointer-events':'all'});
    }
  });

  // Toggle behavior for WooCommerce Product checkboxes
  $(document).on("click", '#wpbooklist-woocommerce-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-woocommerce-no').prop('checked', false);
    }
  });
  $(document).on("click", '#wpbooklist-woocommerce-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-woocommerce-yes').prop('checked', false);
    }
  });

   // Toggle behavior for WooCommerce Virtual Product checkboxes
  $(document).on("click", '#wpbooklist-woocommerce-vert-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-woocommerce-vert-no').prop('checked', false);
    }
  });
  $(document).on("click", '#wpbooklist-woocommerce-vert-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-woocommerce-vert-yes').prop('checked', false);
    }
  });

     // Toggle behavior for WooCommerce Download Product checkboxes
  $(document).on("click", '#wpbooklist-woocommerce-download-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-woocommerce-download-no').prop('checked', false);
    }
  });
  $(document).on("click", '#wpbooklist-woocommerce-download-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-woocommerce-download-yes').prop('checked', false);
    }
  });

     // Toggle behavior for WooCommerce review checkboxes
  $(document).on("click", '#wpbooklist-woocommerce-review-yes', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-woocommerce-review-no').prop('checked', false);
    }
  });
  $(document).on("click", '#wpbooklist-woocommerce-review-no', function(event){
    if($(this).prop('checked') === true){
      $('#wpbooklist-woocommerce-review-yes').prop('checked', false);
    }
  });


  // Toggle effects for displaying WooCommerce fields
  $(document).on("click", '#wpbooklist-woocommerce-yes', function(event){
    if($(this).prop('checked') === true){
      $('.wpbooklist-woo-row').css({'display':'table-row'})
      $('.wpbooklist-woo-row').animate({'opacity':'1'})
      var price = $('#wpbooklist-addbook-price').val();
      $('#wpbooklist-addbook-woo-regular-price').val(price);
    } else {
      $('.wpbooklist-woo-row').animate({'opacity':'0'})
      $('.wpbooklist-woo-row').css({'display':'none'})
      $('#wpbooklist-addbook-woo-regular-price').val('');
    }
  });
  $(document).on("click", '#wpbooklist-woocommerce-no', function(event){
    if($(this).prop('checked') === true){
      $('.wpbooklist-woo-row').animate({'opacity':'0'})
      $('.wpbooklist-woo-row').css({'display':'none'})
      $('#wpbooklist-addbook-woo-regular-price').val('');
    }
  });

  // Toggle effects for displaying WooCommerce fields
  $(document).on("click", '#wpbooklist-woocommerce-download-yes, #wpbooklist-woocommerce-vert-yes', function(event){
    if($(this).prop('checked') === true){
      $('.wpbooklist-woo-row-upload').animate({'opacity':'1'})
      $('.wpbooklist-woo-row-upload').css({'display':'table-row'})
      $('#wpbooklist-addbook-woo-width').prop('disabled',true);
      $('#wpbooklist-addbook-woo-height').prop('disabled',true);
      $('#wpbooklist-addbook-woo-weight').prop('disabled',true);
      $('#wpbooklist-addbook-woo-length').prop('disabled',true);
      $('.book-woocommerce-label-dim').css({'opacity':'0.3'});
    } else {
      $('.wpbooklist-woo-row-upload').animate({'opacity':'0'})
      $('.wpbooklist-woo-row-upload').css({'display':'none'})
      $('#wpbooklist-addbook-woo-width').prop('disabled',false);
      $('#wpbooklist-addbook-woo-height').prop('disabled',false);
      $('#wpbooklist-addbook-woo-weight').prop('disabled',false);
      $('#wpbooklist-addbook-woo-length').prop('disabled',false);
      $('.book-woocommerce-label-dim').css({'opacity':'1'});
    }
  });

  // Toggle effects for displaying WooCommerce fields
  $(document).on("click", '#wpbooklist-woocommerce-download-no, #wpbooklist-woocommerce-vert-no', function(event){
    if($(this).prop('checked') === true){
      $('.wpbooklist-woo-row-upload').animate({'opacity':'0'})
      $('.wpbooklist-woo-row-upload').css({'display':'none'})
      $('#wpbooklist-addbook-woo-width').prop('disabled',false);
      $('#wpbooklist-addbook-woo-height').prop('disabled',false);
      $('#wpbooklist-addbook-woo-weight').prop('disabled',false);
      $('#wpbooklist-addbook-woo-length').prop('disabled',false);
      $('.book-woocommerce-label-dim').css({'opacity':'1'});
    } else {
    
    }
  });



  // Masks for various inputs, utililizing the jQuery Masked Input plugin
  $("input[name='book-pubdate']").mask("9999");
});


// Checks for missing data that is required to be answered to add book
function wpbooklist_add_book_validator(){
  "use strict";
  jQuery(document).ready(function($) {
    var isbn = $( "input[name='book-isbn']" );
    var isbnLabel = $("label[for='isbn']");
    var useAmazonQuestion = $("#use-amazon-question-label");
    var titleLabel = $('#wpbooklist-addbook-label-booktitle');
    var amazonAuthQuestion = $("#auth-amazon-question-label");
    var amazonAuthYes = $("input[name='authorize-amazon-yes']");
    var amazonAuthQuestion = $("#auth-amazon-question-label");
    var amazonAuthNo = $("input[name='authorize-amazon-no']");
    var useAmazonYes = $("input[name='use-amazon-yes']");
    var useAmazonNo = $("input[name='use-amazon-no']");
    var title = $( "input[name='book-title']" );
    var titleLabel = $('#wpbooklist-addbook-label-booktitle');
    var errorFlag = false;

    // Reset all form checks
    isbnLabel.css({'color':'black', 'font-weight':'normal'});
    useAmazonQuestion.css({'font-weight':'normal','color':'black'});
    titleLabel.css({'color':'black', 'font-weight':'normal'});
    amazonAuthQuestion.css({'color':'black', 'font-weight':'normal'});
    var scrollTop = 0;

    // Test ISBN for valid characters
    var isbnVal = isbn.val();
    isbnVal = isbnVal.replace('-','');
    isbnVal = isbnVal.replace(' ','');
    var isnum = /^\d+$/.test(isbnVal);
    if(isbnVal == ''){
      isbnLabel.css({'font-weight':'bold','color':'red'});
      scrollTop = isbnLabel.offset().top-50
    } else {
      isbn.val(isbnVal);
    }

    // Check Amazon Authorization
    if(amazonAuthYes.prop('checked') === false && amazonAuthNo.prop('checked') === false){
      amazonAuthQuestion.css({'font-weight':'bold','color':'red'});
      if(scrollTop > amazonAuthQuestion.offset().top-50){
        scrollTop = amazonAuthQuestion.offset().top-50
      }
      if(scrollTop == 0){
        scrollTop = amazonAuthQuestion.offset().top-50
      }
      errorFlag = true;
    }

    // Check Amazon Usage
    if(useAmazonYes.prop('checked') === false && useAmazonNo.prop('checked') === false && (amazonAuthYes.prop('checked') === true || amazonAuthNo.prop('checked') === true)){
      useAmazonQuestion.css({'font-weight':'bold','color':'red'});
      if(scrollTop > useAmazonQuestion.offset().top-50){
        scrollTop = useAmazonQuestion.offset().top-50
      } 
      if(scrollTop == 0){
        scrollTop = useAmazonQuestion.offset().top-50
      }
      errorFlag = true;
    }
    if(useAmazonYes.prop('checked') === true && (isbn.val() == '' || isbn.val() == undefined || isbn.val() == null)){
      isbn.val('ISBN Required!');
      isbnLabel.css({'color':'red', 'font-weight':'bold'});
      if(scrollTop > isbnLabel.offset().top-50){
        scrollTop = isbnLabel.offset().top-50
      }
      if(scrollTop == 0){
        scrollTop = isbnLabel.offset().top-50
      }
      errorFlag = true;
    }

    // Check Book Title
    if(useAmazonNo.prop('checked') === true && (title.val() == '' || title.val() == undefined)){
      titleLabel.css({'color':'red', 'font-weight':'bold'});
      title.val('Title Required!');

      if($("#wpbooklist-addbook-title").length == 0){
        scrollTop = $("#wpbooklist-editbook-title").offset().top-50;
      }

      if($("#wpbooklist-editbook-title").length == 0){
        scrollTop = $("#wpbooklist-addbook-title").offset().top-50;
      }

      console.log(scrollTop)
      errorFlag = true;
    }

    // Scroll the the highest flagged element 
    if(scrollTop != 0){
      $('html, body').animate({
        scrollTop: scrollTop
      }, 500);
      scrollTop = 0;
    }

    // DOM element that reports back on the form error state
    $('#wpbooklist-add-book-error-check').attr('data-add-book-form-error', errorFlag);

  });

}