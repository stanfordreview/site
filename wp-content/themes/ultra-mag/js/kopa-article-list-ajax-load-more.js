/**
 * This file holds ajax call for loading more articles of article list widget
 * http://kopatheme.com
 * Copyright (c) 2014 Kopatheme
 *
 * Licensed under the GPL license:
 *  http://www.gnu.org/licenses/gpl.html
 */
(function ($) {
	if ( $('.kp-article-list-widget .load-more').length > 0 ) {
		$('.kp-article-list-widget .load-more').on('click', function (e) {
			e.preventDefault();
			var $this = $(this),
				dataPostsList = $this.data('list-id'),
				dataLightBoxID = $this.data('light-box-id'),
	            dataAction = $this.data('action'),
	            dataNonce = $this.data('nonce'),
	            dataOffset = $this.data('offset'),
	            dataCategories = $this.data('categories'),
	            dataTags = $this.data('tags'),
	            dataRelation = $this.data('relation'),
	            dataPostsPerPage = $this.data('more-posts'),
	            dataOrderby = $this.data('orderby'),
	            dataPostsNotIn = $this.data('posts-not-in'),
	            dataDisplayDateMeta = $this.data('display-date-meta'),
	            dataDisplayReadmore = $this.data('display-readmore'),
	            dataDisplayAuthorMeta = $this.data('display-author-meta');

	        $.ajax({
	            type: 'POST',
	            url: kopa_front_variable.ajax.url,
	            data: {
	                action: dataAction,
	                kopa_ajax_load_more_nonce: dataNonce,
	                offset: dataOffset,
	                categories: dataCategories,
	                tags: dataTags,
	                relation: dataRelation,
	                posts_per_page: dataPostsPerPage,
	                orderby: dataOrderby,
	                post__not_in: dataPostsNotIn,
	                light_box_id: dataLightBoxID,
	                display_date_meta: dataDisplayDateMeta,
	                display_readmore: dataDisplayReadmore,
	                display_author_meta: dataDisplayAuthorMeta
	            },
	            success: function (responses) {
	      			responses = $.parseJSON(responses);

	                if ( responses ) {
	                    $(dataPostsList).append(responses.output).find("a[rel^='prettyPhoto']").prettyPhoto({
					        show_title: false,
					        deeplinking:false,
					        social_tools:false,
    					});
	                    $this.data('offset', dataOffset + dataPostsPerPage);
	                    $this.data('posts-not-in', responses.post__not_in);
	                } else {
	                    $this.remove();
	                }
	            }
	        });
		});
	}
}(jQuery));