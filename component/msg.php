<?php
function confirmAction($message, $actionUrl, $buttonLabel = "Confirm")
{
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmed'])) {
        return true;
    }

    // Display the form
    echo <<<HTML
    <form method="POST" action="$actionUrl" onsubmit="return confirm('$message');">
        <input type="hidden" name="confirmed" value="1">
        <button type="submit">$buttonLabel</button>
    </form>
    HTML;

    return false; // Not confirmed yet
}
