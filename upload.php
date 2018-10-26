<?php
    if (isset($_COOKIE['username'])){
        $username = $_COOKIE['username'];
        
        include 'sql_info.php';
        
        $SQL_conn = mysqli_connect($SQL_ServerName, $SQL_Username, $SQL_Password, $SQL_DB_Name);
        
        $username_sql = 'SELECT CurrentLoginToken FROM Users WHERE UserName = "'.$username.'";';
        $username_res = mysqli_query($SQL_conn, $username_sql);
        
        if (mysqli_num_rows($username_res) == 1){
            #Now to check token
            
            $row = $username_res->fetch_assoc();
            $user_token = $_COOKIE["login_token"];
            
            if (password_verify($user_token, $row["CurrentLoginToken"])){
                $task = $_POST["task"];
                if ($task == "Upload"){
                    $image_content = file_get_contents($_FILES['skin']['tmp_name']);
                    $image_hash = hash("sha256", $image_content);
                    $image_content = bin2hex($image_content);
                    $texturetype = $_POST["texturetype"];
                    $modeltype = $_POST["modeltype"];
                    if ($_FILES['skin']['error'] == 0){
                        
                                $stmt_img_models = 'INSERT INTO Textures VALUES("'.$image_hash.'",UNHEX("'.$image_content.'"));';
                        
                                if ($texturetype == "Model"){
                                    $stmt_usr_data = 'UPDATE Users SET ModelID = "'.$image_hash.'",ModelType = "'.$modeltype.'"  WHERE UserName = "'.$username.'";';
                                }else{
                                    $stmt_usr_data = 'UPDATE Users SET CapeID = "'.$image_hash.'" WHERE UserName = "'.$username.'";';
                                }
                                
                                mysqli_query($SQL_conn, $stmt_img_models);
                                mysqli_query($SQL_conn, $stmt_usr_data);
                    }
                }else if ($task == "Remove Skin"){
                    $sql_stmt =  'UPDATE Users SET ModelID = NULL WHERE UserName = "'.$username.'";';
                    mysqli_query($SQL_conn, $sql_stmt);
                }else if ($task == "Remove Cape"){
                    $sql_stmt =  'UPDATE Users SET CapeID = NULL WHERE UserName = "'.$username.'";';
                    mysqli_query($SQL_conn, $sql_stmt);
                }
            }else{
                echo '<script type="text/javascript">var cookies = document.cookie.split(";");for (var i = 0; i < cookies.length; i++) {var cookie = cookies[i];var eqPos = cookie.indexOf("=");var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";}location.replace(".");;</script>';
            }
        }else{
            echo '<script type="text/javascript">var cookies = document.cookie.split(";");for (var i = 0; i < cookies.length; i++) {var cookie = cookies[i];var eqPos = cookie.indexOf("=");var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";}location.replace(".");;</script>';
        }
        mysqli_close($SQL_conn);
    }
?>

<html>
<body>
    Processing...
    <script type="text/javascript">
           location.replace(".");
    </script>
</body>
</html> 
