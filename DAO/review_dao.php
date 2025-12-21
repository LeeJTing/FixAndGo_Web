<?php
require_once __DIR__ . "/../_base.php";

function getAverageRatingByProduct($productId)
{
    global $_db;
    $stmt = $_db->prepare("SELECT ROUND(AVG(rating), 1) AS avg_rating,
                           COUNT(*) AS total_reviews
                           FROM REVIEW
                           WHERE product_id = ?
                           AND is_valid = 1");
    $stmt->execute([$productId]);

    return $stmt->fetch();
}

function addReview($user_id, $productId, $comment, $rating)
{
    global $_db;
    $stmt = $_db->prepare("INSERT INTO REVIEW (user_id, product_id, comment, rating)
                            VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $productId, $comment, $rating]);
}

function getReviewByProductID($productId)
{
    global $_db;
    $stmt = $_db->prepare("SELECT * FROM REVIEW WHERE product_id = ? AND is_valid = 1 ORDER BY reviewed_at DESC");
    $stmt->execute([$productId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateReview($review_id, $comment, $rating)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE REVIEW
                        SET comment = ?, rating = ? 
                        WHERE review_id = ?");
    $stmt->execute([$comment, $rating, $review_id]);
}

function invalidReview($review_id)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE REVIEW
                           SET is_valid = 0
                           WHERE review_id = ?");
    $stmt->execute([$review_id]);
}

function validReview($review_id)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE REVIEW
                           SET is_valid = 1
                           WHERE review_id = ?");
    $stmt->execute([$review_id]);
}

function getReviewById($review_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT * FROM REVIEW WHERE review_id = ?");
    $stmt->execute([$review_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserNameByReviewId($review_id)
{
    global $_db;

    $stmt = $_db->prepare("SELECT user_name FROM USERS
                           WHERE user_id = (SELECT user_id FROM review
                                            WHERE review_id = ?)");
    $stmt->execute([$review_id]);

    return $stmt->fetch()->user_name;
}

function adminGetReviewsByProduct($productId)
{
    global $_db;
    $stmt = $_db->prepare("SELECT r.*, u.user_id, u.user_name, u.account_status
                           FROM REVIEW r
                           LEFT JOIN USERS u ON u.user_id = r.user_id
                           WHERE r.product_id = ?
                           ORDER BY r.reviewed_at DESC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function invalidReviewsByUser($user_id)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE REVIEW SET is_valid = 0 WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->rowCount();
}

function deleteReviewId($review_id)
{
    global $_db;

    $stmt = $_db->prepare("DELETE FROM REVIEW WHERE review_id = ?");
    $stmt->execute([$review_id]);
}
