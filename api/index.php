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
$app->post('/addDname/:uid','addDisplayName');
$app->post('/addUserInfo/:uid','addUserInfo');
$app->post('/checkdisplayname','checkDispayName');

$app->get('/user/:uid/profile','getUserProfile');
$app->get('/user/:uid/followers','getFollowers');
$app->get('/user/:uid/followings','getFollowings');
$app->get('/user/:uid/posts','getUserPosts');

// TODO this must be added to get User Profile
//$app->get('/user/:uid/isfolloweduser','isFollowedUser');

// return thumbimage + title + text + (likes,Comments, Pidders) count + Highest Pid
$app->get('/posts','getPosts');
$app->get('/post/:pid/likers','getLikers');
$app->get('/post/:pid/comments','getComments');

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
  
 
// POST /register
Function registerNewUser(){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $r_userEmail = $req->post('user_email');
    $r_userPass = $req->post('user_pass');
    
    if (isEmailUsed($r_userEmail))
        errorJson ("This Email is already Used");
    
    validatePassword($r_userPass);
    
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
        $query = "INSERT INTO mz_userInfo(uid, displayname) VALUES(:uid, 'Not Set')";
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid);
        $
        $dbCon->
        $dbCon = null;
        echo json_encode(array('last Inserted' => $uid));
        //echo '{"users": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }  
}

// POST /addDname/:uid
Function addDisplayName($uid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $dName = $req->post('user_dname');
    
    if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT))
        errorJson("Somthing Wrong");
    
    if(isDisplayNameUsed($dName))
        errorJson ($dName." Already Used");
    
//    if(isUserInfoAdded($uid))
//        checkDispayName($dName);
//    else
//        errorJson ("User Dispay Name Alreay Added");
    
    $query = "INSERT INTO mz_userInfo(uid, displayname) VALUES(:uid, :dname)";
    try {
        $dbCon = getConnection();
        //$stmt   = $dbCon->query($query);
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid);
        $stmt->bindParam("dname", $dName);
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

// POST /addUserInfo/:uid 
Function addUserInfo($uid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $uAvatar = $req->post('user_avatar');
    $uMobile = $req->post('user_mobile');
    $uAge = $req->post('user_age');
    $uStatus = $req->post('user_status');
    
    if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT))
        errorJson("Somthing Wrong");
    
    //checkDispayName($r_userdname);
    //
    $s = ($uStatus == null  || $uStatus == "" )? "" : " ustatus = :ustatus";
    $m = ($uMobile == null  || $uMobile == "" )? "" : " mobile = :mobile";
    $a = ($uAge == null     || $uAge == "" )?    "" : " age = :age";
    $v = ($uAvatar == null  || $uAvatar == "" )? "" : " uavatar = :uavatar";

    $setparam = array($s, $m, $a, $v);
    $setString = addparam($setparam, 0);
    
    $query = "UPDATE mz_userInfo SET ".$setString." WHERE uid = :uid";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid);
        ($uStatus == "")?   :$stmt->bindParam("ustatus", $uStatus);
        ($uMobile == "")?   :$stmt->bindParam("mobile", $uMobile);
        ($uAge == "")?      :$stmt->bindParam("age", $uAge);
        ($uAvatar == "")?   :$stmt->bindParam("uavatar", $uAvatar);
        $stmt->execute();
        
        $dbCon = null;
        echo json_encode(array('uid' => $uid));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }  
}

Function validatePassword($pass){
    if ($pass == null || $pass == "")
        errorJson ("Please choose Password");
}

/***
 * TODO: change this to be decreament
 */
function addparam($param, $i){
    $t = "";
    if($i < 4)
    {
        if($param[$i] == "")
            $t = addparam ($param, $i+1);
        else{
            $t = $param[$i];
            $n = addparam($param, $i+1);
            if ($n != "")
                $t .= ", " . $n;  
        }
    }
    return $t;
}

Function isEmailUsed($email){

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
            return true;
            //errorJson ("This Email is already Used");
        
        //echo '{"user": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        errorJson($e->getMessage());
        //echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    return false;
}

