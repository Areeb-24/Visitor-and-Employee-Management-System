<?php
$server = "localhost";
$username = "root";
$password = "";
$db_name = "login";

$con = mysqli_connect($server, $username, $password, $db_name);

if (!$con) {
    die("Connection to the database failed: " . mysqli_connect_error());
}
$sql = "SELECT * FROM record ORDER BY id DESC LIMIT 1";
$result = $con->query($sql);

if (!$result) {
    echo "Error retrieving record: " . $con->error;
    exit();
}

$row = $result->fetch_assoc();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Last Entry</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
</head>

<body>
    <div class="page">
        <div class="header">
            <div class="logo"><a href="https://crridom.gov.in/"><img src="https://crridom.gov.in/sites/default/files/color/mayo-377a8647/logo.png" alt="CSIR-CRRI"></a></div>

            <div class="Name">
                <h1 id="Name">CSIR - Central Road Research Institute</h1>
                <h2>सीएसआईआर - केंद्रीय सड़क अनुसंधान संस्थान</h2>
            </div>
        </div>


        <div class="navbar">
            <div id="home">
                <a href="home.php"><i class="fa-solid fa-house-chimney" style="color: #ffffff;"></i></a>
            </div>

            <ul id="navoption">
                <li><a class="navlink" href="">Employee</a>
                <ul class="dropdown">
                    <li class="employeeFetch"><a href="employeeReport.php">Employee Report</a></li>
                    <li class="employeeFetch"><a href="empdataUpload.php">Employee upload</a></li>
                </ul>
            </li>
                <li><a class="navlink" href="dataUpload.php">Data Upload</a></li>
            </ul>
        </div>

        <div class="container2">
            <div id="details" class="content">
                <h1 id="reciept">Your Details</h1>
                <div class="details-container">
                    <div class="details-box">
                        <?php
                        if ($result->num_rows > 0) {
                            echo '<div class="details-item"><p><strong>Name:</strong> ' . $row['Name'] . '</p></div>';
                            echo '<div class="details-item"><p><strong>Phone:</strong> ' . $row['Phone'] . '</p></div>';
                            echo '<div class="details-item"><p><strong>Email:</strong> ' . $row['Email'] . '</p></div>';
                            echo '<div class="details-item"><p><strong>ID Proof Type:</strong> ' . $row['id_type'] . '</p></div>';
                            echo '<div class="details-item"><p><strong>ID Number:</strong> ' . $row['id_number'] . '</p></div>';
                            echo '<div class="details-item"><p><strong>Employee Name:</strong> ' . $row['employee'] . '</p></div>';
                            echo '<div class="details-item"><p><strong>Purpose:</strong> ' . $row['others'] . '</p></div>';
                            echo '<div class="details-item"><p><strong>Date:</strong> ' . $row['Date'] . '</p></div>';
                        } else {
                            echo "<p>No record found.</p>";
                        }
                        ?>
                    </div>
                    <div class="photo">
                        <!-- Display photo if available -->
                        <?php
                        if (!empty($row['Photo'])) {
                            echo '<img src="' . $row['Photo'] . '" alt="Photo">';
                        } else {
                            echo '<p>No photo available.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <button id="printbtn" class="print-btn" onclick="printDiv('details')">Print</button>
            <a id="redirect" href="home.php">Back to Form</a>
        </div>
    </div>
    <script>
        function printDiv(divId) {
            var printContents = document.getElementById(divId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }

        document.getElementById("printbtn").addEventListener("click", function() {
            printDiv('details');
        });
    </script>
</body>

</html>
