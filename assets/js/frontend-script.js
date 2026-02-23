(function ($) {
    'use strict';

    // Book Now: availability check then open Windsurfer booking URL (today / tomorrow)
    $(document).on('click', '.dhr-hotel-rooms-shortcode .bys-book-now-link', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var hotelCode = $btn.data('hotel-code');
        if (!hotelCode || typeof dhrBookNow === 'undefined') return;

        var today = new Date();
        var tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        var fmt = function (d) {
            var y = d.getFullYear(), m = String(d.getMonth() + 1).padStart(2, '0'), day = String(d.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + day;
        };
        var checkIn = fmt(today);
        var checkOut = fmt(tomorrow);
        var rooms = parseInt($btn.data('rooms'), 10) || 1;
        var adults = parseInt($btn.data('adults'), 10) || 2;
        var children = parseInt($btn.data('children'), 10) || 0;
        var childAge = $btn.data('child-age');
        if (childAge !== undefined && childAge !== '' && children <= 0) children = 1;

        $btn.addClass('dhr-book-now-loading').prop('disabled', true);
        $.post(dhrBookNow.ajaxUrl, {
            action: 'dhr_get_availability_booking_url',
            nonce: dhrBookNow.nonce,
            hotel_code: hotelCode,
            channel_id: parseInt($btn.data('channel-id'), 10) || 0,
            check_in: checkIn,
            check_out: checkOut,
            rooms: rooms,
            adults: adults,
            child_age: children ? (childAge !== undefined && childAge !== '' ? childAge : '0') : ''
        })
            .done(function (res) {
                if (res && res.success && res.url) {
                    window.open(res.url, '_blank', 'noopener,noreferrer');
                } else {
                    var msg = (res && res.errors && res.errors.length) ? res.errors.join('\n') : 'Unable to get booking link. Please try again.';
                    alert(msg);
                }
            })
            .fail(function () {
                alert('We could not reach the booking service. Please try again.');
            })
            .always(function () {
                $btn.removeClass('dhr-book-now-loading').prop('disabled', false);
            });
    });
})(jQuery);
