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
                $oldPassword = $_POST["oldPassword"];
                $newPassword = $_POST["newPassword"];
                
                $oldpw_sql_stmt = 'SELECT UserKey FROM Users WHERE UserName = "'.$username.'";';
                $oldpw_res = mysqli_query($SQL_conn, $oldpw_sql_stmt);
                $row = $oldpw_res->fetch_assoc();
                
                if (password_verify($oldPassword, $row["UserKey"])){
                    $newKey = password_hash($newPassword, PASSWORD_DEFAULT);
                    $newpw_sql_stmt = 'UPDATE Users SET UserKey ="'.$newKey.'" WHERE UserName = "'.$username.'";';
                    $newpw_res = mysqli_query($SQL_conn, $newpw_sql_stmt);
                    setcookie("change_fail", "", time() + (-3600), "/");
                    setcookie("change_success", "YES", time() + (10), "/");
                }else{
                    setcookie("change_fail", "YES", time() + (10), "/");
                    setcookie("change_success", "", time() + (-3600), "/");
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
