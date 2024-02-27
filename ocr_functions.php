<?php
//ocr_functions.php

require 'vendor/autoload.php'; // Include Composer autoloader

use thiagoalessio\TesseractOCR\TesseractOCR;

// Constants
define('TESSERACT_EXECUTABLE_PATH', 'C:\Program Files\Tesseract-OCR\tesseract.exe');
define('TXT_DIRECTORY', 'txt/');
define('TXT_FILE_PREFIX', 'txt_');

// Function to preprocess PNG file
function preprocessImage($inputFilePath, $outputFilePath) {
    try {
        // Debug: Output the input and output file paths
        error_log('Input file path: ' . $inputFilePath);
        error_log('Output file path: ' . $outputFilePath);

        // Create Imagick object
        $imagick = new \Imagick($inputFilePath);

        // Debug: Output image dimensions before resizing
        error_log('Original image dimensions: ' . $imagick->getImageWidth() . 'x' . $imagick->getImageHeight());

        // Resize image
        $imagick->resizeImage(1000, 0, \Imagick::FILTER_LANCZOS, 1); // Resize width to 1000px, maintaining aspect ratio

        // Debug: Output image dimensions after resizing
        error_log('Resized image dimensions: ' . $imagick->getImageWidth() . 'x' . $imagick->getImageHeight());

        // Write the preprocessed image to the output file path
        $imagick->writeImage($outputFilePath);

        // Destroy Imagick object
        $imagick->destroy();

        // Debug: Output success message
        error_log('Preprocessing successful');

        return true;
    } catch (\Exception $e) {
        // Log error message
        error_log('Error preprocessing image: ' . $e->getMessage());
        return false;
    }
}


// Function to process PNG file
function processPNG($pngFilePath) {

    // Use TesseractOCR
    $tesseract = new TesseractOCR($pngFilePath);
    $tesseract->executable(TESSERACT_EXECUTABLE_PATH);

    // Perform OCR on the preprocessed image
    $text = $tesseract->run();

    return $text;
}


// Function to generate the TXT filename
function generateTxtFilename($directory) {
    $nextFileNumber = 1;
    while (file_exists($directory . 'file' . $nextFileNumber . '.txt')) {
        $nextFileNumber++;
    }
    return $directory . 'file' . $nextFileNumber . '.txt';
}