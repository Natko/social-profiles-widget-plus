//////////////////////////////////////////////////////
// Social Widget JS
//////////////////////////////////////////////////////

jQuery(document).ready(function($) {

	///////////////////////////////////////////////////////////////////
	// Force the initial widget save (js bugfix)
	// - more info: http://wordpress.stackexchange.com/a/37707/27356
	///////////////////////////////////////////////////////////////////

	$(document).ajaxComplete(function(event, XMLHttpRequest, ajaxOptions){

		var request = {}, pairs = ajaxOptions.data.split('&'), i, split, widget;

		for(i in pairs){
			split = pairs[i].split('=');
			request[decodeURIComponent(split[0])] = decodeURIComponent(split[1]);
		}

		if(request.action && (request.action === 'save-widget')){

			widget = $('input.widget-id[value="' + request['widget-id'] + '"]').parents('.widget');
			widgetName = widget.prop('id');

			if(typeof widgetName != 'undefined'){
				if (widgetName.indexOf("spwp_social_widget") >= 0){
					if(!XMLHttpRequest.responseText){
						wpWidgets.save(widget, 0, 1, 0);
					} else {
						$(document).trigger('saved_widget', widget);
					}
				}
			}
		}

	});

	//////////////////////////////////////////////////////
	// Add new form (repeatable)
	//////////////////////////////////////////////////////

	$('body').on('click', 'a.spwp-social-new', function(e){
		e.preventDefault();
		var $dummyForm = $(this).siblings('.spwp-social-single.dummy').clone(true);
		$dummyForm.removeClass('dummy');
		$dummyForm.children('.spwp-profile-name').attr('name', $dummyForm.children('.spwp-profile-name').data('name'));
		$dummyForm.children('.spwp-profile-name').attr('id', $dummyForm.children('.spwp-profile-name').data('id'));
		$dummyForm.children('.spwp-profile-link').attr('name', $dummyForm.children('.spwp-profile-link').data('name'));
		$dummyForm.children('.spwp-profile-link').attr('id', $dummyForm.children('.spwp-profile-link').data('id'));
		$dummyForm.appendTo($(this).siblings('.spwp-social-sortable'));
	});

	//////////////////////////////////////////////////////
	// Change icons style (regular or flat)
	//////////////////////////////////////////////////////

	$('body').on('change', '.social-change-style', function(){
		if($(this).val() == 'flat'){
			$('.spwp-social-icon-preview').fadeOut( 200, function() {
				$('.spwp-social-sortable').addClass('flat');
				$('.spwp-social-icon-preview').fadeIn(400);
			});
		} else {
			$('.spwp-social-icon-preview').fadeOut( 200, function() {
				$('.spwp-social-sortable').removeClass('flat');
				$('.spwp-social-icon-preview').fadeIn(400);
			});
		}
	});

	//////////////////////////////////////////////////////
	// Bind the events on "ready" and then again
	// after the widget is saved
	//////////////////////////////////////////////////////

	$(document).on('first_init saved_widget', function(){

		//////////////////////////////////////////////////////
		// Change the preview icon when select is changed
		//////////////////////////////////////////////////////

		$('.spwp-profile-name').on('change', function(){
			var $this = $(this);

			if($this.prev('.spwp-social-icon-preview').hasClass($this.val())){
				return false;
			} else {
				$this.prev('.spwp-social-icon-preview').removeClass().addClass('spwp-social-icon-preview').addClass($this.val());
			}

			if($this.val() == 'email'){
				$this.next('.spwp-profile-link').attr('placeholder', socialwidget.emailaddress);
			} else if($this.val() == 'skype'){
				$this.next('.spwp-profile-link').attr('placeholder', socialwidget.skypemessage);
			} else {
				$this.next('.spwp-profile-link').attr('placeholder', socialwidget.profilelink);
			}
		});

		//////////////////////////////////////////////////////
		// Enable jquery.sortable on icons
		//////////////////////////////////////////////////////

		$('.spwp-social-sortable').sortable({
			placeholder: 'social-widget-placeholder',
			handle: '.spwp-social-icon-preview',
			start: function(e,ui){
				ui.placeholder.height(ui.item.height());
			}
		});

		//////////////////////////////////////////////////////
		// Delete icon, removes the form from DOM
		//////////////////////////////////////////////////////

		$('.spwp-social-remove').on('click', function(e){
			e.preventDefault();
			$(this).parent('.spwp-social-single').slideUp(200, function() {
				$(this).remove();
			});
		});

	});

	$(document).trigger('first_init');

});