jQuery(document).ready(function($) {
    // ==============================
    // Overlay + Progress Bar
    // ==============================
    var overlay = $('<div class="efc-overlay">' +
        '<div class="efc-spinner"></div>' +
        '<p>Processing...</p>' +
        '<div class="efc-progress"><div class="efc-progress-bar"></div></div>' +
    '</div>').hide();
    $('body').append(overlay);

    // ==============================
    // Toast Notifications
    // ==============================
    var toastContainer = $('<div class="efc-toast-container"></div>');
    $('body').append(toastContainer);

    function showToast(message, type) {
        var toast = $('<div class="efc-toast"></div>').text(message);
        toast.addClass(type); // success or error
        toastContainer.append(toast);
        toast.fadeIn(300);

        setTimeout(function() {
            toast.fadeOut(300, function() { $(this).remove(); });
        }, 3000);
    }

    // ==============================
    // Confirmation Modal
    // ==============================
    var modal = $('<div class="efc-modal-overlay">' +
        '<div class="efc-modal">' +
            '<h2>⚠️ Confirm Cleanup</h2>' +
            '<p>Are you sure you want to run cleanup now? This will purge all cached files immediately.</p>' +
            '<div class="efc-modal-actions">' +
                '<button class="button button-primary efc-confirm-run">Yes, Run Cleanup</button>' +
                '<button class="button efc-cancel-run">Cancel</button>' +
            '</div>' +
        '</div>' +
    '</div>').hide();
    $('body').append(modal);

    // ==============================
    // Clear Cache Function
    // ==============================
    function clearCache() {
        overlay.fadeIn(300);
        $('.efc-progress-bar').css('width', '0%');

        var progress = 0;
        var interval = setInterval(function() {
            progress += 15;
            if (progress > 90) progress = 90;
            $('.efc-progress-bar').css('width', progress + '%');
        }, 300);

        $.post(ajaxurl, { action: 'efc_clear_cache_ajax' }, function(response) {
            clearInterval(interval);
            $('.efc-progress-bar').css('width', '100%');

            setTimeout(function() {
                overlay.fadeOut(300);
                $('.efc-progress-bar').css('width', '0%');
            }, 500);

            if (response.success) {
                $('#easy-front-end-cache_status').html(
                    '<strong style="color: rgb(0, 115, 170);">Cache Folder Size :</strong> ' + response.data.size + '<br>' +
                    '<strong style="color: rgb(0, 115, 170);">Total Cached Files:</strong> ' + response.data.count
                );
                $('p:contains("Last Cleared")').html('Last Cleared: ' + response.data.lastCleared);
                showToast(response.data.message, 'success');
            } else {
                showToast(response.data.message, 'error');
            }
        });
    }

    // ==============================
    // Reschedule Cron Function
    // ==============================
    $(document).on('click', '.efc-reschedule-btn', function(e) {
        e.preventDefault();
        $('.efc-reschedule-status').text('Rescheduling...').css('color', 'orange');

        $.post(ajaxurl, { action: 'efc_reschedule_ajax' }, function(response) {
            if (response.success) {
                $('.efc-reschedule-status').text(response.data.message).css('color', 'green');
                $('p:contains("Next Scheduled Cleanup")').html(
                    'Next Scheduled Cleanup: ' + response.data.next +
                    ' <button class="button efc-reschedule-btn">🔄 Reschedule Now</button>' +
                    ' <button class="button efc-run-cleanup-btn">⚡ Run Cleanup Now</button>' +
                    ' <span class="efc-reschedule-status"></span>'
                );
                showToast(response.data.message + ' Next run: ' + response.data.next, 'success');
            } else {
                $('.efc-reschedule-status').text(response.data.message).css('color', 'red');
                showToast(response.data.message, 'error');
            }
        });
    });

    // ==============================
    // Run Cleanup Now (with modal)
    // ==============================
    $(document).on('click', '.efc-run-cleanup-btn', function(e) {
        e.preventDefault();
        modal.fadeIn(200);
    });

    $(document).on('click', '.efc-cancel-run', function() {
        modal.fadeOut(200);
    });

    $(document).on('click', '.efc-confirm-run', function() {
        modal.fadeOut(200);
        overlay.fadeIn(300);
        $('.efc-progress-bar').css('width', '0%');

        var progress = 0;
        var interval = setInterval(function() {
            progress += 15;
            if (progress > 90) progress = 90;
            $('.efc-progress-bar').css('width', progress + '%');
        }, 300);

        $.post(ajaxurl, { action: 'efc_run_cleanup_ajax' }, function(response) {
            clearInterval(interval);
            $('.efc-progress-bar').css('width', '100%');

            setTimeout(function() {
                overlay.fadeOut(300);
                $('.efc-progress-bar').css('width', '0%');
            }, 500);

            if (response.success) {
                $('#easy-front-end-cache_status').html(
                    '<strong style="color: rgb(0, 115, 170);">Cache Folder Size :</strong> ' + response.data.size + '<br>' +
                    '<strong style="color: rgb(0, 115, 170);">Total Cached Files:</strong> ' + response.data.count
                );
                $('p:contains("Last Cleared")').html('Last Cleared: ' + response.data.lastCleared);
                showToast(response.data.message, 'success');
            } else {
                showToast(response.data.message, 'error');
            }
        });
    });

    // ==============================
    // Bind Clear Cache Buttons
    // ==============================
    $(document).on('click', '.efc-clear-cache-link, .efc-clear-cache-btn', function(e) {
        e.preventDefault();
        clearCache();
    });
});
