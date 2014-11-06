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

$app->post('/addpost','addNewPost');
$app->post('/post/:pid/comment','addComment');
$app->post('/post/:pid/like','likePost');
$app->post('/post/:pid/unlike','unLikePost');

$app->post('/user/:fuid/follow','followUser');
$app->post('/user/:fuid/unfollow','unFollowUser');


$app->get('/user/:uid/profile','getUserProfile');
$app->get('/user/:uid/followers','getFollowers');
$app->get('/user/:uid/followings','getFollowings');
$app->get('/user/:uid/posts','getUserPosts');

// return thumbimage + title + text + (likes,Comments, Pidders) count + Highest Pid
$app->get('/posts','getPosts');
$app->get('/post/:pid/likers','getLikers');
$app->get('/post/:pid/comments','getComments');


    
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
    $setString = addParam($setparam , 0);
    
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
 * POST /addpost
 * (u_id, p_title, p_image) NOT NULL
 * 
 * return Success with the new pid
 */
function addNewPost(){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    
    $uid    = $req->post('u_id');
    $title  = $req->post('p_title');
    $image  = $req->post('p_image');
    $desc   = $req->post('p_desc');
    $mobile = $req->post('p_mobile');
    $city   = $req->post('p_city');
    $color  = $req->post('p_color');
    $age    = $req->post('p_age');
    $sec    = $req->post('p_section');
    $sale   = $req->post('p_forsale');
    
    if (!filter_var($uid, FILTER_VALIDATE_INT))
        errorJson ("Something wrong: 1");
    if ($title == null || $title =="")
        errorJson ("Title cannot be empty");
    if ($image == null || $image == "")
        errorJson ("Image must be uploaded");
    
    $f_desc   = ($desc == null  || $desc == "" )?       array('','') : array('pdesc',':pdesc');
    $f_mobile = ($mobile == null  || $mobile == "" )?   array('','') : array('pmobile',':pmobile');
    $f_city   = ($city == null  || $city == "" )?       array('','') : array('pcity',':pcity');
    $f_color  = ($color == null  || $color == "" )?     array('','') : array('pcolor',':pcolor');
    $f_age    = ($age == null  || $age == "" )?         array('','') : array('page',':page');
    $f_sec    = ($sec == null  || $sec == "" )?         array('','') : array('psection',':psection');
    $f_sale   = ($sale == null  || $sale == "" )?       array('','') : array('forsale',':forsale');
    
    $param = array(
        'fields'=> array('uid', 'ptitle', 'pimage', $f_desc[0], $f_mobile[0], $f_city[0], $f_color[0], $f_age[0], $f_sec[0], $f_sale[0]),
        'values'=> array(':uid',':ptitle',':pimage', $f_desc[1], $f_mobile[1], $f_city[1], $f_color[1], $f_age[1], $f_sec[1], $f_sale[1]));
    
    $fields = addParam($param['fields'], 0);
    $values = addParam($param['values'], 0);
    
    $query = "INSERT INTO mz_post (".$fields.") VALUES (".$values.")";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        
        $stmt->bindParam("uid", $uid);
        $stmt->bindParam("ptitle", $title);
        $stmt->bindParam("pimage", $image);
        
        ($desc == "")?   :$stmt->bindParam("pdesc",   $desc);
        ($mobile == "")? :$stmt->bindParam("pmobile", $mobile);
        ($city == "")?   :$stmt->bindParam("pcity",   $city, PDO::PARAM_INT);
        ($color == "")?  :$stmt->bindParam("pcolor",  $color,PDO::PARAM_INT);
        ($age == "")?    :$stmt->bindParam("page",    $age,  PDO::PARAM_INT);
        ($sec == "")?    :$stmt->bindParam("psection",$sec,  PDO::PARAM_INT);
        ($sale == "")?   :$stmt->bindParam("forsale", $sale, PDO::PARAM_BOOL);
        $stmt->execute();
        $pid = $dbCon->lastInsertId();
        $dbCon = null;
        echo json_encode(array('Success' => $pid));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }  
    
    
    //echo json_encode(array("success"=>$pid));
}

/***
 * POST /post/:pid/comment
 */
