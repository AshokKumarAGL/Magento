jQuery(function () {
	jQuery("#country_id").selectbox();
	jQuery("#country_id1").selectbox();
	jQuery("#country_id2").selectbox();
});

jQuery(document).ready(function(){
		jQuery('#slider1').tinycarousel({ axis: 'x'});
		jQuery("#slider").easySlider({
			auto: true, 
			continuous: true
		});		
		//	Scrolled by user interaction
	jQuery('#foo2').carouFredSel({
		prev: '#prev2',
		next: '#next2',
		pagination: "#pager2",
		auto: false
	});
		
});
	
function changeclass(n) {
    var n = parseInt(n);
    var current = parseInt(jQuery('#directioncount').val());
    jQuery('#directioncount').val=n;
    var i; 
    var counter = 5;
        jQuery('.inner_banner').stop().animate({'margin-left': -(n-1)*1508+'px'},600);
}      
