<?php
$target = (isset($_GET['target'])) ? $_GET['target'] : 'NONE';

if (strpos($target, '.json') !== false){
    $requestusr = substr($target, 1, strpos($target, '.json') - 1);
    
    include 'sql_info.php';

    $SQL_conn = mysqli_connect($SQL_ServerName, $SQL_Username, $SQL_Password, $SQL_DB_Name);
    
    if (!$SQL_conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $SQL_cmd = 'SELECT UserName, ModelID, CapeID, ModelType FROM Users WHERE UserName = "'.$requestusr.'";';
    
    header('Access-Control-Allow-Origin: *');

    $sql_res = mysqli_query($SQL_conn, $SQL_cmd);
    
    if (mysqli_num_rows($sql_res) == 1){
        header('content-type: application/json');
        $row = $sql_res->fetch_assoc();
        echo("{\n");
        echo("\t\"username\": "."\"".$row["UserName"]."\",\n");
        echo("\t\"skins\": {\n");
        if($row["ModelID"] == NULL){
            echo("\t\t\"default\": null,\n");
            echo("\t\t\"slim\": null\n");
            echo("\t},\n");
        }else{
            if($row["ModelType"] == "D"){
                echo("\t\t\"default\": \"".$row["ModelID"]."\",\n");
                echo("\t\t\"slim\": null\n");
                echo("\t},\n");
            }else{
                echo("\t\t\"default\": null,\n");
                echo("\t\t\"slim\": \"".$row["ModelID"]."\"\n");
                echo("\t},\n");
            }
        }
        if($row["CapeID"] == NULL){
            echo("\t\"cape\": null\n");
        }else{
            echo("\t\"cape\": \"".$row["CapeID"]."\"\n");
        }
        echo("}");
        mysqli_close($SQL_conn);
    }
    else{
        mysqli_close($SQL_conn);
        http_response_code(404);
        die();
    }
}
else if (strpos($target, '/textures/') !== false){
    
    header('Access-Control-Allow-Origin: *');

    $requestid = substr($target, strpos($target, '/textures/') + 10);
    
    if ($requestid === ""){
        header('content-type: image/png');
        echo(file_get_contents("static/steve.png"));
        die();
    }
    
    include 'sql_info.php';

    $SQL_conn = mysqli_connect($SQL_ServerName, $SQL_Username, $SQL_Password, $SQL_DB_Name);
    
    if (!$SQL_conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $SQL_cmd = 'SELECT TextureData FROM Textures WHERE TextureID = "'.$requestid.'";';
    
    $sql_res = mysqli_query($SQL_conn, $SQL_cmd);
    
    if (mysqli_num_rows($sql_res) == 1){
        header('content-type: image/png');
        $row = $sql_res->fetch_assoc();
        echo($row["TextureData"]);
        mysqli_close($SQL_conn);
    }
    else{
        mysqli_close($SQL_conn);
        http_response_code(404);
        die();
    }
    
   
}
else if ($target == "NONE"){
    if (count($_COOKIE) <= 1){
        if (((isset($_COOKIE["login_fail"])) ? $_COOKIE["login_fail"] : '') == "YES"){
            echo '<div class="loginfail">Username or Password is incorrect.</div>';
            echo '<script type="text/javascript">document.cookie = "login_fail=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";</script>';
        }
        echo(file_get_contents("static/login.html"));
    }
    
    else{
        $username = $_COOKIE["username"];
        
        include 'sql_info.php';

        $SQL_conn = mysqli_connect($SQL_ServerName, $SQL_Username, $SQL_Password, $SQL_DB_Name);
        
        if (!$SQL_conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        $username_sql = 'SELECT CurrentLoginToken, ModelID, CapeID, UserName, ModelType FROM Users WHERE UserName = "'.$username.'";';
        $username_res = mysqli_query($SQL_conn, $username_sql);
        
        if (mysqli_num_rows($username_res) == 1){
            #Now to check token
            
            $row = $username_res->fetch_assoc();
            $user_token = $_COOKIE["login_token"];
            
            if (password_verify($user_token, $row["CurrentLoginToken"])){
                #User successfully verified!
                $skinurl = "?target=/textures/".$row["ModelID"];
                $capeurl = "?target=/textures/".$row["CapeID"];
                $username = $row["UserName"];
                $type = $row["ModelType"] == "S" ? "slim" : "default";
                
                if (isset($_COOKIE["change_fail"])){
                    echo str_replace("[[TYPE]]", $type, str_replace("[[PW-STATUS]]","Incorrect Old Password",str_replace("[[USERNAME]]", $username, str_replace("[[CAPEURL]]",$capeurl,str_replace("[[SKINURL]]",$skinurl,file_get_contents("static/homepage.html"))))));
                    echo '<script type="text/javascript">document.cookie = "change_fail=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";</script>';
                }else if (isset($_COOKIE["change_success"])){
                    echo str_replace("[[TYPE]]", $type, str_replace("[[PW-STATUS]]","Password Successfully Changed!",str_replace("[[USERNAME]]", $username, str_replace("[[CAPEURL]]",$capeurl,str_replace("[[SKINURL]]",$skinurl,file_get_contents("static/homepage.html"))))));
                    echo '<script type="text/javascript">document.cookie = "change_success=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";</script>';
                }else{
                    echo str_replace("[[TYPE]]", $type, str_replace("[[PW-STATUS]]","<br>",str_replace("[[USERNAME]]", $username, str_replace("[[CAPEURL]]",$capeurl,str_replace("[[SKINURL]]",$skinurl,file_get_contents("static/homepage.html"))))));
                }
            }
            else {
                #Hacker 100%
                #Delete all tokens for safety
                echo '<script type="text/javascript">var cookies = document.cookie.split(";");for (var i = 0; i < cookies.length; i++) {var cookie = cookies[i];var eqPos = cookie.indexOf("=");var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";}location.reload();</script>';
            }
        }
        else {
            #Delete all tokens for safety
            echo '<script type="text/javascript">var cookies = document.cookie.split(";");for (var i = 0; i < cookies.length; i++) {var cookie = cookies[i];var eqPos = cookie.indexOf("=");var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";}location.reload();</script>';
        }
        mysqli_close($SQL_conn);
    }
}
else{
    http_response_code(404);
    die();
}
?>
