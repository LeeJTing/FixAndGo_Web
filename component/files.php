<?php
function uploadFiles($inputName, $targetDir = "../../images/product/", $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'])
{
    $uploadedFiles = [];

    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            echo "❌ Failed to create directory: $targetDir<br>";
            return [];
        }
    }

    // Check if files exist
    if (!empty($_FILES[$inputName]['name'][0])) {

        $fileCount = count($_FILES[$inputName]['name']);

        for ($i = 0; $i < $fileCount; $i++) {

            $originalName = $_FILES[$inputName]['name'][$i];
            $tmpPath      = $_FILES[$inputName]['tmp_name'][$i];

            // Use unique name (recommended)
            $fileName = basename($originalName);

            $targetFilePath = $targetDir . $fileName;

            // Validate extension
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            if (!in_array($fileType, $allowedTypes)) {
                echo "❌ Invalid file type for: $originalName<br>";
                continue;
            }

            // Move the file
            if (move_uploaded_file($tmpPath, $targetFilePath)) {
                echo "✔ Uploaded: $targetFilePath<br>";
                $uploadedFiles[] = "images/product/" . $fileName;
                echo realpath($targetDir);
            } else {
                echo "❌ Failed to upload: $originalName<br>";
            }
        }
    } else {
        echo "❌ No files found in \$_FILES['$inputName']<br>";
    }

    return $uploadedFiles;
}
