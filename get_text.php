<?php

require 'vendor/autoload.php'; // Include Composer autoloader

use thiagoalessio\TesseractOCR\TesseractOCR;

// Constants
define('TESSERACT_EXECUTABLE_PATH', 'C:\Program Files\Tesseract-OCR\tesseract.exe');
define('TXT_DIRECTORY', 'txt/');
define('TXT_FILE_PREFIX', 'txt_');
define('IMAGE_DIRECTORY', 'images/');

function generateTxtFilename($directory, $prefix) {
    $nextFileNumber = 1;
    while (file_exists($directory . $prefix . $nextFileNumber . '.txt')) {
        $nextFileNumber++;
    }
    return $directory . $prefix . $nextFileNumber . '.txt';
}

function processPNG($pngFilePath) {
    // Use TesseractOCR to perform OCR on PNG file
    $tesseract = new TesseractOCR($pngFilePath);
    $tesseract->executable(TESSERACT_EXECUTABLE_PATH);
    return $tesseract->run();
}

function searchTextInFile($filename, $searchText) {
    $fileContents = file_get_contents($filename);
    return strpos($fileContents, $searchText) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
        $pdfFilePath = $_FILES['pdfFile']['tmp_name'];

        if (!file_exists(IMAGE_DIRECTORY)) {
            mkdir(IMAGE_DIRECTORY, 0777, true);
        }

        $output = null;
        $returnVar = null;
        exec("gs -dNOPAUSE -dBATCH -sDEVICE=pngalpha -r300 -sOutputFile=" . IMAGE_DIRECTORY . "page-%d.png $pdfFilePath", $output, $returnVar);

        if ($returnVar !== 0) {
            echo 'Error executing Ghostscript command.';
            exit;
        }

        $extractedText = '';
        $pngFiles = glob(IMAGE_DIRECTORY . '*.png');
        foreach ($pngFiles as $pngFile) {
            $extractedText .= processPNG($pngFile);
        }

        $txtFilename = generateTxtFilename(TXT_DIRECTORY, TXT_FILE_PREFIX);

        if (file_put_contents($txtFilename, $extractedText) !== false) {
            echo 'Text extracted and saved successfully to: ' . $txtFilename;

            $searchText = isset($_POST['searchText']) ? $_POST['searchText'] : '';
            $searchResults = [];
            foreach ($pngFiles as $pngFile) {
                if (searchTextInFile($pngFile, $searchText)) {
                    $searchResults[] = $pngFile;
                }
            }

            echo '<script>';
            echo 'var searchResults = ' . json_encode($searchResults) . ';';
            echo 'var searchText = ' . json_encode($searchText) . ';';
            echo '</script>';
        } else {
            echo 'Error saving text file.';
        }
    } else {
        echo 'Error uploading file.';
    }
} else {
    echo 'Invalid request method.';
}
