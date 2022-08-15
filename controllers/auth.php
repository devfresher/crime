<?php
require_once '../includes/config.php';
require_once CLASS_DIR.'Validator.php';
require_once MODEL_DIR.'User.php';
require_once MODEL_DIR.'Authorization.php';    

if (isset($_POST["login"])) {
    $myFilters = [
        'username' => [
          'sanitizations' => 'string',
          'validations' => 'required',
        ],
        'password' => [
          'sanitization' => 'string',
          'validation' => 'required',
        ]
    ];

    $validator = new Validator($myFilters);
    $validationResults = $validator->run($_POST);
      
    if ($validationResults === FALSE) {
        $validationErrors = $validator->getValidationErrors();
        foreach($validationErrors as $index => $error){
            if ($index == array_key_last($validationErrors)) {
                $utility->addMessage($error, 'error', REFERER);
            } else {
                $utility->addMessage($error, 'error');
            }
        }
    } else {
        $sanitizedInputData = $validationResults;
        $userName = $sanitizedInputData['username'];
        $password = $sanitizedInputData['password'];

        $rememberMe = isset($_POST['rememberMe']) ? true:false;

        $userResult = $user->getUserByUsername($userName);
        $hashPassword = $userResult->password;
        unset($userResult->password);

        if($userResult) {
            if (password_verify($password, $hashPassword)) {
                if ($userResult->status == 0) {
                    $utility->addMessage($clientLang->account_not_active, 'error', REFERER);
                } else {
                    $loggedIn = $user->processLogin($userName, $rememberMe);

                    if (!$loggedIn) {
                        $utility->addMessage($clientLang->unexpected_error, 'success', REFERER);
                    } else {
                        $utility->addMessage($clientLang->login_success, 'success');
                        if (isset($_POST['to'])) {
                            $utility->doRedirect($_POST['to']);
                            exit();
                        } else {
                            $utility->doRedirect(BASE_URL);
                            exit();
                        }

                    } 
                }
            } else {
                $utility->addMessage($clientLang->invalid_credentials, 'error', REFERER);
            }
        } else {
            $utility->addMessage($clientLang->invalid_credentials, 'error', REFERER);
        }
    }
}

elseif (isset($_POST["authorize"])) {
    $myFilters = [
        'password' => [
          'sanitization' => 'string',
          'validation' => 'required',
        ]
    ];

    $validator = new Validator($myFilters);
    $validationResults = $validator->run($_POST);
      
    if ($validationResults === FALSE) {
        $validationErrors = $validator->getValidationErrors();
        foreach($validationErrors as $error){
            $flash->error($error);
        }
        header("Location: ".$_POST['form_url']);
        exit();
    } else {
        $sanitizedInputData = $validationResults;
        $password = $sanitizedInputData['password'];
        
        $userResult = $staff->getStaffById($user->currentUser->id);
        $hashPassword = $userResult->password;
        // unset($userResult->password);
        
        if (password_verify($password, $hashPassword)) {
            if ($userResult->status == 0) {
                $flash->error($clientLang->account_not_active);
                header("Location: ".$_POST['form_url']);
                exit();
            } else {
                $_SESSION['authorizedPage'] = $_POST['page_slug'];
                header("Location: ".$_POST['form_url']);
                exit();
            }
        } else {
            $flash->error($clientLang->invalid_credentials);
            $_SESSION['formErrorMessage'] = $clientLang->invalid_credentials;
            header("Location: ".$_POST['form_url']);
            exit();
        }
    }
}

elseif (isset($_POST["addNew"])) {
    $myFilters = [
        'firstName' => [
          'sanitizations' => 'string',
          'validations' => 'required|personname',
        ],
        'lastName' => [
          'sanitization' => 'string',
          'validation' => 'required|personName',
        ],
        'phoneNumber' => [
          'sanitization' => 'string',
          'validation' => 'required|phonenumber',
        ],
        'role' => [
          'sanitization' => 'string',
          'validation' => 'required',
        ],
        'email' => [
          'sanitization' => 'string',
          'validation' => 'required|email',
        ],
        'password' => [
          'sanitization' => 'string',
        ]
    ];

    $validator = new Validator($myFilters);
    $validationResults = $validator->run($_POST);
      
    if ($validationResults === FALSE) {
        $validationErrors = $validator->getValidationErrors();
        foreach($validationErrors as $error){
            $flash->error($error);
        }
        $_SESSION['formInput'] = $_POST;
        header("Location: ".$_POST['form_url']);
        exit();
    } else {
        $sanitizedInputData = $validationResults;
        // print_r($sanitizedInputData);die;

        if ($user->getUserByPhone($sanitizedInputData['phoneNumber']) == false) {
            $hashPassword = $sanitizedInputData['password'];

            $staffData = array(
                'firstName' => $sanitizedInputData['firstName'], 
                'staffId' => $staff->generateStaffId(),
                'lastName' => $sanitizedInputData['lastName'], 
                'email' => $sanitizedInputData['email'],
                'phoneNumber' => $sanitizedInputData['phoneNumber'],
                'password' => $hashPassword,
            );

            $result = $staff->addStaff($staffData, $sanitizedInputData['role']);

            if($result) {
                $flash->success($clientLang->created);
                header("Location: ".$_POST['form_url']);
                exit();
            } else {
                $flash->error($clientLang->request_failed);
                $_SESSION['formInput'] = $_POST;
                header("Location: ".$_POST['form_url']);
                exit();
            }
        } else {
            $flash->error($clientLang->item_exist);
            $_SESSION['formErrorMessage'] = $clientLang->item_exist;
            $_SESSION['formInput'] = $_POST;
            header("Location: ".$_POST['form_url']);
            exit();
        }
    }
}

