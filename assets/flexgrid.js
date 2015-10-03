

reattachGridHandlers();

$(document).on("pjax:complete", reattachGridHandlers );

function reattachGridHandlers()
{
	$('#submit-grid-config').unbind( "click" );	
	$('#submit-grid-config').bind( "click", configureGrid );	
}

function configureGrid(e)
{
	var data = '&id='+$('#columns-list').attr('name');
	$.each( $('.visible-column:checked'), function( index, value )
	{
		data += "&cols["+index+"]="+$(value).attr('name');
	});

	$.ajax(
	{
		url: 'index.php?r=storage/flexform/grid-template/configure',
		type: 'POST',
		data: data,
		success: function(response)
		{
			$('.modal').modal('hide');
			$.pjax.reload({container:'#flexible-grid-pjax'});
		}
	});
}

