<?php

return [
    // 'test' or 'live'
    'mode' => 'live',

    // For live mode: starts with sk_live_
    // For test mode: starts with sk_test_
    'secret_key' => 'sk_test_51SeFjZJ0NvBCcCJetZZw02Tf4Hjz1ZUMyT9S1hMPiENlEBR5ZBT7F3b9l6ylskvJ2lED5qpp9nPVxXVeqOwwhZvV00IxMEImWN',

    // Webhook signing secret (optional but recommended for production)
    // Starts with whsec_
    'webhook_secret' => 'PASTE_YOUR_STRIPE_WEBHOOK_SECRET_HERE',
];
