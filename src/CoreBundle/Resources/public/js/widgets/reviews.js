(function ($) {
    $.widget("compo.reviews", {

        _create: function () {
            var self = this;


            /*
            $(".rating-review").rating({
                'step': 1,
                'min': 0,
                'max': 5,
                'size': 'xs',
                'showCaption': false
            });
            */

            $('.review-form').submit(function(event){
                event.preventDefault();

                var data = $( this ).serialize();

                $(this).find('fieldset').attr('disabled', 'disabled');

                $(this).find('.send-review').button('loading');

                // /ajax/review/

                $.ajax({
                    type: "POST",
                    url: '/ajax/review/',
                    data: data,
                    success: function(response){


                        $('#review-modal').on('hidden.bs.modal', function (e) {

                            /*
                             $('.reviews-list').append(response.html);

                             $('.reviews-count').html(response.reviews_count);

                             $.smoothScroll({
                             scrollTarget: '#review-'+response.id,
                             });
                             */


                            $('#review-'+response.id + ' .review-nf-autor ').tooltip({
                                html: true,
                                placement: 'top',
                                container: 'body',
                                title: 'Ваш отзыв добавлен!'
                            });


                            $('#review-'+response.id).tooltip('show');

                            //btn-review-create

                            $('.btn-review-create').tooltip({
                                html: true,
                                placement: 'top',
                                container: 'body',
                                title: 'Ваш отзыв добавлен! И будет размещён на сайте после модерации.'
                            });


                            $('.btn-review-create').tooltip('show');


                            $(window).trigger("compo.reviews.submit", [{
                                'eventCategory': 'Reviews',
                                'eventAction': 'Submit'
                            }]);

                            //$('#review-'+response.id).find(".rating").rating();
                        });

                        $('#review-modal').modal('hide');


                        // Закрыть форму
                    },
                    dataType: 'json'
                });
            });
        }

    });
})(jQuery);