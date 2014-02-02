<?php

if (isset($_GET['error'])) {
    echo '<p class="error">Error Logging In!</p>';
    echo "<b>Game Over</b>";
}
if (isset($_GET['success'])) {
    echo '<p class="success">you have been delogged</p>';
    echo "<b>Game Over</b>";
}
?>