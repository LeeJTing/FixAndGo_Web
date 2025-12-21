<?php

function getAvailableLoyaltyPointsDao(string $user_id): int
{
    global $_db;

    $stmt = $_db->prepare("
        SELECT COALESCE(SUM(royalty_point), 0) AS points
        FROM loyaltypoint
        WHERE user_id = ?
          AND (expired_at IS NULL OR expired_at > NOW())
    ");
    $stmt->execute([$user_id]);
    $points = (int)$stmt->fetchColumn();
    return max(0, $points);
}

function addLoyaltyPointsDao(string $user_id, int $points, ?string $expired_at_sql = null): bool
{
    global $_db;

    if ($points === 0) return true;

    $expiredAt = $expired_at_sql;
    if ($expiredAt === null) {
        $expiredAt = date('Y-m-d H:i:s', strtotime('+1 year'));
    }

    $stmt = $_db->prepare("INSERT INTO loyaltypoint (user_id, royalty_point, expired_at) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $points, $expiredAt]);
}

function spendLoyaltyPointsDao(string $user_id, int $points): bool
{
    global $_db;

    if ($points <= 0) return true;

    $stmt = $_db->prepare("INSERT INTO loyaltypoint (user_id, royalty_point, expired_at) VALUES (?, ?, NULL)");
    return $stmt->execute([$user_id, -$points]);
}
