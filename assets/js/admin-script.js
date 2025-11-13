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
        
        // Copy shortcode to clipboard
        $('#dhr-copy-shortcode-btn').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var shortcodeInput = $('#dhr-shortcode-input');
            var shortcode = shortcodeInput.val();
            
            // Select the text
            shortcodeInput.select();
            shortcodeInput[0].setSelectionRange(0, 99999); // For mobile devices
            
            try {
                // Copy to clipboard
                document.execCommand('copy');
                
                // Update button state
                button.addClass('copied');
                button.find('.dhr-copy-text').hide();
                button.find('.dhr-copied-text').show();
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    button.removeClass('copied');
                    button.find('.dhr-copy-text').show();
                    button.find('.dhr-copied-text').hide();
                }, 2000);
                
            } catch (err) {
                // Fallback for browsers that don't support execCommand
                // Try modern clipboard API
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shortcode).then(function() {
                        button.addClass('copied');
                        button.find('.dhr-copy-text').hide();
                        button.find('.dhr-copied-text').show();
                        
                        setTimeout(function() {
                            button.removeClass('copied');
                            button.find('.dhr-copy-text').show();
                            button.find('.dhr-copied-text').hide();
                        }, 2000);
                    });
                } else {
                    alert('Unable to copy. Please select and copy manually.');
                }
            }
        });
        
    });
    
})(jQuery);


