<?php
function uploadFiles($inputName, $targetDir = "../images/product/", $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'])
{
    $uploadedFiles = [];

    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Check if files exist
    if (!empty($_FILES[$inputName]['name'][0])) {
        $fileCount = count($_FILES[$inputName]['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            $originalName = $_FILES[$inputName]['name'][$i];
            $tmpPath = $_FILES[$inputName]['tmp_name'][$i];

            // Generate unique file name to avoid collisions
            $fileName = uniqid() . "-" . basename($originalName);
            $targetFilePath = $targetDir . $fileName;

            // Get file extension
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Validate file type
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($tmpPath, $targetFilePath)) {
                    $uploadedFiles[] = $fileName; // Save uploaded file name
                }
            }
        }
    }

    return $uploadedFiles;
}
