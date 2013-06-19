jQuery(document).ready(function(){
	
	jQuery('#menu ul li').hover(function() { 
	  if (jQuery(this).children('ul').length > 0 )
	  {
	  	jQuery(this).children('li a').addClass('active'); 
	  	jQuery(this).find('.dropdown').css('display','block');
	  }
	  
	},function() {
		jQuery(this).children('li a').removeClass('active'); 
		jQuery(this).find('.dropdown').css('display','none');
  });
	jQuery('#search_container li').hover(function () {
     clearTimeout(jQuery.data(this, 'timer'));
     jQuery('ul', this).stop(true, true).slideDown();
	 jQuery(this).addClass('active');
	 jQuery('.sbOptions').css('display','none');
  }, function () {
    jQuery.data(this, 'timer', setTimeout(jQuery.proxy(function() {
      jQuery('ul', this).stop(true, true).slideUp();
	  jQuery(this).removeClass('active');
    }, this), 200));
  });
  
  jQuery('a#worldwide').click(function() {
		jQuery('.partner_box').toggle('slide', { direction: "right" }, 'slow');
	});	
	
	
	jQuery('.collection_area ul li .bottom_row a.plus').on('click', function() {
        var id = jQuery(this).attr('rel');
		jQuery('.img-'+ id ).slideDown();
		jQuery(this).next('.minus').show();
		jQuery(this).hide();
    });
	
	jQuery('.collection_area ul li .bottom_row a.minus').on('click', function() {
        var id = jQuery(this).attr('rel');
		jQuery('.img-'+ id ).slideUp();
		jQuery(this).prev('.plus').show();
		jQuery(this).hide();
    }); 
	
	jQuery('.bigImage_area .bottom_row a.plus').on('click', function() {
		jQuery(".blackStrip").slideDown();
		jQuery("a.plus").hide();
		jQuery("a.minus").show();
    }); 
	
	jQuery('.bigImage_area .bottom_row a.minus').on('click', function() {
		jQuery(".blackStrip").slideUp();
		jQuery("a.plus").show();
		jQuery("a.minus").hide();
    }); 
	
	jQuery('.imagehover').hover(function() {		
		var imagepath = jQuery(this).attr('rel');
		jQuery('.mainimage').attr("src", imagepath);
		jQuery(this).stop(true).animate({paddingLeft: '15px'});
	}
	,function() {
		jQuery(this).stop(true).animate({paddingLeft: '0'});
  });
	
  jQuery('.side_widget li').hover(function () {
     clearTimeout(jQuery.data(this, 'timer'));
     jQuery('ul', this).stop(true, true).slideDown();
	 jQuery(this).addClass('active');
  }, function () {
    jQuery.data(this, 'timer', setTimeout(jQuery.proxy(function() {
      jQuery('ul', this).stop(true, true).slideUp();
	  jQuery(this).removeClass('active');
    }, this)));
  });
  
  jQuery("#btnNews").click(function(){
	  jQuery(".ui-widget-bg").show();
	  jQuery("#newsLetter").show();	  
  });
  
  jQuery("#btnNews1").click(function(){
	  jQuery(".ui-widget-bg").show();
	  jQuery("#newsLetter").show();	  
  });
  
  jQuery("#btnClose").click(function(){
	  jQuery(".ui-widget-bg").hide();
	  jQuery("#newsLetter").hide();	  
  });
  
  jQuery("#measureOpen").click(function(){
	  jQuery(".ui-widget-bg").show();	
	  var height= jQuery("#outer_layout").height();
	  jQuery(".ui-widget-bg").height(height+"px");
	  jQuery("#measurePopup").show();	  
  });
  
  jQuery("#around_bust").click(function(){
	   jQuery("#around_bust_div").show();
	   var height= jQuery("#outer_layout").height();
	   jQuery(".ui-widget-bg").height(height+"px");
  });
  
  jQuery("#innerClose").click(function(){
	   jQuery("#around_bust_div").hide();
  });
  
  jQuery("#aroundabovewaist").click(function(){
	   jQuery("#aroundabovewaist_div").show();
  });
  
  jQuery("#innerClose_aroundabovewaist").click(function(){
	   jQuery("#aroundabovewaist_div").hide();
  });
  
  jQuery("#shoulder").click(function(){
	   jQuery("#shoulder_div").show();
  });
  
  jQuery("#innerClose_shoulder").click(function(){
	   jQuery("#shoulder_div").hide();
  });
  
  jQuery("#sleeve_style").click(function(){
	   jQuery("#sleeve_style_div").show();
  });
  
  jQuery("#btn_close_sleeve_style").click(function(){
	   jQuery("#sleeve_style_div").hide();
  });
  
  jQuery("#sleevelength").click(function(){
	   jQuery("#sleevelength_div").show();
  });
  jQuery("#innerClose_sleevelength").click(function(){
	   jQuery("#sleevelength_div").hide();
  });
  
  jQuery("#aroundarmhole").click(function(){
	   jQuery("#aroundarmhole_div").show();
  });
  jQuery("#innerClose_aroundarmhole").click(function(){
	   jQuery("#aroundarmhole_div").hide();
  });
  
  jQuery("#aroundarm").click(function(){
	   jQuery("#aroundarm_div").show();
  });
  jQuery("#innerClose_aroundarm").click(function(){
	   jQuery("#aroundarm_div").hide();
  });
  
  jQuery("#frontneckstyle").click(function(){
	   jQuery("#frontneckstyle_div").show();
  });
  
  jQuery(".btn_close").click(function(){
	   jQuery("#frontneckstyle_div").hide();
  });
  
   jQuery("#frontneckdepth").click(function(){
	   jQuery("#frontneckdepth_div").show();
  });
   
  jQuery(".btn_close").click(function(){
	  jQuery("#frontneckdepth_div").hide();
  });
  
  jQuery("#backneckstyle").click(function(){
	   jQuery("#backneckstyle_div").show();
  });
  
  jQuery(".btn_close").click(function(){
	   jQuery("#backneckstyle_div").hide();
  });
  
  jQuery("#backneckdepth").click(function(){
	   jQuery("#backneckdepth_div").show();
  });
  jQuery("#backneckdepth_aroundarm").click(function(){
	  jQuery("#backneckdepth_div").hide();
  });
  
  jQuery("#blouselength").click(function(){
	   jQuery("#blouselength_div").show();
  });
  jQuery("#blouselength_aroundarm").click(function(){
	  jQuery("#blouselength_div").hide();
  });
  
  jQuery("#aroundwaist").click(function(){
	   jQuery("#aroundwaist_div").show();
  });
  
  jQuery("#aroundwaist_aroundarm").click(function(){
	  jQuery("#aroundwaist_div").hide();
  });
  
  jQuery("#aroundhips").click(function(){
	  jQuery("#aroundhips_div").show();
  });
  
  jQuery("#aroundhips_aroundarm").click(function(){
	  jQuery("#aroundhips_div").hide();
  });
  
  jQuery("#petticoatlength").click(function(){
  jQuery("#petticoatlength_div").show();
  });
  
  jQuery("#petticoatlength_aroundarm").click(function(){
	  jQuery("#petticoatlength_div").hide();
  });
  
  jQuery("#yourheight").click(function(){
	  jQuery("#yourheight_div").show();
  });
  
  jQuery("#yourheight_aroundarm").click(function(){
	  jQuery("#yourheight_div").hide();
  });
  
  jQuery("#measureClose").click(function(){
	  jQuery(".ui-widget-bg").hide();
	  jQuery("#measurePopup").hide();
	  
  }); 
	jQuery('#foo2').carouFredSel({
		prev: '#prev2',
		next: '#next2',
		pagination: "#pager2",
		auto: false
	});
	/*
	jQuery(function() {
		jQuery("#scroller").simplyScroll({
			auto: false,
			manualMode: 'loop',
			speed: 10
		});
	});
	*/
});	

