<script>
$(function () {
    // Set proper folder ID in delete from
    $('a.selection-delete').click(function(ev) {
        $('#selection_delete_id').val($(this).data('id'));
    });

    $("#selection-list-selections-table .selectionVisibleToggle").on('switch-change', function(event, data) {
        $.ajax({
            url : "{url path='admin/selection/toggle-online'}",
            data : {
                selectionId : $(this).data('id'),
                action : 'visibilityToggle'
            }
        });
    });

    $('.selectionPositionChange').editable({
        type        : 'text',
        title       : '{intl l="Enter new Selection position"}',
        mode        : 'popup',
        inputclass  : 'input-mini',
        placement   : 'left',
        success     : function(response, newValue) {
            // The URL template
            var url = "{url noamp=1 path='/admin/selection/update-position' selection_id='__ID__' position='__POS__'}";
            console.log($(this).data);
            // Perform subtitutions
            url = url.replace('__ID__', $(this).data('id')).replace('__POS__', newValue);
            // Reload the page
            location.href = url;
        }
    });
});
</script>