elseif (isset($_POST['assignAuthorizedPages'])) {
    $myFilters = [
        'staffId' => [
          'sanitizations' => 'string',
          'validations' => 'required',
        ],
        'role' => [
          'sanitization' => 'string',
          'validation' => 'required',
        ]
    ];

    $validator = new Validator($myFilters);
    $validationResults = $validator->run($_POST);
      
    if ($validationResults === FALSE) {
        $validationErrors = $validator->getValidationErrors();
        foreach($validationErrors as $error){
            $_SESSION['formErrorMessage'][] = $error;
        }
        header("Location: ".$_POST['form_url']);
        exit();

    } else {
        $sanitizedInputData = $validationResults;
        $staffId = $sanitizedInputData['staffId'];
        
        $theStaff = $staff->getStaffById($staffId);

        if ($theStaff != FALSE) {
            $auth = new Authorization($db);

            $authorizedPages = $auth->getAllPermittedPages($staffId);
            $authorizingPages = $_POST['authorizingPages'];
            
            $check = true;
            try {
                // Add newly athorized pages
                foreach ($authorizingPages as $index => $page) {
                    if (!in_array($page, $authorizedPages) OR empty($authorizedPages)) {
                        $grant = $auth->grantPermission($staffId, $page);
                        $check = $grant;
                    }
                }

                // removed pages
                if ($authorizedPages != false) {
                    foreach ($authorizedPages as $index => $page) {
                        if (!in_array($page, $authorizingPages) OR empty($authorizingPages)) {
                            $revoke = $auth->revokePermission($staffId, $page);
                            $check = $revoke;
                        }
                    }
                }

                if ($theStaff->role_id != $sanitizedInputData['role']) {
                    $updateRole = $user->updateUser(['role_id' => $sanitizedInputData['role']], $staffId);
                    $check = $updateRole;
                }
                
            } catch (\Throwable $e) {
                $check = false;
                // echo $e->getMessage();die;
            }

            if ($check === false) {
                $_SESSION['formErrorMessage'] = $clientLang->request_failed;
                header("Location: ".$_POST['form_url']);
                exit();

            } else {

                $_SESSION['formSuccessMessage'] = $clientLang->saved;
                header("Location: ".$_POST['form_url']);
                exit();
            }
        } else {
            $_SESSION['formErrorMessage'] = $clientLang->user_not_found;
            header("Location: ".$_POST['form_url']);
            exit();
        }
    }
}

elseif (isset($_POST['updateStaffInfo'])) {
    $myFilters = [
        'id' => [
            'sanitizations' => 'string',
            'validations' => 'required',
        ],
        'staffId' => [
			'sanitizations' => 'string',
			'validations' => 'required',
        ],
        'firstName' => [
			'sanitization' => 'string',
			'validation' => 'required',
        ],
        'lastName' => [
			'sanitization' => 'string',
			'validation' => 'required',
        ]
    ];

    $validator = new Validator($myFilters);
    $validationResults = $validator->run($_POST);
      
    if ($validationResults === FALSE) {
        $validationErrors = $validator->getValidationErrors();
        foreach($validationErrors as $error){
            $_SESSION['formErrorMessage'][] = $error;
        }
        header("Location: ".$_POST['form_url']);
        exit();

    } else {
        $sanitizedInputData = $validationResults;
        $userId = $sanitizedInputData['id'];
        
        $theStaff = $staff->getStaffById($userId);

        if ($theStaff != FALSE) {
			$existingStaff = $staff->getStaffById($sanitizedInputData['staffId']);
			if ($existingStaff != FALSE AND $theStaff->staff_id != $sanitizedInputData['staffId']) {
				$_SESSION['formErrorMessage'] = $clientLang->staff_id_exist;
				header("Location: ".$_POST['form_url']);
				exit();
			} else {
				try {
					$userData = array(
						'fname' => $sanitizedInputData['firstName'],
						'lname' => $sanitizedInputData['lastName'],
						'staff_id' => $sanitizedInputData['staffId'],
					);
	
					$update = $staff->updateUser($userData, $userId);
					if ($update) {
						$_SESSION['formSuccessMessage'] = $clientLang->updated;
						header("Location: ".$_POST['form_url']);
						exit();
					} else {
						$_SESSION['formErrorMessage'] = $clientLang->request_failed;
						header("Location: ".$_POST['form_url']);
						exit();
					}
				} catch (\Throwable $e) {
					return false;
					// echo $e->getMessage();die;
				}
			}

        } else {
            $_SESSION['formErrorMessage'] = $clientLang->user_not_found;
            header("Location: ".$_POST['form_url']);
            exit();
        }
    }
}

