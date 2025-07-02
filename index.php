<?php
$insert = false;
$last_id = 0;

if (isset($_POST['name'])) {
    $server = "localhost";
    $username = "root";
    $password = "";
    $db_name = "login";

    $con = mysqli_connect($server, $username, $password, $db_name);

    if ($con) {
        //echo "you are connected to database";
    } else {
        die("connection to this server fails due to" . mysqli_connect_error());
    }

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $id_type = $_POST['id_type'];
    $id_number = $_POST['id_number'];
    $employee = $_POST['employee'];
    $others = $_POST['others'];
    $photoStore = $_POST['photoStore'];

    // Decode the base64 image
    $img = str_replace('data:image/jpeg;base64,', '', $photoStore);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    $file = 'uploadphoto/' . $name .'_'. date("Y.m.d") . " - " . date("h.i.sa") .'.jpg';
    file_put_contents($file, $data);


    $sql = "INSERT INTO `record` (`Name`, `Phone`, `Email`, `id_type`, `id_number`, `employee`, `others`,`Photo`,`Date`) 
            VALUES ('$name', '$phone', '$email', '$id_type', '$id_number', '$employee', '$others', '$file', current_timestamp())";
    
    echo $sql; // Print the SQL query for debugging

    if ($con->query($sql) === true) {
        $insert = true;
        $last_id = $con->insert_id;
        header("Location: finaldisplay.php?id=$last_id");
        exit();
    } else {
        echo "Error: $sql <br> $con->error";
    }

    $con->close();
}
?>
