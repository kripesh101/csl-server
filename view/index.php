<?php

$target = $_SERVER['REQUEST_URI'];
$requestusr = substr($target, 6);

include '../sql_info.php';

$SQL_conn = mysqli_connect($SQL_ServerName, $SQL_Username, $SQL_Password, $SQL_DB_Name);
if (!$SQL_conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($requestusr == "") {
    
    $SQL_cmd = 'SELECT UserName FROM Users;';
    $sql_res = mysqli_query($SQL_conn, $SQL_cmd);
    if (mysqli_num_rows($sql_res) == 0){
        echo 'No users!';
        die();
    }

    echo '<ul>';

    while ( $row = $sql_res->fetch_assoc()) {
        echo '<li><a href="'. $row ['UserName'] . '">' . $row ['UserName'] . '</a></li>';
    }

    echo '</ul>';

} else {
    $SQL_cmd = 'SELECT UserName, ModelID, CapeID, ModelType FROM Users WHERE UserName = "'.$requestusr.'";';

    $sql_res = mysqli_query($SQL_conn, $SQL_cmd);

    if (mysqli_num_rows($sql_res) == 1){
        $row = $sql_res->fetch_assoc();

        $skinurl = "?target=/textures/".$row["ModelID"];
        $capeurl = "?target=/textures/".$row["CapeID"];
        $username = $row["UserName"];
        $type = $row["ModelType"] == "S" ? "slim" : "default";

        echo str_replace("[[TYPE]]",$type,str_replace("[[USERNAME]]", $username, str_replace("[[CAPEURL]]",$capeurl,str_replace("[[SKINURL]]",$skinurl,file_get_contents("../static/viewpage.html")))));

        mysqli_close($SQL_conn);
    } else {
        mysqli_close($SQL_conn);
        http_response_code(404);
        die();
    }
}


?>