/* This code from product details page  */
jQuery(function () {
	jQuery("#country_id").selectbox();
	jQuery("#country_id1").selectbox();
	jQuery("#country_id2").selectbox();
	jQuery("#country_id3").selectbox();
});

jQuery(document).ready(function(){
	jQuery("#slider").easySlider({
		auto: true, 
		continuous: true
	});		
});

(function(jQuery) {
	jQuery(function() {
		jQuery("#scroller").simplyScroll({
			auto: false,
			manualMode: 'end',
			speed: 10
		});
		jQuery("#scroller1").simplyScroll({
			customClass:'vert',
			orientation:'vertical',
			auto: false,
			manualMode: 'loop',
			speed: 10			
		});
	});
})(jQuery);

/* end of product details page */

jQuery(document).ready(function(){
	jQuery('.pages ol li:last a').removeClass('next i-next');
	jQuery('.paging .pages li:last').css('background','none');
	//jQuery('.paging .pages li:last a').addClass('next');
});

jQuery(document).ready(function(){
	jQuery('#shopping-cart-totals-table tfoot tr td:first-child strong').html('<div class="total">Total</div>');
	jQuery('#shopping-cart-totals-table tfoot tr td:first-child').removeClass('a-right');
	jQuery('#shopping-cart-totals-table tfoot tr td:last-child strong span').addClass('total-right ');
});

function show_desc(){
	jQuery("#DescAll").show();
}
function hide_desc(){
	jQuery("#DescAll").hide();
}
