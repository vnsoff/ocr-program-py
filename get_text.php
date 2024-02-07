<?php

require 'vendor/autoload.php'; // Inclua o autoloader do Composer

use thiagoalessio\TesseractOCR\TesseractOCR;

// Definir constantes
define('TESSERACT_EXECUTABLE_PATH', 'C:\Program Files\Tesseract-OCR\tesseract.exe');
define('TXT_DIRECTORY', 'txt/');
define('TXT_FILE_PREFIX', 'txt_');

// Função para gerar o nome do arquivo TXT
function generateTxtFilename($directory, $prefix) {
    $nextFileNumber = 1;
    while (file_exists($directory . $prefix . $nextFileNumber . '.txt')) {
        $nextFileNumber++;
    }
    return $directory . $prefix . $nextFileNumber . '.txt';
}

// Verificar se a solicitação é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se um arquivo de imagem foi enviado com sucesso
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        $imageFilePath = $_FILES['imageFile']['tmp_name'];

        // Instanciar o TesseractOCR
        $tesseract = new TesseractOCR($imageFilePath);
        // Definir o idioma para português (opcional)
        $tesseract->lang('por'); 
        // Especificar o caminho do executável do Tesseract
        $tesseract->executable(TESSERACT_EXECUTABLE_PATH);

        // Executar OCR na imagem
        $text = $tesseract->run();

        // Gerar o nome do arquivo TXT
        $txtFilename = generateTxtFilename(TXT_DIRECTORY, TXT_FILE_PREFIX);

        // Salvar o texto extraído no arquivo TXT
        if (file_put_contents($txtFilename, $text) !== false) {
            // Exibir uma mensagem de sucesso
            echo 'Texto extraído e salvo com sucesso em: ' . $txtFilename;
        } else {
            // Exibir uma mensagem de erro em caso de falha ao salvar o arquivo
            echo 'Erro ao salvar o arquivo de texto.';
        }
    } else {
        // Exibir uma mensagem de erro se houver problemas com o arquivo enviado
        echo 'Erro ao fazer upload do arquivo.';
    }
} else {
    // Exibir uma mensagem de erro se a solicitação não for do tipo POST
    echo 'Método de solicitação inválido.';
}