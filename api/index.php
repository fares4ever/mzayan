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
//include 'db.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/hello/:name','sayHelloTo');
$app->post('/login','login');
$app->get('/testLogin','authenticate','testLogin');
$app->get('/logout','logout');

$app->post('/register','registerNewUser');
$app->post('/user/:uid/addname','addDisplayName');
$app->post('/addUserInfo/:uid','addUserInfo');
$app->post('/checkdisplayname','checkDispayName');

$app->post('/addpost','addNewPost');
$app->post('/post/:pid/comment','addComment');
$app->post('/post/:pid/like','likePost');
$app->post('/post/:pid/unlike','unLikePost');

$app->post('/user/:fuid/follow','followUser');
$app->post('/user/:fuid/unfollow','unFollowUser');

//$app->get('/posts','getPosts');
$app->get('/expolre/latest','exploreLatest');
$app->get('/expolre/popular','explorePopular');
$app->get('/user/:uid/profile','getUserProfile');
$app->get('/user/:uid/posts','getUserPosts');
$app->get('/user/:uid/followers','getFollowers');
$app->get('/user/:uid/followings','getFollowings');

$app->get('/activities','getActivities');

$app->get('/post/:pid/likers','getLikers');
$app->get('/post/:pid/comments','getComments');
$app->get('/post/:pid/bidders','getBidders');


    
// run
$app->run();

function sayHelloTo($name){
     echo "Hello, $name";
}

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
    


  
 
// <editor-fold desc="Login Functions">
    
/***
 *  POST /register
 *  @param String user_email
 *  @param String user_pass 
 */
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

/**
 * POST /user/:uid/addname
 * @param Int $uid 
 * @param String $user_dname Dispaly Name to add
 */
Function addDisplayName($uid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $dName = $req->post('user_dname');
    
    if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT))
        errorJson("Somthing Wrong");
    
    if(isDisplayNameUsed($dName))
        errorJson ($dName." Already Used");
     
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

/**
 * POST /addUserInfo/:uid
 * @param Int $uid 
 * @param Image user_avatar the user Image
 * @param String user_mobile User Mobile number
 * @param String user_age the age of the user
 * @param String user_status Status that appears under his Display Name
 */
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
// </editor-fold>

// <editor-fold desc="POST Functions">

/***
 * POST /addpost
 * (u_id, p_title, p_image) NOT NULL
 * return Success with the new pid
 * 
 * @param Int u_id the user that post the post
 * @param String p_title the title of the post
 * @param Image p_image the image of the post
 * @param String p_desc the describtion of the image
 * @param String p_mobile the person's mobile number to contact
 * @param Int p_city the city number from cities table
 * @param Int p_color the color number from color table
 * @param Int p_age the age number from the age table
 * @param Int p_section the section number from the section table
 * @param boolean p_forsale is this post for sale
 * 
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
 * @param Int pid << from the URI 
 * @param Int u_id the user that likes the post
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
 * @param Int pid << from the URI 
 * @param Int u_id the user that unlikes the post
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
 * @param Int $fuid the user to follow
 * @param Int u_id the following user
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
 * @param Int $fuid the user to unfollow
 * @param Int u_id the unfollowing user
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

// </editor-fold>

// <editor-fold desc="GET Functions">

/**
 *  GET /expolre/latest
 * @param Int page_number which paeg. for navigation page by page
 */
Function exploreLatest(){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $pageNumber = $req->get('page_number');
    // Number of postes per request
    $postPerPage = 21; 
    $offsit = $postPerPage * $pageNumber;
    
    $query = "SELECT p.pid, p.uid, ui.displayname, ui.uavatar, p.pimage, p.ptitle, p.pdesc, p.forsale, p.pdate,
		 cl.cname, a.aname, s.sname, cty.cname, 
		count(distinct c.cid) as ccount, 
		count(distinct l.lid) as lcount, 
		max( c.price ) as mprice
            FROM mz_post p	
		left join mz_userInfo ui on p.uid = ui.uid
		left join mz_post_comment c on p.pid = c.pid
		left join mz_post_like l on p.pid = l.pid
		left join mz_section s on p.psection = s.sid
		left join mz_color cl on p.pcolor = cl.cid
		left join mz_age a on p.page = a.aid
		left join mz_city cty on p.pcity = cty.cid

            GROUP BY p.pid
            ORDER BY p.pdate DESC
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

/**
 *  GET /expolre/popular get the most popular post by number of likes
 * @param Int page_number which paeg. for navigation page by page
 */
Function explorePopular(){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $pageNumber = $req->get('page_number');
    // Number of postes per request
    $postPerPage = 21; 
    $offsit = $postPerPage * $pageNumber;
    
    $query = "SELECT p.pid, p.uid, ui.displayname, ui.uavatar, p.pimage, p.ptitle, p.pdesc, p.forsale, p.pdate,
		cl.cname, a.aname, s.sname, cty.cname, 
		count(distinct c.cid) as ccount, 
		count(distinct l.lid) as lcount, 
		max( c.price ) as mprice
            FROM mz_post p	
		left join mz_userInfo ui on p.uid = ui.uid
		left join mz_post_comment c on p.pid = c.pid
		left join mz_post_like l on p.pid = l.pid
		left join mz_section s on p.psection = s.sid
		left join mz_color cl on p.pcolor = cl.cid
		left join mz_age a on p.page = a.aid
		left join mz_city cty on p.pcity = cty.cid              
            GROUP BY p.pid
            ORDER BY lcount desc
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



/**
 * GET /user/:uid/profile
 * @param type $uid 
 */
function getUserProfile($uid){
    
    if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT)){
        errorJson("Somthing Wrong in user id (uid)");
    }
    
    $query = "SELECT u.uid, i.uavatar, i.displayname, i.ustatus , i.mobile, age, 
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
        $dbCon = null;
        echo json_encode(array('UserInfo' => $userInfo));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * GET /user/:uid/posts
 */
function getUserPosts($uid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    $pageNumber = $req->get('page_number');
    // Number of postes per request
    $postPerPage = 21; 
    $offsit = $postPerPage * $pageNumber;
    
    $query = "SELECT p.pid, p.uid, ui.displayname, ui.uavatar, p.pimage, p.ptitle, p.pdesc, p.forsale, p.pdate,
		 cl.cname, a.aname, s.sname, cty.cname, 
		count(distinct c.cid) as ccount, 
		count(distinct l.lid) as lcount, 
		max( c.price ) as mprice
            FROM mz_post p	
		left join mz_userInfo ui on p.uid = ui.uid
		left join mz_post_comment c on p.pid = c.pid
		left join mz_post_like l on p.pid = l.pid
		left join mz_section s on p.psection = s.sid
		left join mz_color cl on p.pcolor = cl.cid
		left join mz_age a on p.page = a.aid
		left join mz_city cty on p.pcity = cty.cid

            WHERE p.uid = :uid                
            GROUP BY p.pid
            ORDER BY p.pdate desc
            LIMIT :offsit, :pcount ";
    
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query);
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
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

/**
 * GET /user/:uid/followers
 * @param type $uid 
 */
function getFollowers($uid){
       if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT)){
        errorJson("Somthing Wrong in user id (uid)");
    }
    
    $query = "  SELECT ui.uid, ui.displayname 
                FROM mz_following f
                    left join mz_userInfo ui on f.uid = ui.uid
                WHERE f.f_uid = :uid";
        
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid);
        $stmt->execute();
        $users = $stmt->fetchObject(); 
        $dbCon = null;
        echo json_encode(array('Followings' => $users));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * GET /user/:uid/followings
 * @param type $uid 
 */
