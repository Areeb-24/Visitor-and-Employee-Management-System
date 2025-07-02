<?php
// Define the destination folder
$destinationFolder = 'uploadphoto/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if files were uploaded
    if (isset($_FILES['files']) && count($_FILES['files']['name']) > 0) {
        // Check if the destination folder exists, if not create it
        if (!is_dir($destinationFolder)) {
            mkdir($destinationFolder, 0777, true);
        }

        // Loop through the uploaded files
        for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
            $sourceFilePath = $_FILES['files']['tmp_name'][$i];
            $destinationFilePath = $destinationFolder . basename($_FILES['files']['name'][$i]);

            // Move the file to the destination folder
            if (move_uploaded_file($sourceFilePath, $destinationFilePath)) {
                echo "Moved: " . $_FILES['files']['name'][$i] . "<br>";
            } else {
                echo "Failed to move: " . $_FILES['files']['name'][$i] . "<br>";
            }
        }

        echo "Transfer completed.";
    } else {
        echo "No files were selected.";
    }
} else {
    echo "Invalid request method.";
}
?>

