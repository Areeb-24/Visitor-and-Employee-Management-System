<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employees
$sql = "SELECT id, employee_name FROM employees";
$employees = $conn->query($sql);

// Initialize variables
$records = [];
$selectedEmployee = "";
$startDate = "";
$endDate = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedEmployee = $_POST['employee'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Build SQL query
    $sql = "SELECT * FROM record WHERE employee = ?";
    if (!empty($startDate) && !empty($endDate)) {
        $sql .= " AND Date BETWEEN ? AND ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $selectedEmployee, $startDate, $endDate);
    } else {
        $sql .= " ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $selectedEmployee);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }

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
            <div class="selectemployee">
                <h3 style="padding-left: 22%;">Employee Records</h3>
                <form action="employeeReport.php" method="post" id="selectemployee">
                        <label for="employee">Select Employee</label>
                        <select name="employee" id="employee"  required>
                            <option value="">Select Employee</option>
                            <?php
                            if ($employees->num_rows > 0) {
                                while ($row = $employees->fetch_assoc()) {
                                    echo "<option value='" . $row['employee_name'] . "'>" . $row['employee_name'] . "</option>";
                                }
                            } else {
                                echo "<option value=''>No employees available</option>";
                            }
                            ?>
                        </select>
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" >

                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" >

                    <button type="submit" class="btn-retrieve">Retrieve Data</button>
                </form>
            </div>

            <?php if (!empty($records)): ?>
            <h2>Records for <?php echo htmlspecialchars($selectedEmployee); ?>
            <?php if (!empty($startDate) && !empty($endDate)): ?>
                from <?php echo htmlspecialchars($startDate); ?> to <?php echo htmlspecialchars($endDate); ?>
            <?php endif; ?></h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Serial No.</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>ID Type</th>
                        <th>ID Number</th>
                        <th>Employee</th>
                        <th>Others</th>
                        <th>Photo</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serialNumber = 1; ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo $serialNumber++; ?></td>
                            <td><?php echo htmlspecialchars($record['Name']); ?></td>
                            <td><?php echo htmlspecialchars($record['Phone']); ?></td>
                            <td><?php echo htmlspecialchars($record['Email']); ?></td>
                            <td><?php echo htmlspecialchars($record['id_type']); ?></td>
                            <td><?php echo htmlspecialchars($record['id_number']); ?></td>
                            <td><?php echo htmlspecialchars($record['employee']); ?></td>
                            <td><?php echo htmlspecialchars($record['others']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($record['Photo']); ?>" alt="Photo" width="100"></td>
                            <td><?php echo htmlspecialchars($record['Date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#employee').select2({
                placeholder: "Select an employee",
                allowClear: true
            });
        });
    </script>
</body>

</html>
