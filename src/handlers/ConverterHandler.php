<?php
// converter.php - Conversão de vídeos para GIF/MP3

// Configurações
ini_set('max_execution_time', 300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploadDir = __DIR__ . '/../../uploads/uploads/';
$outputDir = __DIR__ . '/../../public/outputs/';

// Cria os diretórios se não existirem
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

function convertVideo($inputFile, $outputFile, $format) {
    if ($format === 'gif') {
        $command = sprintf(
            'ffmpeg -i %s -vf "fps=30,scale=1080:-1:flags=lanczos" -c:v gif -b:v 5M %s 2>&1',
            escapeshellarg($inputFile),
            escapeshellarg($outputFile)
        );
    } elseif ($format === 'mp3') {
        $command = sprintf(
            'ffmpeg -i %s -vn -ar 44100 -ac 2 -ab 192k %s 2>&1',
            escapeshellarg($inputFile),
            escapeshellarg($outputFile)
        );
    } else {
        return ['success' => false, 'message' => 'Formato não suportado'];
    }

    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);

    return [
        'success' => $returnCode === 0,
        'output' => implode("\n", $output),
        'command' => $command,
        'return_code' => $returnCode
    ];
}

// Processa o formulário
$response = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => '', 'data' => null];
    
    try {
        // Verifica se foi feito upload de arquivo
        if (!isset($_FILES['video_file']) || $_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erro no upload do arquivo.');
        }

        $uploadedFile = $_FILES['video_file'];
        $format = $_POST['format'] ?? '';
        
        if (!in_array($format, ['gif', 'mp3'])) {
            throw new Exception('Formato de saída inválido.');
        }

        // Move o arquivo para o diretório de uploads
        $inputFileName = sanitizeFilename($uploadedFile['name']);
        $inputFilePath = $uploadDir . $inputFileName;
        
        if (!move_uploaded_file($uploadedFile['tmp_name'], $inputFilePath)) {
            throw new Exception('Erro ao salvar o arquivo.');
        }

        // Define o arquivo de saída
        $outputFileName = pathinfo($inputFileName, PATHINFO_FILENAME) . '.' . $format;
        $outputFilePath = $outputDir . $outputFileName;

        // Converte o vídeo
        $result = convertVideo($inputFilePath, $outputFilePath, $format);

        if ($result['success']) {
            $response['success'] = true;
            $response['message'] = 'Conversão concluída com sucesso!';
            $response['data'] = [
                'output_file' => $outputFileName,
                'download_url' => '../../public/outputs/' . $outputFileName,
                'format' => $format
            ];
        } else {
            throw new Exception('Erro na conversão: ' . $result['output']);
        }

        // Remove o arquivo de entrada após a conversão
        unlink($inputFilePath);

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}
?>