function getFollowings($uid){
    if ($uid == null || !filter_var($uid, FILTER_VALIDATE_INT)){
        errorJson("Somthing Wrong in user id (uid)");
    }
    
    $query = "select ui.uid, ui.displayname 
            FROM mz_following f
                left join mz_userInfo ui on f.f_uid = ui.uid
            WHERE f.uid = :uid";
        
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("uid", $uid);
        $stmt->execute();
        $users = $stmt->fetchObject(); 
        $dbCon = null;
        echo json_encode(array('Followings' => $users));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}



/**
 * GET /post/:pid/likers
 * @param type $pid 
 */
Function getLikers($pid){
    echo json_encode("getLikers Not Implemented Yet ".$pid);
}

/**
 * GET /post/:pid/comments
 * @param type $pid 
 */
Function getComments($pid){
    
    if ($pid == null || !filter_var($pid, FILTER_VALIDATE_INT)){
        errorJson("Somthing Wrong in user id (uid)");
    }
    
    $query = "  SELECT c.uid, ui.displayname , comment, c_date, price
                FROM mz_post_comment c
                    LEFT JOIN mz_userInfo ui ON c.uid = ui.uid
                WHERE c.pid = :pid";
        
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("pid", $pid);
        $stmt->execute();
        $comments = $stmt->fetchObject(); 
        $dbCon = null;
        echo json_encode(array('Comments' => $comments));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    
    }
}

/**
 * GET /post/:pid/bidders
 * @param Int $pid post id
 */
Function getBidders($pid){
    
    if ($pid == null || !filter_var($pid, FILTER_VALIDATE_INT)){
        errorJson("Somthing Wrong in user id (uid)");
    }
    
    $query = "  SELECT ui.uid, ui.displayname, price, c_date
                FROM mz_post_comment c
                    LEFT JOIN mz_userInfo ui on c.uid = ui.uid
                WHERE c.price IS NOT NULL 
                      AND c.pid = :pid";
        
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query); 
        $stmt->bindParam("pid", $pid);
        $stmt->execute();
        $bidders = $stmt->fetchObject(); 
        $dbCon = null;
        echo json_encode(array('Bidders' => $bidders));
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    
    }
}

/***
 * GET /activities "get the latest followings' posts"
 * @param Int uid the user
 * @param Int page_number for posts pagination
 */
Function getActivities($uid){
    $app = \Slim\Slim::getInstance();
    $req = $app->request();
    
    $pageNumber = $req->get('page_number');
    // Number of postes per request
    $postPerPage = 21; 
    $offsit = $postPerPage * $pageNumber;
    
    $query = "SELECT p.pid, p.uid, ui.displayname, ui.uavatar, 
                p.pimage, p.ptitle, p.pdesc, p.forsale, p.pdate,
		cl.cname, a.aname, s.sname, cty.cname, 
		count(distinct c.cid) as ccount, 
		count(distinct l.lid) as lcount, 
		max( c.price ) as mprice
            FROM mz_post p	
		left join mz_userInfo ui on p.uid = ui.uid
		left join mz_post_comment c on p.pid = c.pid
		left join mz_post_like l on p.pid = l.pid
		left join mz_section s on p.psection = s.sid
		left join mz_color cl on p.pcolor = cl.cid
		left join mz_age a on p.page = a.aid
		left join mz_city cty on p.pcity = cty.cid
            WHERE p.uid IN (SELECT f_uid FROM mz_following WHERE uid = :uid)               
            GROUP BY p.pid
            ORDER BY p.pdate desc
            LIMIT :offsit, :pcount ";
    
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($query);
        $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
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
// </editor-fold>


 //<editor-fold desc="Helper Functions">

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

Function validatePassword($pass){
    if ($pass == null || $pass == "")
        errorJson ("Please choose Password");
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

// </editor-fold>


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