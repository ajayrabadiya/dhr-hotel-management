(function ($) {
    'use strict';

    $(document).on('click', '.bys-book-now-link', function (e) {
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
        var dateRe = /^\d{4}-\d{2}-\d{2}$/;
        var checkInRaw = $btn.data('checkin');
        var checkOutRaw = $btn.data('checkout');
        var checkIn = (checkInRaw && dateRe.test(String(checkInRaw))) ? String(checkInRaw) : fmt(today);
        var checkOut = (checkOutRaw && dateRe.test(String(checkOutRaw))) ? String(checkOutRaw) : fmt(tomorrow);
        var rooms = parseInt($btn.data('rooms'), 10) || 1;
        var adults = parseInt($btn.data('adults'), 10) || 2;
        var children = parseInt($btn.data('children'), 10) || 0;

        var originalText = $btn.text();
        $btn.addClass('dhr-book-now-loading').prop('disabled', true).text('Loading...');

        $.ajax({
            url: dhrBookNow.ajaxUrl,
            type: 'POST',
            data: {
                action: 'bys_generate_deep_link',
                nonce: dhrBookNow.nonce,
                checkin: checkIn,
                checkout: checkOut,
                adults: adults,
                children: children,
                rooms: rooms,
                hotel_code: hotelCode,
                property_id: $btn.data('property-id') || ''
            },
            success: function (res) {
                if (res && res.success && res.data && res.data.link) {
                    window.open(res.data.link, '_blank', 'noopener,noreferrer');
                    $btn.removeClass('dhr-book-now-loading').prop('disabled', false).text(originalText);
                } else {
                    alert('Error generating booking link. Please try again.');
                    $btn.removeClass('dhr-book-now-loading').prop('disabled', false).text(originalText);
                }
            },
            error: function () {
                alert('Error generating booking link. Please try again.');
                $btn.removeClass('dhr-book-now-loading').prop('disabled', false).text(originalText);
            }
        });
    });
})(jQuery);
