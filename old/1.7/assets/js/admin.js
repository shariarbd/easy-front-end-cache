jQuery(document).ready(function($) {

    // Helper: update stats everywhere
    function updateStats(data) {
        // Admin bar nodes
        $('#wp-admin-bar-efc-size .ab-item').text('Size: ' + data.size);
        $('#wp-admin-bar-efc-files .ab-item').text('Files: ' + data.count);

        // Settings page span
        $('#easy-front-end-cache_stattus').html(
            '<strong style="color: rgb(0, 115, 170);">Cache Folder Size :</strong> ' + data.size + '<br>' +
            '<strong style="color: rgb(0, 115, 170);">Total Cached Files:</strong> ' + data.count
        );
    }

    // Helper: show spinner while waiting
    function showSpinner() {
        $('#wp-admin-bar-efc-size .ab-item').text('Size: ⏳ …');
        $('#wp-admin-bar-efc-files .ab-item').text('Files: ⏳ …');
        $('#easy-front-end-cache_stattus').html(
            '<strong style="color: rgb(0, 115, 170);">Cache Folder Size :</strong> ⏳ …<br>' +
            '<strong style="color: rgb(0, 115, 170);">Total Cached Files:</strong> ⏳ …'
        );
    }

    // Admin bar clear
    $(document).on('click', '.efc-clear-cache-link', function(e) {
        e.preventDefault();
        var $link = $(this);
        $link.text('⏳ Clearing...');
        showSpinner();

        $.post(ajaxurl, { action: 'efc_clear_cache_ajax' })
            .done(function(response) {
                if (response.success) {
                    $link.text('✅ Cleared');
                    updateStats(response.data);
                } else {
                    $link.text('⚠️ Failed');
                }
            })
            .fail(function() {
                $link.text('⚠️ AJAX failed');
            })
            .always(function() {
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
        showSpinner();

        $.post(ajaxurl, { action: 'efc_clear_cache_ajax' })
            .done(function(response) {
                if (response.success) {
                    $btn.text('✅ Cleared');
                    $status.removeClass('loading').addClass('success').text(response.data.message);
                    updateStats(response.data);
                } else {
                    $btn.text('⚠️ Failed');
                    $status.removeClass('loading').addClass('error').text(response.data.message);
                }
            })
            .fail(function() {
                $btn.text('⚠️ Failed');
                $status.removeClass('loading').addClass('error').text('AJAX request failed.');
            })
            .always(function() {
                setTimeout(function() {
                    $btn.prop('disabled', false).text('🧹 Clean All Cache Now');
                    $status.text('');
                }, 2000);
            });
    });

    // Auto-refresh stats every 30s
    setInterval(function() {
        $.post(ajaxurl, { action: 'efc_get_cache_stats' }, function(response) {
            if (response.success) {
                updateStats(response.data);
            }
        });
    }, 30000); 

});


// Manual refresh stats button
$(document).on('click', '.efc-refresh-stats-btn', function(e) {
    e.preventDefault();
    var $btn = $(this);
    var $status = $('.efc-refresh-status');
    $btn.prop('disabled', true).text('⏳ Refreshing...');
    $status.removeClass('success error').addClass('loading').text('Fetching latest stats...');
    showSpinner();

    $.post(ajaxurl, { action: 'efc_get_cache_stats' })
        .done(function(response) {
            if (response.success) {
                updateStats(response.data);
                $status.removeClass('loading').addClass('success').text('✅ Stats updated');
            } else {
                $status.removeClass('loading').addClass('error').text('⚠️ Failed to fetch stats');
            }
        })
        .fail(function() {
            $status.removeClass('loading').addClass('error').text('❌ AJAX request failed');
        })
        .always(function() {
            setTimeout(function() {
                $btn.prop('disabled', false).text('🔄 Refresh Stats');
                $status.text('');
            }, 2000);
        });
});