$(function(){
  // Open add review modal
  $('#openAddReview').on('click', function(){
    $('#addReviewModal').show().addClass('open');
  });

  function closeModal(selector){
    $(selector).hide().removeClass('open');
  }

  // Close buttons
  $(document).on('click', '.modal-close, .modal-cancel', function(){
    closeModal($(this).closest('.modal'));
  });

  // Edit button - open edit modal and populate
  $(document).on('click', '.btn-edit', function(){
    var id = $(this).data('review-id');
    // fetch via AJAX
    $.post(window.reviewControllerUrl, { action: 'fetch', review_id: id }, function(resp){
      if (!resp.ok) { alert(resp.message || 'Failed to fetch'); return; }
      var r = resp.review;
      $('#editReviewModal #modalReviewId').val(r.review_id);
      $('#editReviewModal #modalRating').val(r.rating);
      $('#modalRatingValue').text(r.rating);
      $('#editReviewModal #modalComment').val(r.comment);
      $('#editReviewModal').show().addClass('open');
    }, 'json').fail(function(){ alert('Network error'); });
  });

  // Keep rating displays in sync with range inputs (#modalRating, #addRating)
  (function(){
    function updateRatingDisplayFor(inputId, displayId){
      var $r = $('#' + inputId);
      var $disp = $('#' + displayId);
      if($r.length && $disp.length){
        var v = parseFloat($r.val() || 0).toFixed(1);
        $disp.text(v);
      }
    }

    function updateAllRatingDisplays(){
      updateRatingDisplayFor('modalRating','modalRatingValue');
      updateRatingDisplayFor('addRating','addRatingValue');
    }

    $(document).on('input change', '#modalRating, #addRating', updateAllRatingDisplays);

    // Observe modal attribute changes to refresh display when shown
    ['editReviewModal','addReviewModal'].forEach(function(id){
      var el = document.getElementById(id);
      if(el){
        var obs = new MutationObserver(function(){ updateAllRatingDisplays(); });
        obs.observe(el, { attributes: true, attributeFilter: ['aria-hidden'] });
      }
    });

    // initial run
    $(function(){ updateAllRatingDisplays(); });
  })();

  // Submit edit
  $('#editReviewForm').on('submit', function(e){
    e.preventDefault();
    var fd = $(this).serialize();
    $.ajax({ url: window.reviewControllerUrl, method: 'POST', data: fd, dataType: 'json' })
      .done(function(r){ if (r.ok) location.reload(); else alert(r.message || 'Failed'); })
      .fail(function(){ alert('Network error'); });
  });

  // Delete
  $(document).on('click', '.btn-delete', function(){
    var id = $(this).data('review-id');
    if (!confirm('Delete this review?')) return;
    $.post(window.reviewControllerUrl, { action: 'delete', review_id: id }, function(resp){
      if (resp.ok) location.reload(); else alert(resp.message || 'Failed');
    }, 'json').fail(function(){ alert('Network error'); });
  });

  // Add review modal submit
  $('#addReviewForm').on('submit', function(e){
    e.preventDefault();
    var fd = $(this).serialize();
    $.ajax({ url: window.reviewControllerUrl, method: 'POST', data: fd, dataType: 'json' })
      .done(function(r){ if (r.ok) location.reload(); else alert(r.message || 'Failed'); })
      .fail(function(){ alert('Network error'); });
  });

  // Close when clicking outside dialog
  $(document).on('click', '.modal', function(e){ if ($(e.target).is('.modal')) closeModal(this); });

});
