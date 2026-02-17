/**
 * Admin JavaScript for DHR Hotel Management
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Image upload functionality
        $('#upload-image-btn').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var fileFrame = wp.media({
                title: 'Select Hotel Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });
            
            fileFrame.on('select', function() {
                var attachment = fileFrame.state().get('selection').first().toJSON();
                $('#image_url').val(attachment.url);
            });
            
            fileFrame.open();
        });
        
        // Auto-fill Google Maps URL from coordinates
        $('#latitude, #longitude').on('blur', function() {
            var lat = $('#latitude').val();
            var lng = $('#longitude').val();
            
            if (lat && lng && !$('#google_maps_url').val()) {
                var mapsUrl = 'https://www.google.com/maps?q=' + lat + ',' + lng;
                $('#google_maps_url').val(mapsUrl);
            }
        });
        
        // Copy shortcode to clipboard (for all copy buttons)
        $(document).on('click', '.dhr-copy-btn', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var shortcode = button.data('shortcode') || button.siblings('.dhr-shortcode-input').val();
            var shortcodeInput = button.siblings('.dhr-shortcode-input');
            
            if (shortcodeInput.length > 0) {
                shortcodeInput.select();
                shortcodeInput[0].setSelectionRange(0, 99999); // For mobile devices
            }
            
            try {
                // Copy to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shortcode).then(function() {
                        updateCopyButton(button);
                    });
                } else {
                    document.execCommand('copy');
                    updateCopyButton(button);
                }
            } catch (err) {
                // Fallback
                if (shortcodeInput.length > 0) {
                    shortcodeInput.select();
                    document.execCommand('copy');
                    updateCopyButton(button);
                } else {
                    alert('Unable to copy. Please select and copy manually.');
                }
            }
        });

        // SHR hotel sync from list page (AJAX) - add new hotel only; server checks if code exists
        $('#dhr-shr-sync-hotel-list-form').on('submit', function(e) {
            var $form = $(this);
            var $btn  = $('#dhr-shr-sync-hotel-list-btn');
            var code  = $('#dhr_shr_sync_hotel_code').val().trim();

            if (!code) {
                alert('Please enter a hotel code.');
                e.preventDefault();
                return false;
            }

            if (typeof dhrHotelAdmin === 'undefined') {
                return true;
            }

            e.preventDefault();
            $btn.prop('disabled', true);

            $.post(
                dhrHotelAdmin.ajaxurl,
                {
                    action: 'dhr_sync_shr_hotel_ajax',
                    nonce: dhrHotelAdmin.shrSyncNonce || dhrHotelAdmin.nonce,
                    hotel_code: code,
                    update_existing: '0'
                }
            ).done(function(response) {
                if (response && response.success) {
                    if (dhrHotelAdmin.listUrl) {
                        window.location.href = dhrHotelAdmin.listUrl + '&message=added';
                    } else {
                        window.location.reload();
                    }
                } else {
                    var msg = response && response.data && response.data.message ? response.data.message : 'Unknown error.';
                    if (dhrHotelAdmin.listUrl) {
                        window.location.href = dhrHotelAdmin.listUrl + '&message=error&error=' + encodeURIComponent(msg);
                    } else {
                        alert('Sync failed: ' + msg);
                    }
                }
            }).fail(function() {
                var msg = 'An error occurred while syncing the hotel. Please try again.';
                if (dhrHotelAdmin.listUrl) {
                    window.location.href = dhrHotelAdmin.listUrl + '&message=error&error=' + encodeURIComponent(msg);
                } else {
                    alert(msg);
                }
            }).always(function() {
                $btn.prop('disabled', false);
            });
        });

        // Per-row Sync button: re-sync existing hotel from SHR
        $(document).on('click', '.dhr-row-sync-btn', function() {
            var $btn = $(this);
            var code = $btn.data('hotel-code');
            if (!code || typeof dhrHotelAdmin === 'undefined') return;

            $btn.prop('disabled', true);
            $.post(
                dhrHotelAdmin.ajaxurl,
                {
                    action: 'dhr_sync_shr_hotel_ajax',
                    nonce: dhrHotelAdmin.shrSyncNonce || dhrHotelAdmin.nonce,
                    hotel_code: code,
                    update_existing: '1'
                }
            ).done(function(response) {
                if (response && response.success) {
                    if (dhrHotelAdmin.listUrl) {
                        window.location.href = dhrHotelAdmin.listUrl + '&message=updated';
                    } else {
                        window.location.reload();
                    }
                } else {
                    var msg = response && response.data && response.data.message ? response.data.message : 'Unknown error.';
                    if (dhrHotelAdmin.listUrl) {
                        window.location.href = dhrHotelAdmin.listUrl + '&message=error&error=' + encodeURIComponent(msg);
                    } else {
                        alert('Sync failed: ' + msg);
                    }
                }
            }).fail(function() {
                var msg = 'An error occurred while syncing the hotel. Please try again.';
                if (dhrHotelAdmin.listUrl) {
                    window.location.href = dhrHotelAdmin.listUrl + '&message=error&error=' + encodeURIComponent(msg);
                } else {
                    alert(msg);
                }
            }).always(function() {
                $btn.prop('disabled', false);
            });
        });
        
        function updateCopyButton(button) {
            button.addClass('copied');
            button.find('.dhr-copy-text').hide();
            button.find('.dhr-copied-text').show();
            
            setTimeout(function() {
                button.removeClass('copied');
                button.find('.dhr-copy-text').show();
                button.find('.dhr-copied-text').hide();
            }, 2000);
        }
        
    });
    
})(jQuery);


