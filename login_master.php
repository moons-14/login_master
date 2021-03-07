<?php
//ユーザーネームを指定してパスワードを更新する
function password_reset($user_name,$new_password,$db_host,$db_name,$db_user,$db_pass,$db_table){
    $response=[];
    $response['status']="100";
    if(!isset($user_name)||$user_name==""||!isset($new_password)||$new_password==""||!isset($db_host)||$db_host==""||!isset($db_name)||$db_name==""||!isset($db_user)||$db_user==""||!isset($db_table)||$db_table==""){
        $response['status']="400";
        goto login_finish;
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        $response['status']="503";
        //$response['debug']="データベース接続失敗" ."errno: " . mysqli_connect_errno()."error: " . mysqli_connect_error();
        goto login_finish;
    }
    $sql ="SELECT * FROM ".$db_table." WHERE user_name ='".$user_name."'";
    $result = $mysqli->query($sql);
    foreach ($result as $row) {
        $db_user_id=$row['user_id'];
        $db_user_name=$row['user_name'];
    }
    if(isset($db_user_id)&&$db_user_id!=""){
    $sql ="UPDATE ".$db_table." SET user_password = '".password_hash($new_password, PASSWORD_DEFAULT)."' WHERE user_name = '".$user_name."'";
    $result = $mysqli->query($sql);
        $response['status']="200";
        $response['user_name']=$db_user_name;
        $response['user_id']=$db_user_id;
    
    }else{
        $response['status']="401";
    }
    $mysqli->close();
    login_finish:
    return $response;
}

//ユーザーネームとパスワードで登録する関数
function signup_user($user_name,$user_password,$db_host,$db_name,$db_user,$db_pass,$db_table){
    $response=[];
    $response['status']="100";
    if(!isset($user_name)||$user_name==""||!isset($user_password)||$user_password==""||!isset($db_host)||$db_host==""||!isset($db_name)||$db_name==""||!isset($db_user)||$db_user==""||!isset($db_table)||$db_table==""){
        $response['status']="400";
        goto login_finish;
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        $response['status']="503";
        //$response['debug']="データベース接続失敗" ."errno: " . mysqli_connect_errno()."error: " . mysqli_connect_error();
        goto login_finish;
    }

    if (preg_match("/^[a-zA-Z0-9]+$/", $user_name)) {
    $sql = "INSERT INTO ".$db_table." (user_name, user_password) VALUES ('".$user_name."', '".$user_password."')";
    $result = $mysqli->query($sql);
    if (!$result) {
        if($mysqli->errno==1062){
            $response['status']="502";
        }

    }else{
        $response['status']="200";
    }
    }else{
        $response['status']="302";
    }

        $mysqli->close();
        login_finish:
        return $response;
    }

//ユーザーネームとメールアドレスとパスワードで登録する関数(メール認証なし)
function signup($user_name,$user_email,$user_password,$db_host,$db_name,$db_user,$db_pass,$db_table){
    $response=[];
    $response['status']="100";
    if(!isset($user_name)||$user_name==""||!isset($user_email)||$user_email==""||!isset($user_password)||$user_password==""||!isset($db_host)||$db_host==""||!isset($db_name)||$db_name==""||!isset($db_user)||$db_user==""||!isset($db_table)||$db_table==""){
        $response['status']="400";
        goto login_finish;
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        $response['status']="503";
        //$response['debug']="データベース接続失敗" ."errno: " . mysqli_connect_errno()."error: " . mysqli_connect_error();
        goto login_finish;
    }
    if (preg_match("/^[a-zA-Z0-9]+$/", $user_name)) {
        if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $user_email)) {
            $sql = "INSERT INTO ".$db_table." (user_name,user_email,user_password) VALUES ('".$user_name."', '".$user_email."', '". password_hash($user_password, PASSWORD_DEFAULT)."')";
            $result = $mysqli->query($sql);
            if (!$result) {
                if($mysqli->errno==1062){
                    if(strpos($mysqli->error,'user_name') !== false){
                        $response['status']="502";
                    }else if(strpos($mysqli->error,'user_email') !== false){
                        $response['status']="503";
                    }
                }

            }else{
                $response['status']="200";
                $response['user_name']=$user_name;
                $response['user_id']=$mysqli->insert_id;
            }
        }else{
            $response['status']="303";
        }
    }else{
        $response['status']="302";
    }

    $mysqli->close();
    login_finish:
    return $response;
}

