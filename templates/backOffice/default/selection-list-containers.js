<script>
$(function () {
    $('a.selection-container-delete').click(function() {
        $('#selection_container_delete_id').val($(this).data('id'));
    });

    $("#selection-list-containers-table .selectionContainerVisibleToggle").on('switch-change', function() {
        $.ajax({
            url : "{url path='admin/selection/container/toggle-online'}",
            data : {
                selection_container_id : $(this).data('id'),
                action : 'visibilityToggle'
            }
        });
    });

    $('.selectionContainerPositionChange').editable({
        type        : 'text',
        title       : '{intl l="Enter new selection container position"}',
        mode        : 'popup',
        inputclass  : 'input-mini',
        placement   : 'left',
        success     : function(response, newValue) {
            return;
            // The URL template
            var url = "{url noamp=1 path='/admin/selection/container/update-position' selection_container_id='__ID__' position='__POS__'}";
            // Perform subtitutions
            url = url.replace('__ID__', $(this).data('id')).replace('__POS__', newValue);
            // Reload the page
            location.href = url;
        }
    });

});
</script>