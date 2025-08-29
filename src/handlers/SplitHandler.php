<?php
// splitter.php - Divisão de vídeos

// Configurações
ini_set('max_execution_time', 300);
error_reporting(E_ALL);
//ini_set('display_errors', 1);

$uploadDir = __DIR__ . '/../../uploads/';
$outputDir = __DIR__ . '/../../public/outputs/';

// Cria os diretórios se não existirem
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

function sanitizeFilename($filename)
{
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

function splitVideo($inputFile, $startTime, $endTime, $outputFile)
{
    $command = sprintf(
        'ffmpeg -i %s -ss %s -to %s -copyinkf %s 2>&1',
        escapeshellarg($inputFile),
        escapeshellarg($startTime),
        escapeshellarg($endTime),
        escapeshellarg($outputFile)
    );

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

function getVideoDuration($inputFile)
{
    $command = sprintf(
        'ffprobe -v quiet -show_entries format=duration -of csv="p=0" %s',
        escapeshellarg($inputFile)
    );

    $duration = exec($command);
    return $duration ? (float) $duration : 0;
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
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';

        if (empty($startTime) || empty($endTime)) {
            throw new Exception('Tempos de início e fim são obrigatórios.');
        }

        // Move o arquivo para o diretório de uploads
        $inputFileName = sanitizeFilename($uploadedFile['name']);
        $inputFilePath = $uploadDir . $inputFileName;

        if (!move_uploaded_file($uploadedFile['tmp_name'], $inputFilePath)) {
            throw new Exception('Erro ao salvar o arquivo.');
        }

        // Define o arquivo de saída
        $baseName = pathinfo($inputFileName, PATHINFO_FILENAME);
        $extension = pathinfo($inputFileName, PATHINFO_EXTENSION);

        // Encontra um nome único para o arquivo de saída
        $suffix = 1;
        do {
            $outputFileName = $baseName . '_cortada_part' . $suffix . '.' . $extension;
            $outputFilePath = $outputDir . $outputFileName;
            $suffix++;
        } while (file_exists($outputFilePath));

        // Divide o vídeo
        $result = splitVideo($inputFilePath, $startTime, $endTime, $outputFilePath);

        if ($result['success']) {
            $response['success'] = true;
            $response['message'] = 'Vídeo dividido com sucesso!';
            $response['data'] = [
                'output_file' => $outputFileName,
                'download_url' => '../../public/outputs/' . $outputFileName,
                'start_time' => $startTime,
                'end_time' => $endTime
            ];
        } else {
            throw new Exception('Erro na divisão: ' . $result['output']);
        }

        // Remove o arquivo de entrada após a divisão
        unlink($inputFilePath);

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}
?>
