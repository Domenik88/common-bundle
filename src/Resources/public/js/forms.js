$(document)
	.on('new-form-add', function(event, $context){
		$(this).trigger('init-forms', [$context]);
	})
	.on('init-forms', function(event, $context){
		//Collection:
		$('.js-collection_row', $context).each(function(){
			var $collectionRow = $(this),
				id = $collectionRow.prop('id')
				allowAdd = $collectionRow.data('allow-add'),
				allowDelete = $collectionRow.data('allow-delete'),
				addButtonClass = $collectionRow.data('add-button-class'),
				removeButtonClass = $collectionRow.data('remove-button-class'),
				containerId = $collectionRow.data('container-id'),
				prototypeName = $collectionRow.data('prototype-name'),
				nextIndex = $collectionRow.data('next-child-index'),
				selector = $collectionRow.data('type-selector'),
				subPath = $collectionRow.data('subpath') || '',
				addType = $collectionRow.data('add-type')
			;
			if(allowAdd)
			{
				window.collectionIndexes[id] = nextIndex;
				$('.' + addButtonClass, $context).on('click', function(event){
					var $this = $(this),
						key = $this.data('prototype'),
						prototype = ''
					;
					event.preventDefault();
					$('.nav-tabs a[href="#' + $collectionRow.closest('div.tab-pane').attr('id') + '"]').tab('show');
					if(selector)
					{
						$selector = $(selector);
						form_type = $selector.val() | $selector.select2('val');
						prototype = $collectionRow.data('prototype-' + form_type);
					}
					else if(key)
					{
						prototype = $collectionRow.data('prototype-' + key);
					}
					else
					{
						prototype = $collectionRow.data('prototype');
					}
					var $newForm = $(prototype.replace(new RegExp(prototypeName, 'g'), window.collectionIndexes[id]++));
					$('#' + containerId + '>.js-collection_item-no_items').hide();
					if(addType === 'add_last')
					{
						$('#' + containerId + subPath).append($newForm);
					}
					else
					{
						$('#' + containerId + subPath).prepend($newForm);
					}
					$(document).trigger('new-form-add', [$newForm]);
				});
			}
			if(allowDelete)
			{
				$('#' + containerId, $context).on('click', 'button.' + removeButtonClass, function(event){
					event.preventDefault();
					$(this).closest('.js-collection_item').fadeOut('fast', function(){
						$(this).remove();
						if($('#' + containerId + subPath +'>.js-collection_item').length == 0)
						{
							$('#' + containerId + '>.js-collection_item-no_items').show();
						}
					});
				});
			}
		});

		//Color picker:
		if(typeof $.fn.colorpicker === 'function')
		{
			var $colorPickers = $('.js-colorpicker', $context);
			$colorPickers.colorpicker();
			$colorPickers.filter(':has(input[readonly]), :has(input:disabled)').colorpicker('disable');
		}

		//Datetime picker:
		if(typeof $.fn.datetimepicker === 'function')
		{
			var $dateTimePickers = $('.js-datetimepicker', $context);
			$dateTimePickers.datetimepicker();
			$dateTimePickers.filter(':has(input[data-readonly]), :has(input:disabled)').each(function(){
				$(this).data('DateTimePicker').disable();
			});
		}

		//Select2:
		if(window.Select2)
		{
			$('.js-select2', $context).each(function(){
				var $this = $(this),
					options = $this.data('select2-options')
				;
				if(typeof options !== 'object')
				{
					options = eval('(' + options + ')');
				}
				$this.select2(options);
				if($this.attr('readonly'))
				{
					$this.select2("readonly", true);
				}
			});
		}
	})
	.ready(function(){
		window.collectionIndexes = {};
		$(this).trigger('init-forms');
	})
;
