$(document)
	.on('new-form-add', function(event, $context){
		$(this).trigger('init-validation', [$context]);
	})
	.on('init-validation', function(event, $context){
		$('select.select2-offscreen, input[type=hidden].select2-offscreen', $context).on('change', function(){
			$(this).valid();
		});
	})
	.ready(function(){
		/*
		 * Override validator's method to allow validate readonly elements (datetimepickers - required)
		 */
		$.validator.prototype.elements = function() {
			var validator = this,
				rulesCache = {},
				radioCache = {}
			;

			// select all valid inputs inside the form (no submit or reset buttons)
			return $( this.currentForm )
			.find( "input, select, textarea" )
			.not( ":submit, :reset, :image, [disabled]" )
			.not( this.settings.ignore )
			.filter( function() {
				if ( !this.name && validator.settings.debug && window.console ) {
					console.error( "%o has no name assigned", this );
				}

				// select only the first element for each name, and only those with rules specified
				if ( this.name in rulesCache || !validator.objectLength( $( this ).rules() ) ) {
					return false;
				}

				rulesCache[ this.name ] = true;
				return true;
			});
		};

		jQuery.validator.addMethod('date', function(){return true;}); //We don't use date validation since we control this via datetimepicker
		jQuery.validator.setDefaults({
			focusCleanup: false,
			wrapper: '',
			ignore: 'input[type=hidden]:not(.select2-offscreen)',
			errorElement: 'span',
			errorClass: 'label label-danger',
			highlight: function(element) {},
			errorPlacement: function(error, element) {
				var $formGroup = element.closest('.form-group-validation'),
					$validationElement = element.triggerHandler('get-validation-element')
				;
				$formGroup
					.addClass('has-error')
					.addClass('has-feedback')
				;
				if($validationElement !== undefined)
				{
					element = $validationElement;
				}
				else if(!element.hasClass('form-control'))
				{
					element = element.closest('.form-control');
				}
				else if(element.parent().hasClass('input-group'))
				{
					element = element.parent();
				}
				error.insertAfter(element);
				element.after($('<span>', {'class': 'glyphicon glyphicon-warning-sign form-control-feedback'}));
			},
			unhighlight: function(element, errorClass, validClass){
				var group = $(element).closest('.form-group-validation')
					.removeClass('has-error')
					.removeClass('has-feedback')
				;
				$('span.glyphicon-warning-sign.form-control-feedback, span.label-danger', group).remove();
			},
			submitHandler: function(form) {
				var $form = $(form);
				if($form.data("submitted") === undefined)
				{
					$form.data("submitted", "1");
					form.submit();
				}
			},
			invalidHandler: function(event, validator) {
				var $firstErrorElement = $(validator.errorList[0].element),
					$tabsContents = $firstErrorElement.parents('.tab-pane'),
					$tabsButtonsTarget = $('.nav a[data-target]'),
					$tabsButtonsHref = $('.nav a[href^=#][href!=#]');
				$tabsContents.each(function(){
					var $tabContent = $(this),
						found = false;
					$tabsButtonsTarget.each(function(){
						var $tabButton = $(this);
						if($tabContent.is($tabButton.data('target')))
						{
							$tabButton.tab('show');
							found = true;
							return false;
						}
					});
					if(!found)
					{
						$tabsButtonsHref.each(function(){
							var $tabButton = $(this);
							if($tabContent.is($tabButton.attr('href')))
							{
								$tabButton.tab('show');
								return false;
							}
						});
					}
				});
			}
		});
		$(this).trigger('init-validation');
		$("form.form-horizontal:visible").validate();
	})
;
