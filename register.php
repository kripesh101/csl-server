<?php 
if (count($_COOKIE) == 0 || ((isset($_COOKIE["reg_fail"])) ? $_COOKIE["reg_fail"] : '') == "YES"){
    setcookie("registration_attempted", "YES", time() + (100), "/");
    
    if (((isset($_COOKIE["reg_fail"])) ? $_COOKIE["reg_fail"] : '') == "YES"){
        echo '<div class="loginfail">Username is taken / Too many characters</div>';
    }
    
    echo(file_get_contents("static/register.html"));
}
else if($_COOKIE["registration_attempted"] == "YES"){
    echo '<script type="text/javascript">document.cookie = "registration_attempted=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";</script>';

    # Do the registration stuff
    $username = (isset($_POST["username"])) ? $_POST["username"] : '';
    $password = (isset($_POST["password"])) ? $_POST["password"] : '';

    if ($username == '' || $password == ''){
        #Ignore & Clear ALL cookies
        echo '<script type="text/javascript">var cookies = document.cookie.split(";");for (var i = 0; i < cookies.length; i++) {var cookie = cookies[i];var eqPos = cookie.indexOf("=");var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";}location.reload();</script>';
    }
    else{
        include 'sql_info.php';

        $SQL_conn = mysqli_connect($SQL_ServerName, $SQL_Username, $SQL_Password, $SQL_DB_Name);
        
        if (!$SQL_conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        $username_sql = 'SELECT UserName FROM Users WHERE UserName = "'.$username.'";';
        $username_res = mysqli_query($SQL_conn, $username_sql);
        if(mysqli_num_rows($username_res) == 0){
            //New Username
            $userkey = password_hash($password, PASSWORD_DEFAULT);
            $new_account_sql = 'INSERT INTO Users(UserName, UserKey) VALUES("'.$username.'","'.$userkey.'");';
            
            if (mysqli_query($SQL_conn, $new_account_sql)){
                //Success
                echo(file_get_contents("static/success.html"));
                echo '<script type="text/javascript">setTimeout(function(){ window.location.replace("."); }, 4000);</script>';
            }
            else{
                //Faliure. Maybe excess chars
                setcookie("reg_fail", "YES", time() + (10), "/");
                echo '<script type="text/javascript">window.location.reload()</script>';
            }
        }
        else{
            //Existing Username
            setcookie("reg_fail", "YES", time() + (10), "/");
            echo '<script type="text/javascript">window.location.reload()</script>';
        }
        mysqli_close($SQL_conn);
         
        
    }
   
}else{
    #Not supposed to happen. Clear ALL cookies for safety.
    echo '<script type="text/javascript">var cookies = document.cookie.split(";");for (var i = 0; i < cookies.length; i++) {var cookie = cookies[i];var eqPos = cookie.indexOf("=");var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";}location.reload();</script>';
}
?>
