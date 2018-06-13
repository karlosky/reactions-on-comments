(function($){

    /*
    * Hide and show buttons
    */
	$(document).on('mouseenter', 'div.roc-reactions-button', function(e){
		$(this).addClass('show');
	});

	$(document).on('mouseleave', 'div.roc-reactions-button', function(e){
		$(this).removeClass('show');
	});


	$(document).on('click', '.roc-reaction', function(e){
		e.preventDefault();
		var $class = $(this).attr('class');
        var main = $(this).parent().parent().parent(); 
        var vote_type = main.attr('data-type');
        var voted = main.attr('data-vote');
        var text = $(this).find('strong').text();
		
		res = $class.split(' ');
		type = res[1].split('-');

		$('div.roc-reactions-button').removeClass('show');
		$.ajax({
			url: roc_reaction.ajax,
			dataType: 'json',
			type: 'POST',
			data: {
				action: 'roc_reaction',
				nonce: main.data('nonce'),
				type: type[2],
				post: main.data('post'),
				voted: voted,
                comment: main.data('comment')
			},
			success: function(data) {
				if ( data.success ) {
					$('.roc-reactions-post-' + main.data('post') + '-comment-' + main.data('comment')).find('.roc-reactions-count').html(data.data.html);
					$('.roc-reactions-post-' + main.data('post') + '-comment-' + main.data('comment')).find('.roc-reactions-main-button').attr('class','roc-reactions-main-button').addClass('roc_reaction_' + type[2]).text(text);
					main.attr('data-vote','yes').attr('data-type', 'unvote');
				}
			}
		});
	});

	$(document).on('click','.roc-reactions-main-button', function(e) {
		e.preventDefault();

		var parent = $(this).parent().parent();
		var type = parent.attr('data-type');
		var text = $(this).parent().find('.roc-reaction-like strong').text();

		$.ajax({
			url: roc_reaction.ajax,
			dataType: 'json',
			type: 'POST',
			data: {
				action: 'roc_reaction',
				nonce: parent.data('nonce'),
				type: 'like',
				post: parent.data('post'),
				vote_type: type,
				voted: parent.attr('data-voted'),
                comment: parent.data('comment')
			},
			success: function(data) {
				if ( data.success ) {
					if ( data.data.type == 'unvoted' ) {
						$('.roc-reactions-post-' + parent.data('post') + '-comment-' + parent.data('comment')).find('.roc-reactions-main-button').attr('class', 'roc-reactions-main-button').text(text);
						parent.attr('data-type', 'vote');
					} else {
						$('.roc-reactions-post-' + parent.data('post') + '-comment-' + parent.data('comment')).find('.roc-reactions-main-button').addClass('roc_reaction_like');
						parent.attr('data-type', 'unvote');
					}
					$('.roc-reactions-post-' + parent.data('post') + '-comment-' + parent.data('comment')).find('.roc-reactions-count').html(data.data.html);
				}
			}
		});
	})
})(jQuery);
