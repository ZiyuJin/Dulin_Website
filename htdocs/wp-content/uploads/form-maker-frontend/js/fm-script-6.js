    var fm_currentDate = new Date();
    var FormCurrency_6 = '$';
    var FormPaypalTax_6 = '0';
    var check_submit6 = 0;
    var check_before_submit6 = {};
    var required_fields6 = ["4"];
    var labels_and_ids6 = {"3":"type_name","4":"type_checkbox","5":"type_submit_reset"};
    var check_regExp_all6 = [];
    var check_paypal_price_min_max6 = [];
    var file_upload_check6 = [];
    var spinner_check6 = [];
    var scrollbox_trigger_point6 = '20';
    var header_image_animation6 = 'none';
    var scrollbox_loading_delay6 = '0';
    var scrollbox_auto_hide6 = '1';
         function before_load6() {	
}	
 function before_submit6() {
	 }	
 function before_reset6() {	
}
    function onload_js6() {
    }
    function condition_js6() {
  if( jQuery("#form6 div[wdid=4] input[value=\"1\"]").is(":checked") && jQuery("#form6 div[wdid=4] input[value=\"1\"]").is(":checked") ) {
    jQuery("#form6 div[wdid=3]").removeAttr("style");
  }
  else {
    jQuery("#form6 div[wdid=3]").css("display", "none");
  }
  jQuery("div[wdid=4] input[type='checkbox']").click(function() {
    if( jQuery("#form6 div[wdid=4] input[value=\"1\"]").is(":checked") && jQuery("#form6 div[wdid=4] input[value=\"1\"]").is(":checked") ) {
      jQuery("#form6 div[wdid=3]").removeAttr("style");
    }
    else {
      jQuery("#form6 div[wdid=3]").css("display", "none");
    }
  });
    }
    function check_js6(id, form_id) {
    if (id != 0) {
    x = jQuery("#" + form_id + "form_view"+id);
    }
    else {
    x = jQuery("#form"+form_id);
    }    }
    function onsubmit_js6() {
    
  jQuery("<input type=\"hidden\" name=\"wdform_4_allow_other6\" value=\"no\" />").appendTo("#form6");
  jQuery("<input type=\"hidden\" name=\"wdform_4_allow_other_num6\" value=\"0\" />").appendTo("#form6");
  var disabled_fields = "";	
  jQuery("div[wdid]").each(function() {
    if(jQuery(this).css("display") == "none") {
      disabled_fields += jQuery(this).attr("wdid");
      disabled_fields += ",";
    }
    if(disabled_fields) {
      jQuery("<input type=\"hidden\" name=\"disabled_fields6\" value =\""+disabled_fields+"\" />").appendTo("#form6");
    }
  });    }
    jQuery(window).load(function () {
    formOnload(6);
    });
    form_view_count6 = 0;
    jQuery(document).ready(function () {
    fm_document_ready(6);
    });
    