//ユーザーネームとメールアドレスとパスワードで登録する関数(メール認証あり)
//2.仮登録
function signup_provisional($user_name,$user_email,$user_password,$db_host,$db_name,$db_user,$db_pass,$db_table){
    $response=[];
    $response['status']="100";
    if(!isset($user_name)||$user_name==""||!isset($user_email)||$user_email==""||!isset($user_password)||$user_password==""||!isset($db_host)||$db_host==""||!isset($db_name)||$db_name==""||!isset($db_user)||$db_user==""||!isset($db_table)||$db_table==""){
        $response['status']="400";
        goto login_finish;
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        $response['status']="503";
        //$response['debug']="データベース接続失敗" ."errno: " . mysqli_connect_errno()."error: " . mysqli_connect_error();
        goto login_finish;
    }
        if (preg_match("/^[a-zA-Z0-9]+$/", $user_name)) {
            if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $user_email)) {
                $sql = "INSERT INTO ".$db_table." (user_name,user_email,user_password) VALUES ('".$user_name."', '.".$user_email."', '". password_hash($user_password, PASSWORD_DEFAULT)."')";
                $result = $mysqli->query($sql);
                if (!$result) {
                    if($mysqli->errno==1062){
                        if(strpos($mysqli->error,'user_name') !== false){
                            $response['status']="502";
                        }else if(strpos($mysqli->error,'user_email') !== false){
                            $response['status']="503";
                        }
                    }
    
                }else{
                    $response['status']="202";
                    $response['email_token']=$mysqli->insert_id.random_int(11111, 99999);
                    $response['user_name']=$user_name;
                    $response['user_id']=$mysqli->insert_id;
                    $sql ="UPDATE ".$db_table." SET email_token = ".$response['email_token']." WHERE user_id = ".$response['user_id'];
                    $result = $mysqli->query($sql);
                }
            }else{
                $response['status']="303";
            }
        }else{
            $response['status']="302";
        }

    $mysqli->close();
    login_finish:
    return $response;
}

//2.メール認証
function signup_email($email_token,$db_host,$db_name,$db_user,$db_pass,$db_table){
    $response=[];
    $response['status']="100";
    if(!isset($email_token)||$email_token==""||!isset($db_host)||$db_host==""||!isset($db_name)||$db_name==""||!isset($db_user)||$db_user==""||!isset($db_table)||$db_table==""){
        $response['status']="400";
        goto login_finish;
    }
    if(!preg_match("/^[0-9]+$/",$email_token)){
        $response['status']="401";
        goto login_finish;
    }
    $email_token=intval($email_token);
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        $response['status']="503";
        //$response['debug']="データベース接続失敗" ."errno: " . mysqli_connect_errno()."error: " . mysqli_connect_error();
        goto login_finish;
    }
    $sql ="SELECT * FROM ".$db_table." WHERE email_token = ".$email_token;
        $result = $mysqli->query($sql);
        foreach ($result as $row) {
        $db_user_id=$row['user_id'];
        $db_user_name=$row['user_name'];
        $db_user_email=$row['user_email'];
        }
        
    if(isset($db_user_id)&&$db_user_id!=""){
        if(substr($db_user_email, 0, 1)=="."){
        $sql ="UPDATE ".$db_table." SET user_email = '".substr($db_user_email, 1)."',email_token='' WHERE user_id = ".intval($db_user_id);
        $result = $mysqli->query($sql);
        $response['status']="200";
        $response['user_name']=$db_user_name;
        $response['user_id']=$db_user_id;
    }else{
        $response['status']="304";
    }
    }else{
        $response['status']="305";
    }
    $mysqli->close();
    login_finish:
    return $response;
}

//ユーザーネームとパスワードを指定して認証する関数
function login_name($user_name,$user_password,$db_host,$db_name,$db_user,$db_pass,$db_table){
    $response=[];
    $response['status']="100";
    if(!isset($user_name)||$user_name==""||!isset($user_password)||$user_password==""||!isset($db_host)||$db_host==""||!isset($db_name)||$db_name==""||!isset($db_user)||$db_user==""||!isset($db_table)||$db_table==""){
        $response['status']="400";
        goto login_finish;
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        $response['status']="503";
        //$response['debug']="データベース接続失敗" ."errno: " . mysqli_connect_errno()."error: " . mysqli_connect_error();
        goto login_finish;
    }
    if (preg_match("/^[a-zA-Z0-9]+$/", $user_name)) {
            $sql="SELECT user_password,user_id,user_name FROM ".$db_table." WHERE user_name = "."'".$user_name."'";
        $result = $mysqli->query($sql);
        foreach ($result as $row) {
            $db_user_password=$row['user_password'];
            $db_user_id=$row['user_id'];
            $db_user_name=$row['user_name'];
            }
            if(!isset($db_user_password)||$db_user_password==""){
                $response['status']="302";
                goto login_finish;
            }
            if(password_verify($user_password,$db_user_password)){
                $response['status']="200";
                $response['user_name']=$db_user_name;
                $response['user_id']=$db_user_id;
                goto login_finish;
            }else{
                $response['status']="301";
            }
    }else{
        $response['status']="302";
    }

    $mysqli->close();
    login_finish:
    return $response;
}

//メールアドレスとパスワードを指定して認証する関数

