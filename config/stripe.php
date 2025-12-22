<?php

declare(strict_types=1);

/**
 * Stripe configuration loader.
 *
 * Priority:
 *  1) config/stripe.local.php (returns array)
 *  2) Environment variables
 *
 * Supported env vars:
 *  - STRIPE_MODE: 'test' (default) or 'live'
 *  - STRIPE_SECRET_KEY_TEST / STRIPE_SECRET_KEY_LIVE (recommended)
 *  - STRIPE_WEBHOOK_SECRET_TEST / STRIPE_WEBHOOK_SECRET_LIVE (recommended)
 *  - STRIPE_SECRET_KEY / STRIPE_WEBHOOK_SECRET (fallback)
 */
function stripe_config(): array
{
    $localPath = __DIR__ . '/stripe.local.php';
    if (is_file($localPath)) {
        $local = require $localPath;
        if (!is_array($local)) {
            throw new RuntimeException('config/stripe.local.php must return an array.');
        }
        return stripe_normalize_config($local);
    }

    $mode = getenv('STRIPE_MODE');
    $mode = is_string($mode) ? strtolower(trim($mode)) : '';
    if ($mode !== 'live' && $mode !== 'test') {
        $mode = 'test';
    }

    $secret = getenv($mode === 'live' ? 'STRIPE_SECRET_KEY_LIVE' : 'STRIPE_SECRET_KEY_TEST');
    $secret = (is_string($secret) && trim($secret) !== '') ? trim($secret) : '';

    if ($secret === '') {
        $fallback = getenv('STRIPE_SECRET_KEY');
        $secret = (is_string($fallback) && trim($fallback) !== '') ? trim($fallback) : '';
    }

    $webhookSecret = getenv($mode === 'live' ? 'STRIPE_WEBHOOK_SECRET_LIVE' : 'STRIPE_WEBHOOK_SECRET_TEST');
    $webhookSecret = (is_string($webhookSecret) && trim($webhookSecret) !== '') ? trim($webhookSecret) : '';

    if ($webhookSecret === '') {
        $fallback = getenv('STRIPE_WEBHOOK_SECRET');
        $webhookSecret = (is_string($fallback) && trim($fallback) !== '') ? trim($fallback) : '';
    }

    return stripe_normalize_config([
        'mode' => $mode,
        'secret_key' => $secret,
        'webhook_secret' => $webhookSecret,
    ]);
}

/**
 * Returns the webhook signing secret for the current mode.
 *
 * This does NOT require the Stripe secret API key to be configured.
 */
function stripe_webhook_secret(): string
{
    $localPath = __DIR__ . '/stripe.local.php';
    if (is_file($localPath)) {
        $local = require $localPath;
        if (is_array($local) && isset($local['webhook_secret'])) {
            return trim((string)$local['webhook_secret']);
        }
    }

    $mode = getenv('STRIPE_MODE');
    $mode = is_string($mode) ? strtolower(trim($mode)) : '';
    if ($mode !== 'live' && $mode !== 'test') {
        $mode = 'test';
    }

    $secret = getenv($mode === 'live' ? 'STRIPE_WEBHOOK_SECRET_LIVE' : 'STRIPE_WEBHOOK_SECRET_TEST');
    $secret = (is_string($secret) && trim($secret) !== '') ? trim($secret) : '';

    if ($secret === '') {
        $fallback = getenv('STRIPE_WEBHOOK_SECRET');
        $secret = (is_string($fallback) && trim($fallback) !== '') ? trim($fallback) : '';
    }

    return $secret;
}

function stripe_normalize_config(array $cfg): array
{
    $mode = isset($cfg['mode']) ? strtolower(trim((string)$cfg['mode'])) : 'test';
    if ($mode !== 'live' && $mode !== 'test') {
        $mode = 'test';
    }

    $secretKey = isset($cfg['secret_key']) ? trim((string)$cfg['secret_key']) : '';
    if ($secretKey === '') {
        throw new RuntimeException('Stripe secret key not configured. Set STRIPE_SECRET_KEY_* or create config/stripe.local.php.');
    }

    $expectedPrefix = $mode === 'live' ? 'sk_live_' : 'sk_test_';
    if (!str_starts_with($secretKey, $expectedPrefix)) {
        $providedPrefix = 'unknown';
        if (str_starts_with($secretKey, 'sk_test_')) {
            $providedPrefix = 'sk_test_';
        } elseif (str_starts_with($secretKey, 'sk_live_')) {
            $providedPrefix = 'sk_live_';
        } elseif (str_starts_with($secretKey, 'rk_test_')) {
            $providedPrefix = 'rk_test_';
        } elseif (str_starts_with($secretKey, 'rk_live_')) {
            $providedPrefix = 'rk_live_';
        }

        throw new RuntimeException(
            "Stripe key/mode mismatch: mode={$mode} expects {$expectedPrefix}..., but you provided {$providedPrefix}. "
                . 'Set mode=test with sk_test_... OR mode=live with sk_live_...'
        );
    }

    $webhookSecret = isset($cfg['webhook_secret']) ? trim((string)$cfg['webhook_secret']) : '';

    return [
        'mode' => $mode,
        'secret_key' => $secretKey,
        'webhook_secret' => $webhookSecret,
    ];
}
