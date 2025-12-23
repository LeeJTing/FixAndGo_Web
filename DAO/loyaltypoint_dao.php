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

    $sql = "INSERT INTO loyaltypoint (user_id, get_at, royalty_point, expired_at) VALUES (?, ?, ?, ?)";
    $stmt = $_db->prepare($sql);

    $baseTs = time();
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $getAt = date('Y-m-d H:i:s', $baseTs + $attempt);
        try {
            return $stmt->execute([$user_id, $getAt, $points, $expiredAt]);
        } catch (PDOException $e) {
            // 23000 / 1062 => duplicate entry
            $errorInfo = $e->errorInfo ?? null;
            $sqlState = is_array($errorInfo) ? ($errorInfo[0] ?? '') : '';
            $driverCode = is_array($errorInfo) ? (int)($errorInfo[1] ?? 0) : 0;
            if ($sqlState === '23000' && $driverCode === 1062) {
                continue;
            }
            throw $e;
        }
    }

    return false;
}

function spendLoyaltyPointsDao(string $user_id, int $points): bool
{
    global $_db;

    if ($points <= 0) return true;

    // Same duplicate-PK protection 
    $sql = "INSERT INTO loyaltypoint (user_id, get_at, royalty_point, expired_at) VALUES (?, ?, ?, NULL)";
    $stmt = $_db->prepare($sql);

    $baseTs = time();
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $getAt = date('Y-m-d H:i:s', $baseTs + $attempt);
        try {
            return $stmt->execute([$user_id, $getAt, -$points]);
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo ?? null;
            $sqlState = is_array($errorInfo) ? ($errorInfo[0] ?? '') : '';
            $driverCode = is_array($errorInfo) ? (int)($errorInfo[1] ?? 0) : 0;
            if ($sqlState === '23000' && $driverCode === 1062) {
                continue;
            }
            throw $e;
        }
    }

    return false;
}
