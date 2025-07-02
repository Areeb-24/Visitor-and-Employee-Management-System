<?php
// Database connection settings
$server = "localhost";
$username = "root";
$password = "";
$db_name = "login";

// Establish a connection to the database
$con = new mysqli($server, $username, $password, $db_name);

// Check the connection
if ($con->connect_error) {
    die("Connection to database failed: " . $con->connect_error);
}

// Handle file upload and data import
if (isset($_POST["import"])) {
    $fileName = $_FILES["excel"]["name"];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = date("Y.m.d") . " - " . date("h.i.sa") . "." . $fileExtension;
    $targetDirectory = "uploads/" . $newFileName;

    // Validate the uploaded file
    $allowedExtensions = ['xls', 'xlsx'];
    if (in_array($fileExtension, $allowedExtensions) && move_uploaded_file($_FILES['excel']['tmp_name'], $targetDirectory)) {
        require 'excelReader/excel_reader2.php';
        require 'excelReader/SpreadsheetReader.php';

        try {
            $reader = new SpreadsheetReader($targetDirectory);
            $isFirstRow = true; // Variable to track the first row

            foreach ($reader as $key => $row) {
                // Skip the header row
                if ($isFirstRow) {
                    $isFirstRow = false;
                    continue;
                }
                // Ensure the row has the correct number of columns
                if (count($row) == 9) {
                    $name = $row[0];
                    $phone = $row[1];
                    $email = $row[2];
                    $id_type = $row[3];
                    $id_number = $row[4];
                    $employee = $row[5];
                    $others = $row[6];
                    $photo = $row[7];
                    $date = $row[8];

                    // Prepare and bind the SQL statement
                    $stmt = $con->prepare("INSERT INTO record (Name, Phone, Email, id_type, id_number, employee, others, photo, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt) {
                        $stmt->bind_param("sssssssss", $name, $phone, $email, $id_type, $id_number, $employee, $others, $photo, $date);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        throw new Exception("Failed to prepare the SQL statement: " . $con->error);
                    }
                } else {
                    throw new Exception("Invalid row format in the Excel file.");
                }
            }

            echo "<script>
                    alert('Successfully Imported');
                    document.location.href = '';
                  </script>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid file type or failed to upload file.</div>";
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Employee Data</title>
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

        <div class="admincontainer">

            <div class="adminbox">
                <div class="excelupload">
                    <div class="exceldownload">
                        <h4>Click the below button to download the excel format to upload data.</h4>
                        <a href="data.xlsx" class="btn btn-primary" download>Download Excel Sheet</a>
                    </div>  
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="excelform" method="post" enctype="multipart/form-data">
                        <label for="excel">Select and upload Excel File</label>
                        <input type="file" name="excel" id="inputexcel" required>
                        <button class="btn-excel" type="submit" name="import">Import</button>
                    </form>
                    <p>Note*:  only upload the data in the provided excel sheet format</p>
                    <hr>
                </div>

                <div class="phototransfer">
                    <h4>Select photos to transfer in database folder</h4><br>
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="file" id="fileInput" name="files[]" multiple class="hidden-input" onchange="displaySelectedFiles()">
                        <div id="selectedFiles"></div>
                        <input class="btn-excel" type="button" value="Transfer Photos" onclick="uploadFiles()">
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        function displaySelectedFiles() {
            const input = document.getElementById('fileInput');
            // const selectedFilesDiv = document.getElementById('selectedFiles');
            selectedFilesDiv.innerHTML = '';

            for (const file of input.files) {
                const div = document.createElement('div');
                div.textContent = file.name;
                selectedFilesDiv.appendChild(div);
            }
        }

        function uploadFiles() {
            const formData = new FormData(document.getElementById('uploadForm'));
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'transfer.php', true);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                } else {
                    alert('An error occurred!');
                }
            };

            xhr.send(formData);
        }
    </script>
</body>

</html>