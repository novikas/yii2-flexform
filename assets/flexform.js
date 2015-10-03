$(document).ready(function(e)
{
	$('#template-panel-pjax').insertBefore( $('#flexform-widget') );
	reattachHandlers();
	
	$(document).on('pjax:complete', function()
	{
		reattachHandlers();
		$('#template-panel-pjax').insertBefore( $('#flexform-widget') );
		$('#flexform-widget-whole').removeClass('kv-grid-loading');
	});
	
	$(document).on('pjax:beforeSend', function()
	{
		$('#flexform-widget-whole').addClass('kv-grid-loading');
	})

	// obvious
	function reattachHandlers()
	{
		$('.cloak-switcher').unbind( 'change' );
		$('.put-on-cloak').unbind( 'click' );
		$('.current-template > input[type=radio]').unbind('click');
		$('#new-template').unbind( 'click' );
		$('#remove-template').unbind('click' );
		$('#save-template').unbind('click');
		//$('#flexform-submit').unbind('click' );

		$('#remove-template').bind('click', removeCurrentTemplate );
		$('#save-template').bind('click', saveCurrentTemplate );
		$('.cloak-switcher').bind( 'change', dropdownHandler );
		$('.put-on-cloak').bind( 'click', dropdownHandler );
		$('#new-template').bind( 'click', templateNameHandler);
		$('.current-template > input[type=radio]').bind( 'change', setCurrentTemplate );
		//$('#flexform-submit').bind('click', flexformSubmit );
	}

	// Sets a template with id templateId as current
	function setCurrentTemplate(e)
	{
		var templateId = $(this).val();
		var route = $('#field-dropdown').attr('value');
		$.ajax({
			url: 'index.php?r='+route+'/set-current-template',
			type: 'POST',
			dataType: 'json',
			data:'&id='+templateId,
			success: function( response )
			{
				$.pjax.reload({container:'#flexform-widget-whole'});
			}
		});
	}

	// Removes current template
	function removeCurrentTemplate()
	{
		var userResponse = confirm('Вы уверены, что хотите удалить текущий шаблон?');
		if( userResponse )
		{
			var route = $('#field-dropdown').attr('value');
			var templateId = $('label.active.template-panel > input').val();
			console.log(templateId);
			$.ajax({
				url: 'index.php?r='+route+'/remove-template',
				type: 'POST',
				data: '&id='+ templateId,
				success:function(response){
					$.pjax({container:'#flexform-widget-whole'});
				},
			});
		}
		else
		{
			return;
		}
	}

	// submits the form data to action url
	function flexformSubmit(e)
	{
		var form = $('#flexform-widget>form');
		var data = form.serialize();
		$.ajax({
			url: form.action,
			type: 'POST',
			data: data,
			success: function(response)
			{

			}
		});
	}

	// Saves values of current template
	function saveCurrentTemplate(e)
	{
		var data = $('#flexform-widget>form').serialize();
		var route = $('#field-dropdown').attr('value');
		$.ajax({
			url:'index.php?r='+route+'/save-template',
			type: 'POST',
			data: data + '&templateId='+$('label.active.template-panel > input').val(),
			success: function(response)
			{
				//console.log(response);
			}
		});
	}

	// Handler for dropdown that manipulates fields visibility 
	function dropdownHandler(e)
	{
		var fieldName = $(this).attr('value');
		var route = $('#field-dropdown').attr('value');
		var checked = $(this).is(':checked');
		var templateId = $('label.active.template-panel > input').val();
		$.ajax({
			url: 'index.php?r='+route + ( checked?'/add-field':'/remove-field' ),
			type: 'POST',
			data: '&fieldName='+fieldName+'&templateId='+templateId,
			success: function ( response )
			{
				$.pjax.reload({container:'#flexform-widget'});
				if(!checked)
					$('#'+fieldName+'-check').removeAttr('checked');
			}
		});
	}
	
	// Starts new templates naming
	function templateNameHandler(e)
	{
		$(this).html("<input type='text' id = 'template-name' class = 'form-control' placeholder='Название фильтра'>");
		
		$(this).unbind( 'click', templateNameHandler );
		
		$('#template-name').focus();
		$('#template-name').on('focusout', recallNewTemplate);
		$('#template-name').unbind('keyup');
		$('#template-name').bind('keyup', newTemplateHandler);
	}

	// Cancel template creation(naming)
	function recallNewTemplate(e)
	{
		$(this).parent().removeClass('focus active');
		$(this).parent().bind('click', templateNameHandler);
		$(this).replaceWith('<span class="glyphicon glyphicon-plus"></span>');

	}

	// Create new template
	function newTemplateHandler(e)
	{
		var route = $('#field-dropdown').attr('value');
		
		if( e.keyCode == 13 )
		{
			$.ajax({
				url: 'index.php?r='+route+'/create-template',
				type: 'POST',
				dataType: 'json',
				data: '&name='+$(this).val()+'&modelClass=' + $(this).parent().attr('value'),
				success: function( response )
				{
					$.pjax.reload({container:'#flexform-widget-whole'});
				}
			});
		}
	}

});