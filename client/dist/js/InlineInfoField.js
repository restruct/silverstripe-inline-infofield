(function($) {
	$.entwine("ss", function($) {
		$("span.inline-info").entwine({
			onadd: function() {
				var self = this;
				let preName = '#Form_EditForm_';
				// move to correct element
				if(!$(this).hasClass('processed')){
					// at around 3.3 IDs changed
					var $label_el = $('#'+$(this).attr('data-target'));
					var $label_el = $('#Form_EditForm_'+ $(this).attr('data-target') +'_Holder');

					// Any form, not just the page editor (#3): a holder id is "<form id>_<field name>_Holder"
					// (FormTemplateHelper), and a GridField detail form is Form_ItemEditForm. Build the exact
					// id from the span's own form rather than a suffix match: "_Title_Holder" would also match
					// a "Sub_Title" field. getElementById, so a field name needs no selector escaping.
					var $form = $(this).closest('form');
					if($form.length && $form.attr('id')){
						var $in_form = $(document.getElementById($form.attr('id') + '_' + $(this).attr('data-target') + '_Holder'));
						if($in_form.length){
							$label_el = $in_form;
						}
					}

					if(!$label_el.length){
						$label_el = $('[id$="Form_EditForm_'+$(this).attr('data-target')+'_Holder"]');

					}
					
					$(this).addClass('processed')
						.appendTo(
							$label_el
								.find('label:first')
								.addClass('has-inline-infofield')
						);
				}
			},
			onclick: function() {
				// only respond to clicks on touch devices (else we respond to mouseenter/leave
				if(this.touchDevice()){
					$(this).toggleClass('show');
				}
				return false;
			},
			onmouseenter: function(){
				if( ! this.touchDevice()){ $(this).addClass('show'); }
			},
			onmouseleave: function(){
				if( ! this.touchDevice()){ $(this).removeClass('show'); }
			},
			touchDevice: function(){
				return ("ontouchstart" in document.documentElement);
			}
		});
	});
})(jQuery);

////(function($) {
//	$('.helpFieldSeed').livequery(function() {
//
//
//		$('#'+targetID+'_HelpFieldButton').click(function() {
//			var box = $('#'+targetID+'_HelpField');
//			if (box.hasClass('open')) {
//				box.removeClass('open');
//				$(this).removeClass('open').blur();
//			} else {
//				box.addClass('open');
//				$(this).addClass('open').blur();
//			}
//			return false;
//		});
//
//	});
//})(jQuery);
