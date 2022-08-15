<?php
require_once MODEL_DIR.'Utility.php';

class User extends Utility {
    public $currentUser;
    protected $responseBody;

    function __construct($db) {
        $this->db = $db;
        $this->table = new stdClass();
        $this->table->userMeta = 'user_meta';
        $this->table->userUpdate = 'user_update';
        $this->table->authorization = 'authorization';
        $this->table->users = 'users';

        if ($this->loggedInUser() !== false) {
            $currentUser = $this->loggedInUser();
            $this->currentUser = $currentUser;
        }
    }

    function getAllUser($roleId = '') {

        if ($roleId == '') {
            $result = $this->db->getAllRecords($this->table->users, "*", "ORDER BY registered_at DESC");
        } else {
            $result = $this->db->getAllRecords($this->table->users, "*", "AND role_id = '$roleId'", "ORDER BY registered_at DESC");
        }

        if (count($result) > 0) {
            foreach ($result as $index => $user) {
                $users[$index] = $this->getUserById($user['id']);
            }
            $this->responseBody = $users;
        }else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }

    function getUserById($userId) {
        $result = $this->db->getSingleRecord($this->table->users, "*", "AND id = '$userId'");

        if ($result != NULL) {

            $result['fullName'] = ucwords($result['firstname'].' '.$result['lastname']);
            $result['initial'] = strtoupper($result['firstname'][0].$result['lastname'][0]);

            $this->responseBody = $this->arrayToObject($result);
        }else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }

    function getUserByUserName($userName) {
        $result = $this->db->getSingleRecord($this->table->users, "*", "AND username = '$userName'");

        if ($result != NULL) {
            $result['fullName'] = ucwords($result['firstname'].' '.$result['lastname']);
            $result['initial'] = strtoupper($result['firstname'][0].$result['lastname'][0]);

            $this->responseBody = $this->arrayToObject($result);
        }else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }


    function getUser($key) {

        $result = $this->db->getSingleRecord($this->table->users, "*", "AND email = '$key' OR mobile = '$key' OR id = '$key' OR staff_id = '$key'");
        
        if ($result != NULL) {
            $result['fullName'] = ucwords($result['firstname'].' '.$result['lastname']);
            $result['initial'] = strtoupper($result['firstname'][0].$result['lastname'][0]);
            
            $this->responseBody = $this->arrayToObject($result);
        }else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }

    function getUserByEmail($email) {
        $result = $this->db->getSingleRecord($this->table->users, "*", "AND email = '$email'");
        
        if ($result != NULL) {
            $this->responseBody =$this->getUser($email);
        }else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }

    function getUserByPhone($phone, $roleId = '') {

        if ($roleId == '') {
            $result = $this->db->getSingleRecord($this->table->users, "*", "AND mobile = '$phone'");
        } else {
            $result = $this->db->getSingleRecord($this->table->users, "*", "AND mobile = '$phone' AND role_id = $roleId");
            
        }

        if ($result != NULL) {
            $this->responseBody = $this->getUserById($result['id']);
        } else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }

