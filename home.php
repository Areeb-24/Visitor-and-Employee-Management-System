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

$sql = "SELECT id, employee_name FROM employees";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <div class="page">
        <div class="header">
            <div class="logo"><a href="https://crridom.gov.in/"><img src="E:\website\logo.png" alt="CSIR-CRRI"></a></div>
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

        <div class="container1">
            <form action="index.php" method="post" id="myForm">
                <h1 id="login">Visitors Entry Form</h1>

                <div class="inputname formentry">
                    <label for="name">Visitor's Name</label>
                    <input type="text" name="name" id="name" class="input" placeholder="Enter your name" required>
                </div>

                <div class="inputphone formentry">
                    <label for="phone">Phone No.</label>
                    <input type="text" name="phone" id="phone" class="input" placeholder="Enter your Phone number" required>
                </div>

                <div class="inputemail formentry">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="input" placeholder="Enter your email" required>
                </div>

                <div class="inputidproof formentry">
                    <label for="id_type">ID Proof</label>
                    <select name="id_type" class="input" id="id_type" required>
                        <option value="">Select Id</option>
                        <option value="aadhaar">Aadhar Card</option>
                        <option value="pan">Pan Card</option>
                        <option value="voterid">Voter-Id</option>
                        <option value="drivinglicence">Driving Licence</option>
                    </select>
                </div>

                <div class="idnumber formentry">
                    <label for="id_number">Enter ID Number:</label>
                    <input type="text" name="id_number" class="input" id="id_number" required>
                </div>

                <div class="inputemployeevisited formentry">
                    <label for="employee">Employee to Visit</label>
                    <select name="employee" class="input" id="employee" required>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['employee_name'] . "'>" . $row['employee_name'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No employees available</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" name="employee_name" id="employee_name">
                </div>
                <div class="inputpurpose formentry">
                    <label for="others">Purpose of Visit</label>
                    <input type="text" id="others" name="others" class="input" placeholder="Enter your purpose of visit" required>
                </div>

                <div class="webcam-container">
                    <div id="my_camera"></div>
                    <div id="results" style="display:none;"></div>
                    <br>
                    <input type="button" value="Take Snapshot" id="take_snapshot_button" class="img-btn" onclick="take_snapshot()">
                    <input type="button" value="Retake Snapshot" id="retake_snapshot_button" class="img-btn" style="display:none;" onclick="retake_snapshot()">
                    <input type="hidden" name="photoStore" id="photoStore">
                </div>

                <button type="submit" id="submit">Submit</button>
            </form>
            <script>
                $(document).ready(function() {
                    $('#employee').select2({
                        placeholder: "Select a category",
                        allowClear: true
                    });
                });

                function updateEmployeeName() {
                    var employeeSelect = document.getElementById('employee');
                    var selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex].value;
                    document.getElementById('employee_name').value = selectedEmployee;
                }
            </script>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <script>
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 90
        });

        Webcam.attach('#my_camera');

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                document.getElementById('photoStore').value = data_uri;
                document.getElementById('my_camera').style.display = 'none';
                document.getElementById('results').style.display = 'block';
                document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
                document.getElementById('take_snapshot_button').style.display = 'none';
                document.getElementById('retake_snapshot_button').style.display = 'inline';
            });
        }

        function retake_snapshot() {
            document.getElementById('photoStore').value = '';
            document.getElementById('my_camera').style.display = 'block';
            document.getElementById('results').style.display = 'none';
            document.getElementById('results').innerHTML = '';
            document.getElementById('take_snapshot_button').style.display = 'inline';
            document.getElementById('retake_snapshot_button').style.display = 'none';
        }

        document.getElementById('submit').addEventListener('click', function(event) {
            var photoStore = document.getElementById('photoStore').value;


            // Existing validation logic here

        });

        document.getElementById('submit').addEventListener('click', function() {
            var name = document.getElementById('name').value;
            var phone = document.getElementById('phone').value;
            var email = document.getElementById('email').value;
            var id_type = document.getElementById('id_type').value;
            var id_number = document.getElementById('id_number').value;
            var employee = document.getElementById('employee').value;
            var others = document.getElementById('others').value;
            var photoStore = document.getElementById('photoStore').value;

            // Validate all fields
            if (name === '' || phone === '' || email === '' || id_type === '' || id_number === '' || employee === '' || others === '' || photoStore === '') {
                alert("All fields are required.");
                return;
            }
            // Validate phone number
            if (!/^\d{10}$/.test(phone)) {
                alert("Phone number must be 10 digits long.");
                return;
            }

            // Validate email
            if (!/\S+@\S+\.\S+/.test(email)) {
                alert("Please enter a valid email address.");
                return;
            }

            // Validate ID proof number based on selected type
            if (id_type === 'aadhaar' && !/^\d{12}$/.test(id_number)) {
                alert("Aadhaar number must be 12 digits long.");
                return;
            } else if (id_type === 'pan' && !/^[A-Z]{5}\d{4}[A-Z]{1}$/.test(id_number)) {
                alert("PAN number must be in the format AAAAA1234A.");
                return;
            } else if (id_type === 'voterid' && !/^[A-Z]{3}\d{7}$/.test(id_number)) {
                alert("Voter ID must be in the format ABC1234567.");
                return;
            } else if (id_type === 'drivinglicence' && !/^[A-Z]{2}\d{2} \d{4} \d{7}$/.test(id_number)) {
                alert("Driving Licence must be in the format AA12 1234 1234567.");
                return;
            }
            // Validate photo capture
            if (photoStore === '') {
                alert("Please take a snapshot.");
                event.preventDefault();
                return;
            }

        });
    </script>
</body>

</html>
<?php
$conn->close();
?>