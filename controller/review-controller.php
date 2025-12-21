<?php
require_once __DIR__ . "/../_base.php";
require_once __DIR__ . "/../dao/review_dao.php";

// Support AJAX and form submissions. Actions: add (default), edit, delete, fetch
if (is_post()) {
	$action = post('action') ?? 'add';

	// require login for all actions
	$userId = temp('USER_ID');
	if (!$userId || $userId === 'Guest') {
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			header('Content-Type: application/json');
			echo json_encode(['ok' => false, 'message' => 'Login required']);
			exit;
		}
		redirect($rootDir . '/pages/guest/login.php');
	}

	if ($action === 'add') {
		$productId = post('product_id');
		$comment = post('comment');
		$rating = (float) post('rating');

		if (!$productId || $rating < 0 || $rating > 5) {
			$referer = $_SERVER['HTTP_REFERER'] ?? ($rootDir . '/');
			redirect($referer);
		}

		if (strlen($comment) > 2000) $comment = substr($comment, 0, 2000);
		addReview($userId, $productId, $comment, $rating);

		// if AJAX, return JSON
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			header('Content-Type: application/json');
			echo json_encode(['ok' => true]);
			exit;
		}

		$referer = $_SERVER['HTTP_REFERER'] ?? ($rootDir . '/');
		redirect($referer);
	}

	if ($action === 'edit') {
		$review_id = post('review_id');
		$comment = post('comment');
		$rating = (float) post('rating');

		if (!$review_id) {
			header('Content-Type: application/json');
			echo json_encode(['ok' => false, 'message' => 'Missing review id']);
			exit;
		}

		$existing = getReviewById($review_id);
		if (!$existing || $existing['user_id'] !== $userId) {
			header('Content-Type: application/json');
			echo json_encode(['ok' => false, 'message' => 'Permission denied']);
			exit;
		}

		if ($rating < 0 || $rating > 5) $rating = $existing['rating'];
		if (strlen($comment) > 2000) $comment = substr($comment, 0, 2000);

		updateReview($review_id, $comment, $rating);
		header('Content-Type: application/json');
		echo json_encode(['ok' => true]);
		exit;
	}

	if ($action === 'delete') {
		$review_id = post('review_id');
		if (!$review_id) {
			header('Content-Type: application/json');
			echo json_encode(['ok' => false, 'message' => 'Missing review id']);
			exit;
		}

		$existing = getReviewById($review_id);
		if (!$existing || $existing['user_id'] !== $userId) {
			header('Content-Type: application/json');
			echo json_encode(['ok' => false, 'message' => 'Permission denied']);
			exit;
		}

		// soft delete
		invalidReview($review_id);
		header('Content-Type: application/json');
		echo json_encode(['ok' => true]);
		exit;
	}

	if ($action === 'fetch') {
		$review_id = post('review_id');
		if (!$review_id) {
			header('Content-Type: application/json');
			echo json_encode(['ok' => false, 'message' => 'Missing review id']);
			exit;
		}

		$review = getReviewById($review_id);
		if (!$review) {
			header('Content-Type: application/json');
			echo json_encode(['ok' => false, 'message' => 'Not found']);
			exit;
		}

		$user_name = getUserNameByReviewId($review_id);
		header('Content-Type: application/json');
		echo json_encode(['ok' => true, 'review' => $review, 'user_name' => $user_name]);
		exit;
	}
}
