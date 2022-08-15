<?php
require_once MODEL_DIR.'Authorization.php';

$auth = new Authorization($db);

if (PAGE_NAME == 'logout') {
    if ($user->isLoggedIn() === false) {
        $referer = (REFERER == '' OR REFERER == NULL) ? BASE_URL : REFERER;
        $utility->doRedirect($referer);
    }
} else {
    $pageInfo = $auth->getPage(PAGE_NAME);
    // print_r($pageInfo->type);
    // print_r($user->currentUser);

    
    if ($pageInfo->type == 'admin_page') {
        if ($user->isLoggedIn()) {
            if($user->isSuperAdmin($user->currentUser->id) == false){
                $utility->doRedirect();
            }
        } else {
            $utility->doRedirect();
        }
    } 
    
    elseif ($pageInfo->type == 'user_page') {
        if ($user->isLoggedIn() == false) {
            $utility->doRedirect();
        }
    }
    
    elseif ($pageInfo->type == 'auth_page') {
        if ($user->isLoggedIn() === true) {
            $utility->doRedirect();
        }
    }
}