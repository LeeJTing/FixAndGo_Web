<?php
require_once __DIR__ . "/../_base.php";
require_once __DIR__ . "/../dao/review_dao.php";
require_once __DIR__ . "/../dao/security_dao.php";

// Determine product id: prefer provided $productId, else GET 'id' or 'product_id'
$productId = (get('id'));
if (!$productId) {
    echo "<p>No product specified.</p>";
    return;
}

$avg = getAverageRatingByProduct($productId);
$avg_rating = $avg->avg_rating ?? 0;
$total_reviews = $avg->total_reviews ?? 0;
$reviews = getReviewByProductID($productId);

function largerThanZeroFive($num): bool
{
    $decimal = $num - floor($num);
    return $decimal > 0.5;
}

?>
<link rel="stylesheet" href="<?= $rootDir ?>/css/review.css">

<div class="reviewSection">
    <h2 class="section-title">Customer Reviews</h2>

    <!-- Add review button (opens modal) -->
    <?php if (temp('USER_ID') && temp('USER_ID') !== 'Guest'): ?>
        <div class="add-review-top">
            <button id="openAddReview" class="btn-primary">Add a review</button>
        </div>
    <?php else: ?>
        <div class="add-review-top">
            <a href="<?= $rootDir ?>/pages/guest/login.php" class="btn-primary">Log in to review</a>
        </div>
    <?php endif; ?>

    <div class="reviews-summary">
        <span class="big-rating"> <?= htmlspecialchars($avg_rating) ?></span>
        <div class="stars">
            <?php for ($i = 1; $i <= $avg_rating; $i++) : ?>
                <i class="fa-solid fa-star"></i>
            <?php endfor ?>
            <?php if (largerThanZeroFive($avg_rating)) : ?>
                <i class="fa-solid fa-star-half-alt"></i>
            <?php endif ?>
            <?php if ($avg_rating != 5) :
                $start = largerThanZeroFive($avg_rating) ? 4 : 5;
                for ($i = $start; $i > $avg_rating; $i--) : ?>
                    <i class="fa-regular fa-star"></i>
                <?php endfor ?>
            <?php endif ?>
        </div>
        <p>Based on <?= intval($total_reviews) ?> reviews</p>
    </div>

    <?php if (!empty($reviews)): ?>
        <div class="review-list">
            <?php foreach ($reviews as $r):
                // $r may be assoc or object depending on DAO
                $user_id = is_array($r) ? $r['user_id'] : $r->user_id;
                $review_id = is_array($r) ? $r['review_id'] : $r->review_id;
                $user_name = getUserNameByReviewId($review_id);
                $comment = is_array($r) ? $r['comment'] : $r->comment;
                $rating = is_array($r) ? $r['rating'] : $r->rating;
                $created = is_array($r) ? ($r['reviewed_at'] ?? '') : ($r->reviewed_at ?? '');
                $user = getUserById($user_id);
            ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer">
                            <strong><?= htmlspecialchars($user_name) ?></strong>
                        </div>
                        <div class="review-rating">
                            <span class="rating"><?= htmlspecialchars($rating) ?>/5</span>
                            <?php for ($i = 1; $i <= $rating; $i++) : ?>
                                <i class="fa-solid fa-star filled"></i>
                            <?php endfor ?>
                            <?php if (largerThanZeroFive($rating)) : ?>
                                <i class="fa-solid fa-star-half-alt filled"></i>
                            <?php endif ?>
                            <?php if ($rating != 5) :
                                $start = largerThanZeroFive($rating) ? 4 : 5;
                                for ($i = $start; $i > $rating; $i--) : ?>
                                    <i class="fa-regular fa-star filled"></i>
                                <?php endfor ?>
                            <?php endif ?>
                        </div>
                        <?php if ($user_id == temp('USER_ID')) : ?>
                            <div class="review-action">
                                <button class="btn-edit" data-review-id="<?= htmlspecialchars($review_id) ?>" data-rating="<?= htmlspecialchars($rating) ?>" data-comment="<?= htmlspecialchars($comment) ?>">Edit</button>
                                <button class="btn-delete" data-review-id="<?= htmlspecialchars($review_id) ?>">Delete</button>
                            </div>
                        <?php endif ?>
                    </div>
                    <p class="review-date"><?= htmlspecialchars($created) ?></p>
                    <p class="review-text"><?= nl2br(htmlspecialchars($comment)) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No reviews yet. Be the first to review this product.</p>
    <?php endif; ?>

    <!-- Add/Edit Modals -->
    <?php if (temp('USER_ID') && temp('USER_ID') !== 'Guest'): ?>
    <div id="addReviewModal" class="modal" aria-hidden="true">
        <div class="modal-dialog">
            <button class="modal-close">x</button>
            <h3>Add Review</h3>
            <form id="addReviewForm">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($productId) ?>">
                <div class="form-group">
                    <label for="addRating">Rating <span id="addRatingValue"></span></label>
                    <input type="range" name="rating" id="addRating" min="0" max="5" step="0.1" value="5" required>
                </div>
                <div class="form-group">
                    <label for="addComment">Comment</label>
                    <textarea name="comment" id="addComment" rows="4" maxlength="2000" required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Submit</button>
                    <button type="button" class="modal-cancel btn-dark">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editReviewModal" class="modal" aria-hidden="true">
        <div class="modal-dialog">
            <button class="modal-close">x</button>
            <h3>Edit Review</h3>
            <form id="editReviewForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="review_id" id="modalReviewId">
                <div class="form-group">
                    <label for="modalRating">Rating <span id="modalRatingValue"></span></label>
                    <input type="range" name="rating" id="modalRating" min="0" max="5" step="0.1" value="" required>
                </div>
                <div class="form-group">
                    <label for="modalComment">Comment</label>
                    <textarea name="comment" id="modalComment" rows="4" maxlength="2000" required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" class="modal-cancel btn-dark">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        window.reviewControllerUrl = '<?= $rootDir ?>/controller/review-controller.php';
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= $rootDir ?>/js/review.js"></script>

</div>