function addComment($pid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $uid = $req->post('u_id');
    $comment = $req->post('c_txt');
    $price = $req->post('c_price');
    
    if ($uid == null){
        errorJson("Somthing Wrong");
    }
    if ($comment == null && $price == null){
        errorJson("Either Comment or Pid");
    }
    if ($price == NULL){
        $price = null;
    }
    $query = "INSERT INTO mz_post_comment(pid, uid, comment, price)"
            . "                  VALUES(:pid, :uid, :comment, :price)";
    
    try {
        $dbCon = getConnection();
        //$stmt   = $dbCon->query($query);
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("pid", $pid);
        $stmt->bindParam("uid", $uid);
        $stmt->bindParam("comment", $comment);
        $stmt->bindParam("price", $price, PDO::PARAM_INT);
        $stmt->execute();
        $cid = $dbCon->lastInsertId();
        $dbCon = null;
        echo json_encode(array('Success' => $cid));
        //echo '{"users": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }  
}

/***
 * POST /post/:pid/like
 */
function likePost($pid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $uid = $req->post('u_id');
    
    if ($uid == null){
        errorJson("Something Wrong");
    }
    // check like existance first
    $query = "SELECT count(*) FROM mz_post_like WHERE pid = :pid AND uid = :uid ";
    //$query = "INSERT INTO mz_post_like(pid, uid) VALUES(:pid, :uid)";
    
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("pid", $pid, PDO::PARAM_INT);
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn()>0){
            errorJson("Already Liked");
        }
        
        $stmt = null;
        $query = "INSERT INTO mz_post_like(pid, uid) VALUES(:pid, :uid)";
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("pid", $pid, PDO::PARAM_INT);
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
        $stmt->execute();
        $lid = $dbCon->lastInsertId();
        $dbCon = null;
        echo json_encode(array('Success' => $lid));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }  
}

/***
 * POST /post/:pid/unlike
 */
function unLikePost($pid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $uid = $req->post('u_id');
    if ($uid == null){
        errorJson("Something Wrong");
    }
    $query = "SELECT count(*) FROM mz_post_like WHERE pid = :pid AND uid = :uid ";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("pid", $pid, PDO::PARAM_INT);
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn()==0){
            errorJson("Already NOT Liked");
        }
        $stmt = null;
        $query = "DELETE FROM mz_post_like  WHERE pid = :pid AND uid = :uid";
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("pid", $pid, PDO::PARAM_INT);
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
        $stmt->execute();
        $dbCon = null;
        echo json_encode(array('Success'));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/***
 * POST /user/:fuid/follow
 */
function followUser($fuid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $uid = $req->post('u_id');
    if ($uid == null){
        errorJson("Something Wrong");
    }
    $query = "SELECT count(*) FROM mz_following WHERE uid = :uid AND f_uid = :f_uid ";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
        $stmt->bindParam("f_uid", $fuid, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn()>0){
            errorJson("Already Followed");
        }
        $stmt = null;
        $query = "INSERT INTO mz_following(uid, f_uid) VALUES(:uid, :f_uid)";
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
        $stmt->bindParam("f_uid", $fuid, PDO::PARAM_INT);
        $stmt->execute();
        $dbCon = null;
        echo json_encode(array('Success'));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

/***
 * POST /user/:fuid/unfollow
 */
function unFollowUser($fuid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $uid = $req->post('u_id');
    if ($uid == null){
        errorJson("Something Wrong");
    }
    $query = "SELECT count(*) FROM mz_following WHERE uid = :uid AND f_uid = :f_uid ";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
        $stmt->bindParam("f_uid", $fuid, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn()==0){
                errorJson("Already Unfollowed");
        }
        $stmt = null;
        $query = "DELETE FROM mz_following WHERE uid = :uid AND f_uid = :f_uid";
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
        $stmt->bindParam("f_uid", $fuid, PDO::PARAM_INT);
        $stmt->execute();
        $dbCon = null;
        echo json_encode(array('Success'));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}



/***
 * TODO: change this to be decreament
 */
function addParam($param, $i){
    $t = "";
    if($i < count($param))
    {
        if($param[$i] == "")
            $t = addParam ($param, $i+1);
        else{
            $t = $param[$i];
            $n = addParam($param, $i+1);
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
        SELECT u.uid, i.uavatar, i.displayname, i.ustatus , i.mobile, age, 
            count(f.f_uid) as following, 
            count(distinct p.pid) as posts, 
            count(distinct ff.uid) as followers
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