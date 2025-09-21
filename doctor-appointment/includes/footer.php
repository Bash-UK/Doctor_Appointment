</div> <!-- container -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('.select2-multiple').select2({
        placeholder: 'Select options',
        allowClear: true,
        width: '200px'
    });
    // close select2 on outside click (bootstrap modal compatibility)
    $(document).on('click', function(e) {
        var container = $('.select2-container');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            // do nothing
        }
    });
});
</script>
</body>
</html>
