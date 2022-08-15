<?php
    require_once './includes/config.php';
    require_once INCLUDES_DIR.'session_check.php';

    session_destroy();
    if (isset($_COOKIE['member_login'])) {
        unset($_COOKIE['member_login']); 
        setcookie('member_login', null);
    }
    $utility->doRedirect(BASE_URL);
?>