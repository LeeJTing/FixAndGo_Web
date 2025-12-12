<?php
function showMessage($type, $message)
{
    $class = '';
    $icon  = '';

    switch ($type) {
        case 'success':
            $class = 'msg-success';
            $icon  = '✔️';
            break;
        case 'warning':
            $class = 'msg-warning';
            $icon  = '⚠️';
            break;
        case 'error':
            $class = 'msg-error';
            $icon  = '❌';
            break;
        default:
            $class = 'msg-info';
            $icon  = 'ℹ️';
            break;
    }

    echo <<<HTML
    <div class="message {$class}">
        <span class="icon">{$icon}</span>
        <span class="text">{$message}</span>
    </div>
    HTML;
}

function displayFlashMessage()
{
    if (!empty($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'] ?? 'info';
        $text = $_SESSION['flash_message']['text'] ?? '';

        showMessage(htmlspecialchars($type),  htmlspecialchars($text));
        // Remove message after displaying
        unset($_SESSION['flash_message']);
    }
}
