$(document)
	.on('click', '.js-grid-toggle-searchbox', function(event){
		var $button = $(this),
			gridId = $button.data('grid-id')
		;
		event.preventDefault();
		$('#' + gridId).trigger('grid-toggle-searchbox');
	})
	.on('click', '.js-grid-click-goto-url', function(event){
		var $button = $(this),
			gridId = $button.data('grid-id'),
			url = $button.data('url')
		;
		event.preventDefault();
		$('#' + gridId).trigger('grid-goto-url', [url]);
	})
	.on('keypress', '.js-grid-keypress-goto-url', function(event){
		var $input = $(this),
			gridId = $input.data('grid-id'),
			url = $input.data('url'),
			page = $input.val() - 1,
			key = event.which
		;
		if(window.event)
		{
			key = window.event.keyCode; //IE
		}
		if(key == 13)
		{
			$('#' + gridId).trigger('grid-goto-url', [url + page]);
			event.preventDefault();
		}
	})
	.on('change', '.js-grid-change-goto-url', function(event){
		var $input = $(this),
			gridId = $input.data('grid-id'),
			url = $input.data('url'),
			val = $input.val()
		;
		$('#' + gridId).trigger('grid-goto-url', [url + val]);
	})
	.on('click', '.js-grid-mark-visible', function(event){
		var $button = $(this),
			gridId = $button.data('grid-id'),
			isCheckbox = $button.is('input[type=checkbox]'),
			state = isCheckbox ? $button.prop('checked') : $button.data('state'),
			$labelContainer = $('#' + gridId + '_mass_action_selected'),
			labelText = $labelContainer.data('text')
		;
		if(!isCheckbox)
		{
			event.preventDefault();
		}
		$('.js-mass-action[data-grid-id=' + gridId + ']').prop('checked', state);
		var checkedCounter = $('.js-mass-action[data-grid-id=' + gridId + ']:checked:not(.js-mass-action-toggle)').length;
		$labelContainer.html(checkedCounter > 0 ? labelText.replace('_s_', checkedCounter) : '');
		$('#' + gridId + '_mass_action_all').val('0');
	})
	.on('click', '.js-grid-mark-all', function(event){
		var $button = $(this),
			gridId = $button.data('grid-id'),
			state = $button.data('state'),
			$labelContainer = $('#' + gridId + '_mass_action_selected'),
			labelText = $labelContainer.data('text'),
			labelTotal = $labelContainer.data('total')
		;
		event.preventDefault();
		$('.js-mass-action[data-grid-id=' + gridId + ']').prop('checked', state);
		$labelContainer.html(state ? labelText.replace('_s_', labelTotal) : '');
		$('#' + gridId + '_mass_action_all').val(+state);
	})
	.on('change', '.js-grid-switch-operator', function(event){
		var $input = $(this),
			gridId = $input.data('grid-id'),
			query = $input.data('query_'),
			dataSubmitOnChange = $input.data('submit-on-change'),
			operators = $input.data('operators'),
			val = $input.val(),
			$inputFrom = $('#' + query + 'from'),
			$inputTo = $('#' + query + 'to')
		;
		if($.inArray(val, operators.between) >= 0)
		{
			$inputFrom.show().prop('disabled', false);
			$inputTo.show().prop('disabled', false);
		}
		else if($.inArray(val, operators.is_null) >= 0)
		{
			$inputFrom.hide().prop('disabled', true);
			$inputTo.hide().prop('disabled', true);
			if(dataSubmitOnChange === undefined)
			{
				dataSubmitOnChange = true;
			}
			if(dataSubmitOnChange)
			{
				$('#' + gridId).trigger('grid-submit-form', [$input.closest('.js-grid-search-form')]);
			}
		}
		else
		{
			$inputFrom.show().prop('disabled', false);
			$inputTo.hide().prop('disabled', true);
		}
	})
	.on('keypress', '.js-grid-keypress-submit-form', function(event){
		var $input = $(this),
			gridId = $input.data('grid-id'),
			$form = $input.closest('.js-grid-search-form'),
			key = event.which
		;
		if(window.event)
		{
			key = window.event.keyCode; //IE
		}
		if(key == 13)
		{
			$('#' + gridId).trigger('grid-submit-form', [$form]);
		}
	})
	.on('change', '.js-grid-change-submit-form', function(event){
		var $input = $(this),
			gridId = $input.data('grid-id'),
			$form = $input.closest('.js-grid-search-form')
		;
		$('#' + gridId).trigger('grid-submit-form', [$form]);
	})
	.on('init-grids', function(event, $context){
		$('.js-grid', $context).each(function(){
			var $grid = $(this),
				gridId = $grid.attr('id')
			;
			$grid
				.on('grid-toggle-searchbox', function(){
					$('#js-grid-search-' + gridId).toggle('slow');
				})
				.on('grid-goto-url', function(event, url){
					window.location.href = url;
				})
				.on('grid-submit-form', function(event, $form){
					$form.submit();
				})
			;
		});
		$('.js-grid-ajax', $context).each(function(){
			var $grid = $(this),
				gridId = $grid.attr('id'),
				gridUrl = $grid.attr('action') || $grid.data('action')
			;
			$grid
				.on('grid-toggle-searchbox', function(){
					$('#js-grid-search-' + gridId).toggle('slow');
				})
				.on('grid-goto-url', function(event, url, data, type){
					var $type = type || 'GET',
						data = data || []
					;
					$.ajax({
						url: url,
						data: data,
						type: type,
						success: function(response) {
							var $response = $(response);
							$grid.parent().replaceWith($response);
							$(document).trigger('init-grids', [$response]);
						}
					});
				})
				.on('grid-submit-form', function(event, $form){
					event.preventDefault();
					if(event.type != 'keypress' || event.which == 13)
					{
						$grid.trigger('grid-goto-url', [gridUrl, $('input, select', $form).serialize(), 'POST']);
					}
				})
			;
			// Order and pagerfanta links
			$('.pagination li.disabled a', $grid).on('click', function (e) {
				return false;
			});

			// Order and pagerfanta links
			$('a.order, nav a, a.searchOnClick, .pagination li:not(.disabled) a', $grid).on('click', function (event) {
				var $this = $(this);
				event.preventDefault();
				$grid.trigger('grid-goto-url', [$this.attr('href')]);
			});

			// Mass actions submit
			$('.mass-actions .grid_massactions input[type=submit]', $grid).on('click', function (event) {
				event.preventDefault();
				$grid.trigger('grid-submit-form', [$grid]);
			});

			// Grid_search submit (load only one time)
			$('.js-grid-submit-search', '#' + gridId + '_search').on('click', function (event) {
				var $button = $(this),
					$container = $('#' + gridId + '_search')
				;
				event.preventDefault();
				event.stopPropagation();
				$grid.trigger('grid-submit-form', [$container]);
			});

			$('a.grid-row-action.ajax-link', $grid)
				.each(function(){
					var $this = $(this);
					$this
						.data('onClick', $this.attr('onClick'))
						.removeAttr('onClick')
					;
				})
				.on('click', function(e){
					var $this = $(this);
					e.preventDefault();
					if(func = $this.data('onClick'))
					{
						func = eval('(function(){' + func + ';})');
						res = func();
						if(typeof res == 'boolean' && !res)
						{
							return;
						}
					}
					$grid.trigger('grid-goto-url', [$this.attr('href')]);
				})
			;
		});
	})
	.on('ready', function(){
		$(this).trigger('init-grids');
	})
;
