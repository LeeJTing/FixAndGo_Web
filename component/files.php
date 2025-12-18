<?php
function uploadFiles(
    $inputName,
    $targetDir,
    $publicPath,
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']
) {
    $uploadedFiles = [];

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (empty($_FILES[$inputName]['name'])) {
        return [];
    }

    $files = $_FILES[$inputName];

    // Normalize single → multiple
    if (!is_array($files['name'])) {
        $files = [
            'name'     => [$files['name']],
            'tmp_name' => [$files['tmp_name']],
            'error'    => [$files['error']]
        ];
    }

    $fileCount = count($files['name']);

    for ($i = 0; $i < $fileCount; $i++) {

        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        $originalName = $files['name'][$i];
        $tmpPath      = $files['tmp_name'][$i];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedTypes)) {
            continue;
        }

        $fileName = uniqid('img_', true) . '.' . $ext;
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($tmpPath, $targetFilePath)) {
            $uploadedFiles[] = $publicPath . $fileName;
        }
    }

    return $uploadedFiles;
}
