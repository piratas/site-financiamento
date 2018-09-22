jQuery(function($){
    //globals here, in CAPS

    var PSMI_Setup = {
        init : function () {
			
        	var $this = this;
			//prevent default behaviour on links with #
			$("a[href='#']").on('click',function(e){e.preventDefault();});
			
			//select all
			$('.select_all').on('click', function(e){
				$(this).parents('.psmi-menucontainer').find('input[type="checkbox"]').each(function(){
					$(this).attr('checked', true);
				});
			});
			
			//deselect all
			$('.deselect_all').on('click', function(e){
				$(this).parents('.psmi-menucontainer').find('input[type="checkbox"]').each(function(){
					$(this).attr('checked', false);
				});
			});
			
			//invert selection
			$('.invert_selection').on('click', function(e){
				$(this).parents('.psmi-menucontainer').find('input[type="checkbox"]').each(function(){
					$(this).prop('checked', !$(this).prop('checked'));
				});
			});
        }
    };
    PSMI_Setup.init();
});