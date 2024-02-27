<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'ocr_functions.php'; // Include OCR functions

// Function to execute Python script
function execute_python_script($script_path, $args) {
    $command = escapeshellcmd("python $script_path $args");
    return shell_exec($command);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        $uploadedFile = $uploadDir . DIRECTORY_SEPARATOR . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadedFile);

        // Process the uploaded image and extract text
        $pngFilePath_output = $uploadDir . basename($_FILES['image']['name']);
        $processPNG = processPNG($pngFilePath_output);

        // Save extracted text to a file
        if (!empty($processPNG)) {
            $txtFilePath_output = __DIR__ . '/output/txt/';
            $txtFilename = generateTxtFilename($txtFilePath_output);
            file_put_contents($txtFilename, $processPNG);

            // Execute Python script for text search
            $searchResult = execute_python_script('search.py', "$txtFilename {$_POST['query']}");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image to Text Conversion and Search</title>
</head>
<body>
    <h1>Convert Image to Text and Search</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="image">Upload PNG Image:</label>
        <input type="file" name="image" id="image" accept=".png">
        <label for="query">Search Query:</label>
        <input type="text" name="query" id="query">
        <button type="submit">Search</button>
    </form>

    <?php if (isset($searchResult)): ?>
        <h2>Search Results:</h2>
        <p><?php echo $searchResult; ?></p>
    <?php endif; ?>
</body>
</html>
