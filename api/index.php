<?php
/**
 * Created by PhpStorm.
 * User: fares
 * Date: 3/12/14
 * Time: 7:15 PM
 */
ini_set('display_errors', true);
// load required files
require 'lib/Slim/Slim.php';
include 'db.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/hello/:name','sayHelloTo');
$app->get('/login','login');
$app->get('/testLogin','authenticate','testLogin');
$app->get('/logout','logout');

$app->post('/register','registerNewUser');
$app->post('/addUserInfo/:uid','addUserInfo');
$app->get('/checkdisplayname/:displayName','checkDispayName');

$app->get('/userprofile/:uid','getUserProfile');
//
//// return last user posts ===> add pagination
//$app->get('/userposts/:uid','getUserPosts');
//
//
//// return image + title + text + likes count + 4 first comments (commenter + ctext )
//$app->get('/post/:pid','getPost');
//// image + title + text
//$app->post('/addpost','addPost');
//$app->post('/deletepost/:pid','deletePost');
//
//$app->get('/post/:pid/comments','getPostComments');
//$app->post('/addcomment/:pid','addComment');
//$app->post('/deletecomment/:pid','deleteComment');
//
//$app->get('/post/:pid/likes','getPostLikes');
//$app->post('/addlike/:pid','addLike');
//$app->post('/deletelike/:pid','deleteLike');
//
//
//$app->post('/follow/:uid','follow');
//$app->post('/unfollow/:uid','unfollow');
//
//// return last posts ===> add pagination
//$app->get('/explore','explore');

    
// run
$app->run();

function sayHelloTo($name){
     echo "Hello, $name";
}

# region  Authentication Operations
function login() {
    $app = \Slim\Slim::getInstance();
    try {
        $app->setEncryptedCookie('uid', 'demo', '1 minutes');
        $app->setEncryptedCookie('key', 'demo', '1 minutes');
        echo 'Logged in as demo';

    } catch (Exception $e) {
        $app->response()->status(400);
        $app->response()->header('X-Status-Reason', $e->getMessage());
    }
}

// route middleware for simple API authentication
function authenticate(\Slim\Route $route) {
    $app = \Slim\Slim::getInstance();
    $uid = $app->getEncryptedCookie('uid');
    $key = $app->getEncryptedCookie('key');
    if (validateUserKey($uid, $key) === false) {
        $easyUiResult = array();
        $easyUiResult['success'] = false;
        $easyUiResult['msg'] = "Authorization";
        $app->halt(300, 'Not Authorized...');
    }
    
}

function validateUserKey($uid, $key) {
    // insert your (hopefully more complex) validation routine here
    if ($uid == 'demo' && $key == 'demo') {
        return true;
    } else {
        return false;
    }
}
    
function testLogin() {
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $result = array();
    try {
        $r_userName = $req->get('user_name');
        $r_userPass = $req->get('user_pass');
        if ($r_userName ==="fares"){
            $result['success'] = true;

        }else{
            $result['success'] = false;
        }

    } catch (Exception $e) {
        $app->response()->status(400);
        $app->response()->header('X-Status-Reason', $e->getMessage());
    }
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($result);
}
    
function logout() {
        $app = \Slim\Slim::getInstance();
        try {
            $app->deleteCookie('uid');
            $app->deleteCookie('key');
            echo 'demo Logged out';
            
        } catch (Exception $e) {
            $app->response()->status(400);
            $app->response()->header('X-Status-Reason', $e->getMessage());
        }
    }
# endregion
  
 
    
Function registerNewUser(){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $r_userEmail = $req->post('user_email');
    $r_userPass = $req->post('user_pass');
    //$r_userMobile = $req->post('user_mobile');
    //$r_userAge = $req->post('user_age');
    
    validateEmail($r_userEmail);
    
    $query = "INSERT INTO mz_users(email, password) VALUES(:email, :pass)";
    
    try {
        $dbCon = getConnection();
        //$stmt   = $dbCon->query($query);
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("email", $r_userEmail);
        $stmt->bindParam("pass", $r_userPass);
        $stmt->execute();
        $uid = $dbCon->lastInsertId();
        //$users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo json_encode(array('uid' => $uid));
        //echo '{"users": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }  
}

Function validateEmail($email){
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        errorJson ("Not a valid email");
      
    $sql = "SELECT count(email) FROM mz_users WHERE email LIKE :email limit 1";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("email", $email);
        $stmt->execute();
        $count = $stmt->fetchColumn(); 
        $dbCon = null;
        if ($count>0)
            errorJson ("This Email is already Used");
        
        //echo '{"user": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        errorJson($e->getMessage());
        //echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    return true;
}

Function addUserInfo($uid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    //$r_useruid = $req->post('user_uid');
    $r_userdname = $req->post('user_dname');
    $r_userMobile = $req->post('user_mobile');
    $r_userAge = $req->post('user_age');
    
    if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT))
        errorJson("Somthing Wrong");
    
    checkDispayName($r_userdname);
    
    $query = "INSERT INTO mz_userInfo(uid, displayname, mobile, age) VALUES(:uid, :dname, :mobile, :age)";
    try {
        $dbCon = getConnection();
        //$stmt   = $dbCon->query($query);
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid);
        $stmt->bindParam("dname", $r_userdname);
        $stmt->bindParam("mobile", $r_userMobile);
        $stmt->bindParam("age", $r_userAge);
        $stmt->execute();
        //$uid = $dbCon->lastInsertId();
        //$users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo json_encode(array('uid' => $uid));
        //echo '{"users": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }  
}

Function checkDispayName($dName){
    //$login = query("SELECT displayname FROM mz_userInfo WHERE displayname='%s' limit 1", $displayName);
    if ($dName == null)
        errorJson ("Please pick display name");
            
    $sql = "SELECT displayname FROM mz_userInfo WHERE displayname LIKE :dname limit 1";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("dname", $dname);
        $stmt->execute();
        $count = $stmt->fetchColumn(); 
        $dbCon = null;
        if ($count>0)
            errorJson ("This name is already Used");
        
        //echo '{"user": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        errorJson($e->getMessage());
        //echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    return true;
    
    
    
}

function getUserProfile($uid){
    // TODO: will add more followers + following + picture
    
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT))
        errorJson("Somthing Wrong in user id (uid)");
    
    
    $query = "SELECT mz_users.uid, email, displayname, mobile, age "
            . " FROM mz_users, mz_userInfo"
            . " WHERE mz_userInfo.uid = :uid "
            . " AND mz_users.uid = mz_userInfo.uid";
    
    $query_following = "SELECT count(*) followers FROM mz_following WHERE uid = :uid";
    $query_followers = "SELECT count(*) followers FROM mz_following WHERE f_uid = :uid";
    $query_postsCount = "SELECT count(*) followers FROM mz_posts WHERE uid = :uid";
    
    try {
        $dbCon = getConnection();
        //$stmt   = $dbCon->query($query);
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid);
        $stmt->execute();
        $userInfo = $stmt->fetchObject(); 
        //$uid = $dbCon->lastInsertId();
        //$users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo json_encode(array('UserInfo' => $userInfo));
        //echo '{"users": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/***
 * to check that uid is integer and exist in users table and not in userInfo table 
 */
function checkuid($uid){
    
}

function errorJson($msg){
    echo json_encode(array('error'=>$msg));
    
    exit();
}

function getConnection() {
    try {
        $db_username = "root";
        $db_password = "root";
        $conn = new PDO('mysql:host=localhost;port=8889;dbname=mzayan', $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    return $conn;
}