elseif (isset($_REQUEST['updateSuperAdminPages'])) {
    $superAdmins = $staff->getAllStaffs(1);
    try {
        foreach ($superAdmins as $admin) {
            $auth = new Authorization($db);

            $authorizedPages = $auth->getAllPermittedPages($admin->id);
            $allAdminPages = $auth->getAllPages($pageType = 'admin_page');

            foreach ($allAdminPages as $index => $adminPage) {
                if (!in_array($adminPage['id'], $authorizedPages) OR empty($authorizedPages)) {
                    $grant = $auth->grantPermission($admin->id, $adminPage['id']);
                    $check = $grant;
                }
            }
        }

        echo "Authorization Updated";
    } catch (\Throwable $e) {
        echo $e->getMessage();
    }
}

elseif (isset($_REQUEST['updateStaffStatus'])) {
    $myFilters = [
        'staffId' => [
          'sanitization' => 'number',
          'validation' => 'required',
        ],
        'action' => [
            'sanitization' => 'string',
            'validation' => 'required',
        ]
    ];

    $validator = new Validator($myFilters);
    $validationResults = $validator->run($_POST);
      
    if ($validationResults === FALSE) {
        $validationErrors = $validator->getValidationErrors();
        foreach($validationErrors as $error){
            $_SESSION['formErrorMessage'][] = $error;
        }
        $_SESSION['formInput'] = $_POST;
        header("Location: ".$_POST['form_url']);
        exit();
    } else {
        $sanitizedInputData = $validationResults;
        
        switch ($sanitizedInputData['action']) {
            case 'activate':
                $updateData = array(
                    'status' => 1
                );
                $msg = "Account activated";
            break;

            case 'deactivate':
                $updateData = array(
                    'status' => 0
                );
                $msg = "Account deactivated";
            break;

            default:
                $updateData = array(
                    'status' => 0
                );
                $msg = "Account deactivated";
            break;
        }
        
        $updateStaff= $staff->updateUser($updateData, $sanitizedInputData['staffId']);

        if($updateStaff) {
            $response['msg'] = $msg;
			$response['status'] = $updateData['status'];
        } else {
            $response['error'][] = $clientLang->request_failed;
        } 
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_PRETTY_PRINT);
}

elseif (isset($_REQUEST['updatePassword'])) {
    $myFilters = [
        'staffId' => [
          'sanitization' => 'number',
          'validation' => 'required',
        ],
        'oldPassword' => [
            'sanitization' => 'string',
            'validation' => 'required',
        ],
        'newPassword' => [
            'sanitization' => 'string',
            'validation' => 'required',
        ]
    ];

    $validator = new Validator($myFilters);
    $validationResults = $validator->run($_POST);
      
    if ($validationResults === FALSE) {
        $validationErrors = $validator->getValidationErrors();
        foreach($validationErrors as $error){
            $_SESSION['formErrorMessage'][] = $error;
        }
        header("Location: ".$_POST['form_url']);
        exit();
    } else {
        $sanitizedInputData = $validationResults;
        
        if ($sanitizedInputData['oldPassword'] != $sanitizedInputData['newPassword']) {
            $theStaff = $staff->getStaffById($sanitizedInputData['staffId']);

            $newHashPassword = $user->hashPassword($sanitizedInputData['newPassword']);
            $oldHashPassword = $user->hashPassword($sanitizedInputData['oldPassword']);

            if (password_verify($sanitizedInputData['oldPassword'], $theStaff->password)) {
                $updateData = array(
                    'password' => $newHashPassword
                );
    
                $updateStaff = $staff->updateUser($updateData, $sanitizedInputData['staffId']);
    
                if($updateStaff) {
                    $_SESSION['formSuccessMessage'] = $clientLang->updated;
                    header("Location: ".$_POST['form_url']);
                    exit();
                } else {
                    $_SESSION['formErrorMessage'] = $clientLang->request_failed;
                    header("Location: ".$_POST['form_url']);
                    exit();
                }

            } else {
                $_SESSION['formErrorMessage'][]  = "Invalid old password";
                header("Location: ".$_POST['form_url']);
                exit();
            }
        }  
        else {
            $_SESSION['formErrorMessage'][]  = "New password is same as old password";
            header("Location: ".$_POST['form_url']);
            exit();
        }
    }
}

else if(isset($_POST['deleteStaff'])) {
    extract($_POST);
    $deleteStaff = $staff->deleteStaff(['id' => $staffId]);
    if($deleteStaff) {
        echo "deleted";
        exit();
    } else {
        echo "error";
        exit();
    }
}

else {
    header("Location: ".BASE_URL.'logout');
}
?>