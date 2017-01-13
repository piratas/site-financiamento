/**
 * alg-variations-frontend.
 *
 * version 2.3.0
 */
var original_img_atts_src    = null;
var original_img_atts_title  = null;
var original_img_atts_alt    = null;
var original_img_atts_srcset = null;
var original_img_atts_sizes  = null;
var original_a_atts_href  = null;
var original_a_atts_title = null;
function handle_variation_images(current_variation) {
		jQuery.each(jQuery.parseJSON(jQuery("form.variations_form").attr("data-product_variations")),function(){
		if (this.variation_id == current_variation) {
			if (this.image_src && this.image_src.length > 1) {
				jQuery("div.images img:eq(0)").attr("src",    this.image_src);
				jQuery("div.images img:eq(0)").attr("title",  this.image_title);
				jQuery("div.images img:eq(0)").attr("alt",    this.image_alt);
				jQuery("div.images img:eq(0)").attr("srcset", this.image_srcset);
				jQuery("div.images img:eq(0)").attr("sizes",  this.image_sizes);
				jQuery("div.images a.zoom:eq(0)").attr("href",  this.image_link);
				jQuery("div.images a.zoom:eq(0)").attr("title", this.image_caption);
			} else {
				jQuery("div.images img:eq(0)").attr("src",    original_img_atts_src);
				jQuery("div.images img:eq(0)").attr("title",  original_img_atts_title);
				jQuery("div.images img:eq(0)").attr("alt",    original_img_atts_alt);
				jQuery("div.images img:eq(0)").attr("srcset", original_img_atts_srcset);
				jQuery("div.images img:eq(0)").attr("sizes",  original_img_atts_sizes);
				jQuery("div.images a.zoom:eq(0)").attr("href",  original_a_atts_href);
				jQuery("div.images a.zoom:eq(0)").attr("title", original_a_atts_title);
			}
		}
	});
}
jQuery(document).ready(function() {
	if (original_img_atts_src == null) {
		original_img_atts_src    = jQuery("div.images img:eq(0)").attr("src");
		original_img_atts_title  = jQuery("div.images img:eq(0)").attr("title");
		original_img_atts_alt    = jQuery("div.images img:eq(0)").attr("alt");
		original_img_atts_srcset = jQuery("div.images img:eq(0)").attr("srcset");
		original_img_atts_sizes  = jQuery("div.images img:eq(0)").attr("sizes");
		original_a_atts_href  = jQuery("div.images a.zoom:eq(0)").attr("href");
		original_a_atts_title = jQuery("div.images a.zoom:eq(0)").attr("title");
	}
	if(jQuery("input:radio[name='alg_variations']").is(':checked')){
		var checked_radio = jQuery("input:radio[name='alg_variations']:checked");
		var variation_id = checked_radio.attr("variation_id");
		jQuery("input:hidden[name='variation_id']").val(variation_id);
		jQuery(checked_radio[0].attributes).each(
			function(i, attribute){
				if(attribute.name.match("^attribute_")){
					jQuery("input:hidden[name='" + attribute.name + "']").val(attribute.value);
				}
			}
		);
		handle_variation_images(variation_id);
	}
	jQuery("input:radio[name='alg_variations']").change(
		function(){
			var current_variation = jQuery(this).attr("variation_id");
			jQuery("input:hidden[name='variation_id']").val(current_variation);
			jQuery(this.attributes).each(
				function(i, attribute){
					 if(attribute.name.match("^attribute_")){
						jQuery("input:hidden[name='" + attribute.name + "']").val(attribute.value);
					 }
				}
			);
			handle_variation_images(current_variation);
		}
	);
});