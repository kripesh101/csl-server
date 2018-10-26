<?php 
$username = $_POST["username"];

$user_exist = 0;

include 'sql_info.php';

$SQL_conn = mysqli_connect($SQL_ServerName, $SQL_Username, $SQL_Password, $SQL_DB_Name);

if (!$SQL_conn) {
            die("Connection failed: " . mysqli_connect_error());
}

$username_sql = 'SELECT UserName, UserKey FROM Users WHERE UserName = "'.$username.'";';
$username_res = mysqli_query($SQL_conn, $username_sql);

if (mysqli_num_rows($username_res) == 1){
    #Username exists. Now verify password.
    $user_exist = 1;
    
    $row = $username_res->fetch_assoc();
    
    $password = $_POST["password"];
    
    if (password_verify($password, $row["UserKey"])){
        #User successfully authenticated
        $random_bytes = openssl_random_pseudo_bytes(32, $cstrong);
        $cookieval = bin2hex($random_bytes);
        $cookiehash = password_hash($cookieval, PASSWORD_DEFAULT);
        
        $token_sql = 'UPDATE Users SET CurrentLoginToken = "'.$cookiehash.'" WHERE UserName = "'.$username.'";';
        mysqli_query($SQL_conn, $token_sql);
        
        setcookie("login_token", $cookieval, time() + (86400), "/");
        setcookie("username", $username, time() + (86400), "/");
        setcookie("login_fail", "", time() + (-3600), "/");
    }
    else{
        #Password mismatch
        setcookie("login_fail", "YES", time() + (10), "/");
    }
}
if ($user_exist == 0){
    setcookie("login_fail", "YES", time() + (10), "/");
}
mysqli_close($SQL_conn);


?>
<html>
<body>
    Processing login information...
    <script type="text/javascript">
           window.location.replace(".");
    </script>
</body>
</html> 
 