    public function loggedInUser()
    {

        if (isset($_SESSION['authUser']) AND !empty($_SESSION['authUser'])) {
            $userId = $_SESSION['authUser'];
        } elseif (isset($_COOKIE["member_login"]) AND !empty($_COOKIE["member_login"])) {
            $userId = $_COOKIE["member_login"];
        } else {
            return false;
        }

        $result = $this->getUserById($userId);
        if ($result !== false) {
            unset($result->password);

            $this->responseBody = $result;
        }else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    public function processLogin($userName, $remeber = false) {
        $result = $this->getUserByUserName($userName);

        if ($result !== false){
            $this->updateUser(['last_login' => date("Y-m-d H:i:s")], $result->id);
            $_SESSION['authUser'] = $result->id;

            if($remeber) {
				setcookie ("member_login", $result->id, time()+ (7 * 60 * 60));
			} else {
				if(isset($_COOKIE["member_login"])) {
					setcookie ("member_login","");
				}
			}
            $this->responseBody = true;
            
        }else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    public function processRegister($userData) {
        $result = $this->db->insert($this->table->users, $userData);

        if ($result) {
            $this->responseBody = true;
        }else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    public function isLoggedIn () {
        if ($this->loggedInUser() !== false) {
            $this->responseBody = true;
        }else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    public function hashPassword($password) {
        $hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

        $this->responseBody = $hash;
        return $this->responseBody;
    }

    public function isSuperAdmin($userId)
    {
        $user = $this->getUserById($userId);

        if ($user !== false AND $user->role == 'SUPERADMIN') {
            $this->responseBody = true;
        }else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    public function isAdim($userId)
    {
        $user = $this->getUserById($userId);

        if ($user !== false AND $user->role == 'ADMIN') {
            $this->responseBody = true;
        }else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    public function isUser($userId)
    {
        $user = $this->getUserById($userId);

        if ($user !== false AND $user->role == 'USER') {
            $this->responseBody = true;
        }else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    public function updateUser($userData, $userId)
    {
        $theUser = $this->getUserById($userId);

        if ($theUser == false) {
            $this->responseBody =  false;
        } else {
            $update = $this->db->update_new($this->table->users, $userData, " AND id = $userId");

            if ($update > 0) {
                $this->responseBody =  true;
            } else {
                $this->responseBody =  false;
            }
        }

        return $this->responseBody;
    }

    public function addUser($userData, $roleId)
    {
        $app = new App($this->db);
        $appInfo = $app->getAppInfo();

        $userData = array(
            'firstname' => $userData['firstName'],
            'lastname' => $userData['lastName'], 
            'email' => $userData['email'],
            'mobile' => $userData['phoneNumber'],
            'staff_id' => isset($userData['staffId']) ? $userData['staffId']:NULL,
            'role_id' => $roleId,
            'password' => $this->hashPassword(isset($userData['password']) ? $userData['password']:$appInfo->default_user_password),
        );

        $this->db->beginTransaction();

        try {
            $this->db->insert($this->table->users, $userData);
            $userId = $this->db->lastInsertId();

            $this->responseBody =  true;
            $this->db->commit();

        } catch (\Throwable $e) {
            $this->db->rollback();
            $this->responseBody =  true;
            echo $e->getMessage();die;
        }
        

        return $this->responseBody;
    }

    public function createUserMeta($userId, $userMetaData)
    {
        $theUser = $this->getUserById($userId);
        
        if ($theUser != false) {    
            $index = 0;        
            foreach ($userMetaData as $key => $value) {
                $userMeta[$index]['key'] = $key;
                $userMeta[$index]['value'] = $value;
                $userMeta[$index]['user_id'] = $userId;
                
                $index++;
            }
            
            $insert = $this->db->multiInsert('user_meta', $userMeta);

            if ($insert > 0) {
                $this->responseBody =  true;
            } else {
                $this->responseBody =  false;
            }
        } else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    
    public function getUserMeta($userId, $metaKey)
    {
        $result = $this->db->getSingleRecord($this->table->userMeta, "value", "AND `user_id` = '$userId' AND `key` = '$metaKey'");

        if ($result != NULL) {
            $this->responseBody = $result['value'];
        } else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    public function deleteSingleUserMeta($userId, $metaKey)
    {
        $theUser = $this->getUserById($userId);

        if ($theUser != false) {
            $delete = $this->db->delete($this->table->userMeta, ["user_id" => $userId, "meta_key" => $metaKey]);

            if ($delete > 0) {
                $this->responseBody =  true;
            } else {
                $this->responseBody =  false;
            }
        } else {
            $this->responseBody = false;
        }

        return $this->responseBody;
    }

    // Authorization
    public function getAuthorizationPages($userId)
    {
        $auth = new Authorization($this->db);

        $result = $this->db->getAllRecords($this->table->authorization, "*", "AND user_id = $userId");

        if (count($result) > 0) {
            foreach ($result as $index => $pageId) {
                $page = $auth->getPage($pageId);
                $pages[$index] = $this->arrayToObject($page);
            }
            $this->responseBody = $pages;
        }else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }
}