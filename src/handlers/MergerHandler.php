<?php
// merger.php - Mesclagem de vídeos

// Configurações
ini_set('max_execution_time', 300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploadDir = __DIR__ . '/../../uploads/';
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

function mergeVideos($video1Path, $video2Path, $outputPath) {
    $uploadDir = __DIR__ . '/../../uploads/';
    // Cria um arquivo temporário com a lista de vídeos
    $listFile = $uploadDir . 'merge_list_' . uniqid() . '.txt';
    $listContent = "file '" . realpath($video1Path) . "'\n";
    $listContent .= "file '" . realpath($video2Path) . "'\n";
    file_put_contents($listFile, $listContent);

    $command = sprintf(
        'ffmpeg -f concat -safe 0 -i %s -c copy %s 2>&1',
        escapeshellarg($listFile),
        escapeshellarg($outputPath)
    );

    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);

    // Remove o arquivo temporário
    unlink($listFile);

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
        // Verifica se foram feitos uploads dos dois arquivos
        if (!isset($_FILES['video1']) || $_FILES['video1']['error'] !== UPLOAD_ERR_OK ||
            !isset($_FILES['video2']) || $_FILES['video2']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('É necessário enviar dois arquivos de vídeo.');
        }

        $video1 = $_FILES['video1'];
        $video2 = $_FILES['video2'];
        $outputName = $_POST['output_name'] ?? '';
        
        if (empty($outputName)) {
            $outputName = 'video_mesclado.mp4';
        } elseif (!str_ends_with(strtolower($outputName), '.mp4')) {
            $outputName .= '.mp4';
        }

        // Move os arquivos para o diretório de uploads
        $video1FileName = sanitizeFilename($video1['name']);
        $video2FileName = sanitizeFilename($video2['name']);
        
        $video1Path = $uploadDir . 'temp1_' . uniqid() . '_' . $video1FileName;
        $video2Path = $uploadDir . 'temp2_' . uniqid() . '_' . $video2FileName;
        
        if (!move_uploaded_file($video1['tmp_name'], $video1Path) ||
            !move_uploaded_file($video2['tmp_name'], $video2Path)) {
            throw new Exception('Erro ao salvar os arquivos.');
        }

        // Define o arquivo de saída
        $outputFileName = sanitizeFilename($outputName);
        $outputFilePath = $outputDir . $outputFileName;
        
        // Se o arquivo já existe, adiciona um sufixo
        $counter = 1;
        while (file_exists($outputFilePath)) {
            $baseName = pathinfo($outputFileName, PATHINFO_FILENAME);
            $extension = pathinfo($outputFileName, PATHINFO_EXTENSION);
            $outputFileName = $baseName . '_' . $counter . '.' . $extension;
            $outputFilePath = $outputDir . $outputFileName;
            $counter++;
        }

        // Mescla os vídeos
        $result = mergeVideos($video1Path, $video2Path, $outputFilePath);

        if ($result['success']) {
            $response['success'] = true;
            $response['message'] = 'Vídeos mesclados com sucesso!';
            $response['data'] = [
                'output_file' => $outputFileName,
                'download_url' => '../../public/outputs/' . $outputFileName,
                'video1_name' => $video1['name'],
                'video2_name' => $video2['name']
            ];
        } else {
            throw new Exception('Erro na mesclagem: ' . $result['output']);
        }

        // Remove os arquivos temporários
        unlink($video1Path);
        unlink($video2Path);

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        
        // Limpa arquivos temporários em caso de erro
        if (isset($video1Path) && file_exists($video1Path)) unlink($video1Path);
        if (isset($video2Path) && file_exists($video2Path)) unlink($video2Path);
    }
}

