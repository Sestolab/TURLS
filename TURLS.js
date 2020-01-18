$(document).ready(function(){

	$('#turls .actions').on('click', 'button', function(){
		switch($(this).children().attr('class').match(/fa-\w+/)[0]){
			case 'fa-pencil':
				$(this).closest('tr').find('input').attr({'readonly': false, 'type': 'text'});
				$(this).closest('tr').find('a').toggleClass('hide');
				break;
			case 'fa-check':
				return $('form#urls').submit();
			case 'fa-trash':
				if(confirm($(this).attr('confirm'))){
					$(this).closest('tr').remove();
					$('form#urls').submit();
				}
				return;
			case 'fa-ban':
				$('form#urls').trigger('reset');
				$(this).closest('tr').find('input').attr({'readonly': true, 'type': 'hidden'});
				$(this).closest('tr').find('a').toggleClass('hide');
				break;
		}

		$(this).closest('tr').find('button').each(function(i, elem){
			$(elem).children().toggleClass(['fa-pencil fa-check', 'fa-trash fa-ban'][i]);
			$(elem).attr({'title': $(elem).attr('at'), 'at': $(elem).attr('title')});
		});

	});

});