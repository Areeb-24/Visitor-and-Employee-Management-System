<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file upload and data import
if (isset($_POST["import"])) {
    $fileName = $_FILES["excel"]["name"];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = date("Y.m.d") . " - " . date("h.i.sa") . "." . $fileExtension;
    $targetDirectory = "empsheetFolder/" . $newFileName;

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
                if (count($row) == 1) {
                    $name = $row[0];

                    // Prepare and bind the SQL statement
                    $stmt = $conn->prepare("INSERT INTO employees (employee_name) VALUES (?)");
                    if ($stmt) {
                        $stmt->bind_param("s", $name);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['employee_name'];
    $division = $_POST['employee_division'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO employees (employee_name,employee_division) VALUES (?,?)");
    $stmt->bind_param("ss", $name,$division);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrieve Employee Data</title>
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

                <div id="empDataUpload">
                    <form method="post" id="newEmpForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <h4>Enter the details of the New Employee</h4><br>
                        <label for="employee_name">Employee Name:</label>
                        <input type="text" id="employee_name" name="employee_name" required>

                        <label for="employee_division">Employee Division:</label>
                        <input type="text" id="employee_division" name="employee_division" required><br>

                        <input class="btn-excel" type="submit" value="Submit">
                    </form>
                </div>

                <div class="empexcel">
                    <div class="exceldownload">
                        <h4 style="padding-left: 5%;">Click the below button to download the excel format to upload Employee data.</h4>
                        <a href="employee.xlsx" class="btn btn-primary" download>Download Excel Sheet</a>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="excelform" method="post" enctype="multipart/form-data">
                        <label for="excel">Select and upload Excel File</label>
                        <input type="file" name="excel" id="inputexcel" required>
                        <button class="btn-excel" type="submit" name="import">Import</button>
                    </form>
                    <p style="padding-left: 5%;">Note*:  only upload the data in the provided excel sheet format</p>
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

        $(document).ready(function() {
            $('#employee').select2({
                placeholder: "Select a category",
                allowClear: true
            });
        });
    </script>
</body>

</html>