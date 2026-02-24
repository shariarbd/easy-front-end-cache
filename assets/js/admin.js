jQuery(document).ready(function($) {

    // Admin bar clear
    $(document).on('click', '.efc-clear-cache-link', function(e) {
        e.preventDefault();
        var $link = $(this);
        $link.text('⏳ Clearing...');

        $.post(ajaxurl, { action: 'efc_clear_cache_ajax' }, function(response) {
            if (response.success) {
                $link.text('✅ Cleared');
            } else {
                $link.text('⚠️ Failed');
            }
            setTimeout(function() { $link.text('🧹 Clean All'); }, 2000);
        });
    });

    // Settings page clear
    $(document).on('click', '.efc-clear-cache-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $status = $('.efc-clear-status');
        $btn.prop('disabled', true).text('⏳ Clearing...');
        $status.removeClass('success error').addClass('loading').text('Clearing cache...');

        $.post(ajaxurl, { action: 'efc_clear_cache_ajax' }, function(response) {
            if (response.success) {
                $btn.text('✅ Cleared');
                $status.removeClass('loading').addClass('success').text(response.data.message);
            } else {
                $btn.text('⚠️ Failed');
                $status.removeClass('loading').addClass('error').text(response.data.message);
            }
            setTimeout(function() {
                $btn.prop('disabled', false).text('🧹 Clean All Cache Now');
                $status.text('');
            }, 2000);
        });
    });

});