function login_email($user_email,$user_password,$db_host,$db_name,$db_user,$db_pass,$db_table){
    $response=[];
    $response['status']="100";
    if(!isset($user_email)||$user_email==""||!isset($user_password)||$user_password==""||!isset($db_host)||$db_host==""||!isset($db_name)||$db_name==""||!isset($db_user)||$db_user==""||!isset($db_table)||$db_table==""){
        $response['status']="400";
        goto login_finish;
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        $response['status']="503";
        //$response['debug']="データベース接続失敗" ."errno: " . mysqli_connect_errno()."error: " . mysqli_connect_error();
        goto login_finish;
    }
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $user_email)) {
            $sql="SELECT user_password,user_id,user_name FROM ".$db_table." WHERE user_email = "."'".$user_email."'";
            $result = $mysqli->query($sql);
        foreach ($result as $row) {
            $db_user_password=$row['user_password'];
            $db_user_id=$row['user_id'];
            $db_user_name=$row['user_name'];
            }
            if(!isset($db_user_password)||$db_user_password==""){
                $response['status']="302";
                goto login_finish;
            }
            if(password_verify($user_password,$db_user_password)){
                $response['status']="200";
                $response['user_name']=$db_user_name;
                $response['user_id']=$db_user_id;
            }else{
                $response['status']="301";
            }
    }else{
        $response['status']="302";
    }

    $mysqli->close();
    login_finish:
    return $response;
}

//ユーザーネームかメールアドレスとパスワードを指定して認証する関数
function login($user_NorM,$user_password,$db_host,$db_name,$db_user,$db_pass,$db_table){
    $response=[];
    $response['status']="100";
    if(!isset($user_NorM)||$user_NorM==""||!isset($user_password)||$user_password==""||!isset($db_host)||$db_host==""||!isset($db_name)||$db_name==""||!isset($db_user)||$db_user==""||!isset($db_table)||$db_table==""){
        $response['status']="400";
        goto login_finish;
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        $response['status']="503";
        //$response['debug']="データベース接続失敗" ."errno: " . mysqli_connect_errno()."error: " . mysqli_connect_error();
        goto login_finish;
    }

    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $user_NorM)) {
        $user_point="user_email";
    }else if (preg_match("/^[a-zA-Z0-9]+$/", $user_NorM)) {
        $user_point="user_name";
    }else{
        $response['status']="302";
        goto login_302;
    }
        $sql="SELECT user_password,user_id,user_name FROM ".$db_table." WHERE ".$user_point." = "."'".$user_NorM."'";
        $result = $mysqli->query($sql);
        foreach ($result as $row) {
        $db_user_password=$row['user_password'];
        $db_user_id=$row['user_id'];
        $db_user_name=$row['user_name'];
        }
        if(!isset($db_user_password)||$db_user_password==""){
            $response['status']="302";
            goto login_finish;
        }
        if(password_verify($user_password,$db_user_password)){
            $response['status']="200";
            $response['user_name']=$db_user_name;
            $response['user_id']=$db_user_id;
        }else{
            $response['status']="301";
        }
    login_302:
    $mysqli->close();
    login_finish:
    return $response;
}

//ログイン

/*
ステータスコード
200:ログインに成功しました
400:不足している引数があります
503:DBに接続できません
402:第一引数に誤りがあります
100:不明なエラーです
301:パスワードが違います
302:ユーザー名もしくはメールアドレスが違います

返り値
ステータスコード200の場合のみ以下の値が返ってきます
キー:値
user_name:DBに収納されているユーザーネーム
user_id:DBに収納されているユーザーID
どちらも認証に成功したユーザーの情報です
*/

//サインアップ
/*
ステータスコード
200:ユーザー登録に成功しました
202:メールアドレスの仮登録が完了しました
400:不足か間違った型の引数があります
401:メールトークンは数字しか含まれません
502:重複したユーザーネームで登録しようとしています
504:重複したメールアドレスで登録しようとしています
503:DBに接続できません
100:不明なエラーです
302:ユーザーネームの形式が違います
303:メールアドレスの形式が違います
304:メール認証済みです
305:メールトークンが違います

返り値
ステータスコード200の場合のみ以下の値が返ってきます
メール認証無し、メール認証
キー:値
user_name:DBに収納されたユーザーネーム
user_id:DBに収納されたユーザーID
どちらも登録に成功したユーザーの情報です
メール仮登録
email_token:メールトークン
*/

//パスワードリセット
/*
ステータスコード
200:パスワード変更に成功しました
400:不足か間違った型の引数があります
401:ユーザーネームが違います
503:DBに接続できません
100:不明なエラーです


返り値
ステータスコード200の場合のみ以下の値が返ってきます
キー:値
user_name:DBに収納されたユーザーネーム
user_id:DBに収納されたユーザーID
どちらも登録に成功したユーザーの情報です
*/
