jQuery(document).ready(function(){			
	if (typeof wpegSettings === 'undefined') {
		var wpegSettings = {gallery_theme: 'light_rounded', show_overlay: false};
	}
	jQuery(".gallery a[rel^='prettyPhoto']").prettyPhoto({counter_separator_label:' of ', theme:wpegSettings.gallery_theme, overlay_gallery:wpegSettings.show_overlay, social_tools:wpegSettings.show_social});
	
});