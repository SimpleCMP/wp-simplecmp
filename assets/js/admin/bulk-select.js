/* SimpleCMP Bulk Select */
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.querySelector('#cb-select-all-1');
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');

    if (selectAll && checkboxes.length) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }
});
