<?php
function setFlashMessage($type, $message) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function displayFlashMessage() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!empty($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        echo '<div class="alert alert-' . htmlspecialchars($message['type']) . '">' . 
             htmlspecialchars($message['message']) . 
             '</div>';
        unset($_SESSION['flash_message']);
    }
}
?>