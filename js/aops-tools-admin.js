jQuery(document).ready(function($) {

	if ($('#aops_tools_tab_container').length > 0) {

		var hash = window.location.hash;
		var tabRegexp = new RegExp('#tab_', 'g');
		var hashRegexp = new RegExp('#aops_tools_', 'g');
		var defaultOpenedTab;

		if (!hash) {

			defaultOpenedTab = jQuery("#aops_tools_last_opened_settings_tab").val();

			if (defaultOpenedTab) {

				var hashTab = defaultOpenedTab.replace(tabRegexp, '#aops_tools_tab_');

				// Check whether this tab actually exists
				if (jQuery('#aops_tools_tab_container ul.etabs li' + defaultOpenedTab).length > 0) {
					window.location.hash = hashTab;
				} else {
					defaultOpenedTab = false;
				}

			}

		} else {

			defaultOpenedTab = hash.replace(hashRegexp, '#');

			// Check whether this tab actually exists
			if (jQuery('#lp_tab_container ul.etabs li' + defaultOpenedTab).length > 0) {
				jQuery('#aops_tools_last_opened_settings_tab').val(defaultOpenedTab);
			} else {
				defaultOpenedTab = false;
			}

		}
		
		// Fallback to default tab
		if (!defaultOpenedTab) {
			defaultOpenedTab = '#tab_general';
			window.location.hash = '#aops_tools_tab_general';
			jQuery('#aops_tools_last_opened_settings_tab').val(defaultOpenedTab);
		}

		// Activate tabs plugin
		jQuery('#aops_tools_tab_container').easytabs({
			animate: false,
			defaultTab: defaultOpenedTab,
			updateHash: true
		});

		// Save name of the last opened tab to open it later
		jQuery('#aops_tools_tab_container').bind('easytabs:after', function() {
			jQuery('#aops_tools_tab_container ul.etabs li').each(function() {
				$this = jQuery(this);
				if ($this.hasClass('active')) {
					var hashRegexp = new RegExp('#aops_tools_', 'g');
					var activeTabId = $this.find('a').attr('href').toString().replace(hashRegexp, '#');
					jQuery('#aops_tools_last_opened_settings_tab').val(activeTabId);
				}
			});
		});

	}

	// Collapsible content in Meta box
	new jQueryCollapse(jQuery('#aops-tools-metabox-collapsible'), {
		open: function() {
			this.slideDown(150);
			this.prev().find('.dashicons').removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
		},
		close: function() {
			this.slideUp(150);
			this.prev().find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
		} 
	  });

});