Function isDisplayNameUsed($dName){
    if ($dName == null || $dName == "")
        errorJson ("Display Name Cannot be empty");
    
    $sql = "SELECT count(displayname) FROM mz_userInfo WHERE displayname LIKE :dname limit 1";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("dname", $dName);
        $stmt->execute();
        $count = $stmt->fetchColumn(); 
        $dbCon = null;
        if ($count>0)
            return true;
    } catch(PDOException $e) {
        errorJson($e->getMessage());
    }
    return false;
}

Function isUserInfoAdded($uid){
    $sql = "SELECT count(uid) FROM mz_userInfo WHERE uid = :uid";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("uid", $uid);
        $stmt->execute();
        $count = $stmt->fetchColumn(); 
        $dbCon = null;
        if ($count>0)
            return true;
            //errorJson ("The User Information (Display Name) is already Added");
        
        //echo '{"user": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        errorJson($e->getMessage());
        //echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    return false;
}

// POST /checkdisplayname     TODO: review?
Function checkDispayName(){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $dName = $req->post('user_dname');
    
    if(isDisplayNameUsed($dName))
        errorJson ($dName. " is Already Used");
    
    echo json_encode(array('Success'=>true));
}

// GET /user/:uid/profile
function getUserProfile($uid){
    //$app = \Slim\Slim::getInstance();
    //$req = $app->request();
    if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT)){
        errorJson("Somthing Wrong in user id (uid)");
    }
    
    $query = "
        SELECT u.uid, i.uavatar, i.displayname, i.ustatus , i.mobile, age, count(f.f_uid) as following, count(distinct p.pid) as posts, count(distinct ff.uid) as followers
	FROM mz_users u 
                left join mz_userInfo i on u.uid = i.uid
                left join mz_following f  on u.uid = f.uid
                left join mz_following ff on u.uid = ff.f_uid
                left join mz_post p on u.uid = p.uid 
	WHERE u.uid = :uid";
        
    
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid);
        $stmt->execute();
        $userInfo = $stmt->fetchObject(); 
        //$uid = $dbCon->lastInsertId();
        //$users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo json_encode(array('UserInfo' => $userInfo));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// GET /user/:uid/posts
function getUserPosts($uid){
    json_encode("getUserPosts Not Implemented Yet ".$uid);
}

// GET /user/:uid/followers
function getFollowers($uid){
    
    json_encode("getFollowers Not Implemented Yet ".$uid);
}

// GET /user/:uid/followings
function getFollowings($uid){
    json_encode("getFollowings Not Implemented Yet ".$uid);
}

// GET /posts
Function getPosts(){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $pageNumber = $req->get('page_number');
    $postPerPage = 21;
    $offsit = $postPerPage * $pageNumber;
    
    $query = "SELECT p.pid, p.ptitle, p.pimage, p.pdesc,  p.forsale, p.pdate,
		p.uid, ui.displayname, 
                cl.cname, a.aname, s.sname, cty.cname, 
		count(distinct c.cid) as ccount, 
                count(distinct l.lid) as lcount, 
                max( c.price ) as mprice
              FROM mz_post p	left join mz_userInfo ui on p.uid = ui.uid
				left join mz_post_comment c on p.pid = c.pid
				left join mz_post_like l on p.pid = l.pid
				left join mz_section s on p.psection = s.sid
                left join mz_color cl on p.pcolor = cl.cid
                left join mz_age a on p.page = a.aid
                left join mz_city cty on p.pcity = cty.cid
                
              GROUP BY p.pid
              LIMIT :offsit, :pcount ";
    
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query);
        $stmt->bindParam("offsit", $offsit, PDO::PARAM_INT);
        $stmt->bindParam("pcount", $postPerPage, PDO::PARAM_INT);
        $stmt->execute();
        $posts  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"posts": ' . json_encode($posts) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
}

// GET /post/:pid/likers
Function getLikers($pid){
    echo json_encode("getLikers Not Implemented Yet ".$pid);
}

// GET /post/:pid/comments
Function getComments($pid){
    echo json_encode("getCommentss Not Implemented Yet ".$pid);
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

function successJson($msg){
    echo json_encode($msg);
}

function getConnection() {
    try {
        $db_username = "root";
        $db_password = "root";
        $conn = new PDO('mysql:host=127.0.0.1;port=8889;dbname=mzayan', $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    return $conn;
}