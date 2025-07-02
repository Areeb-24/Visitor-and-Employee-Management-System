<?php
$server = "localhost";
$username = "root";
$password = "";
$db_name = "login";

$con = mysqli_connect($server, $username, $password, $db_name);

if (!$con) {
    die("connection to this server fails due to" . mysqli_connect_error());
}

$last_id = $_GET['id'];
$sql = "SELECT * FROM `record` WHERE `id` = $last_id";
$result = $con->query($sql);

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Last Entry</title>
    <link rel="stylesheet" href="fetch.css">
</head>
<body>
    <div class="container">
        <h2>Your Details</h2>
        <?php
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<p>Name: " . $row['Name'] . "</p>";
            echo "<p>Phone: " . $row['Phone'] . "</p>";
            echo "<p>Email: " . $row['Email'] . "</p>";
            echo "<p>Others: " . $row['others'] . "</p>";
            echo "<p>Date: " . $row['date'] . "</p>";
        } else {
            echo "<p>No record found.</p>";
        }
        ?>
         <button class="print-btn" onclick="printContent()">Print</button>
        <a href="index.php">Back to Form</a>
    </div>
    <script>
        function printContent() {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Last Entry</title></head><body>');
            printWindow.document.write(document.querySelector('.container').innerHTML);
            printWindow.document.write('</body><p></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>
