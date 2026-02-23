/**
 * Easy Front End Cache - Admin Scripts
 */

document.addEventListener('DOMContentLoaded', function () {
    // Highlight cache stats when page loads
    const cacheStats = document.querySelectorAll('.wrap p strong');
    cacheStats.forEach(stat => {
        stat.style.color = '#0073aa'; // WordPress blue
    });

    // Add confirmation before clearing all cache
    const clearCacheBtn = document.querySelector('input[name="efc_clear_cache"]');
    if (clearCacheBtn) {
        clearCacheBtn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to clear all cached files?')) {
                e.preventDefault();
            }
        });
    }

    // Toggle description emphasis for exclusions
    const exclusionNotes = document.querySelectorAll('.form-table td .description em');
    exclusionNotes.forEach(note => {
        note.style.backgroundColor = '#f8f9fa';
        note.style.padding = '2px 4px';
        note.style.borderRadius = '3px';
    });
});