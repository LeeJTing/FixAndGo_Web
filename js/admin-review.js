/* Admin review JS
   - expects `window.adminReviewControllerUrl` to be set to the admin review controller endpoint
   - fallback to review-controller.php if not provided
*/
(function($){
    var url = window.adminReviewControllerUrl || (window.reviewControllerUrl || '/controller/review-controller.php');

    $(function(){
        // Product list: view reviews button
        $(document).on('click', '.view-reviews-btn', function(){
            var pid = $(this).data('product-id');
            if(!pid) return;
            // navigate to admin product review page (implement server-side page)
            window.location.href = '/pages/admin/product-reviews.php?id=' + encodeURIComponent(pid);
        });

        // Toggle valid/invalid for a review (admin)
        $(document).on('click', '.btn-valid-toggle', function(){
            if ($(this).prop('disabled')) return;
            var $btn = $(this);
            var reviewId = $btn.data('review-id');
            var current = $btn.data('is-valid') ? 1 : 0;
            var newVal = current ? 0 : 1;
            $btn.prop('disabled', true);
            $.post(url, { action: 'admin_toggle_valid', review_id: reviewId, value: newVal }, function(resp){
                if(resp && resp.ok){
                    $btn.data('is-valid', newVal);
                    if(newVal){ $btn.removeClass('btn-invalid').addClass('btn-valid').text('Valid'); }
                    else { $btn.removeClass('btn-valid').addClass('btn-invalid').text('Invalid'); }
                } else {
                    alert(resp && resp.message ? resp.message : 'Failed to update');
                }
            }, 'json').fail(function(){ alert('Network error'); })
            .always(function(){ $btn.prop('disabled', false); });
        });

        // Block user (admin)
        $(document).on('click', '.btn-block-user', function(){
            if ($(this).prop('disabled')) return;
            if(!confirm('Block this user?')) return;
            var $btn = $(this);
            var userId = $btn.data('user-id');
            var reviewId = $btn.data('review-id');
            $btn.prop('disabled', true);
            $.post(url, { action: 'admin_block_user', user_id: userId, review_id: reviewId }, function(resp){
                if(resp && resp.ok){
                        $btn.text('Blocked').prop('disabled', true);
                        // mark review as invalid and disable valid-toggle for reviews from this user
                        var $row = $btn.closest('.review-row');
                        var $valid = $row.find('.btn-valid-toggle');
                        if($valid && $valid.length){
                            $valid.data('is-valid', 0).prop('disabled', true).removeClass('btn-valid').addClass('btn-invalid').text('Invalid');
                        }
                } else {
                    alert(resp && resp.message ? resp.message : 'Failed to block user');
                    $btn.prop('disabled', false);
                }
            }, 'json').fail(function(){ alert('Network error'); $btn.prop('disabled', false); });
        });

        // Delete review (admin) — uses existing delete action
        $(document).on('click', '.btn-delete-review', function(){
            if ($(this).prop('disabled')) return;
            if(!confirm('Delete this review permanently?')) return;
            var $btn = $(this);
            var reviewId = $btn.data('review-id');
            $btn.prop('disabled', true);
            $.post(url, { action: 'delete', review_id: reviewId }, function(resp){
                if(resp && resp.ok){
                    $btn.closest('.review-row').remove();
                } else {
                    alert(resp && resp.message ? resp.message : 'Failed to delete');
                    $btn.prop('disabled', false);
                }
            }, 'json').fail(function(){ alert('Network error'); $btn.prop('disabled', false); });
        });
    });
})(jQuery);
