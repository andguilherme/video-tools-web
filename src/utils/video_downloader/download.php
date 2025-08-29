<?php
// download.php - Script para processar downloads de vídeo (VERSÃO FINAL)

// Configurações
ini_set('max_execution_time', 300); // 5 minutos para downloads grandes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Diretório onde os vídeos serão salvos
$downloadDir = __DIR__ . '/downloads/';

// Cria o diretório se não existir
if (!is_dir($downloadDir)) {
    mkdir($downloadDir, 0755, true);
}

// Função para sanitizar nome de arquivo
function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

// Função para validar URL
function isValidVideoUrl($url) {
    $validDomains = [
        'youtube.com', 'youtu.be', 'vimeo.com', 'dailymotion.com',
        'twitch.tv', 'facebook.com', 'instagram.com', 'tiktok.com'
    ];
    
    $parsedUrl = parse_url($url);
    if (!$parsedUrl || !isset($parsedUrl['host'])) {
        return false;
    }
    
    $host = strtolower($parsedUrl['host']);
    $host = preg_replace('/^www\./', '', $host);
    
    foreach ($validDomains as $domain) {
        if (strpos($host, $domain) !== false) {
            return true;
        }
    }
    
    return false;
}

// Função para executar yt-dlp via linha de comando (VERSÃO FINAL)
function downloadVideo($url, $options = []) {
    global $downloadDir;
    
    // Comando base do yt-dlp
    $command = 'yt-dlp';
    
    // Adiciona opções
    $args = [];
    
    // Diretório de output
    $args[] = '--output "' . $downloadDir . '%(title)s.%(ext)s"';
    
    // Formato - mantendo -f best explícito como no script shell do usuário
    if (isset($options['format']) && !empty($options['format'])) {
        switch ($options['format']) {
            case 'mp3':
                $args[] = '--extract-audio --audio-format mp3';
                break;
            case 'mp4':
                $args[] = '--format "best[ext=mp4]/best"';
                break;
            case 'bestaudio':
                $args[] = '--format bestaudio';
                break;
            case 'bestvideo':
                $args[] = '--format bestvideo';
                break;
            case 'worst':
                $args[] = '--format worst';
                break;
            case 'best':
            default:
                // Usa -f best explicitamente como no script shell do usuário
                $args[] = '--format best';
                break;
        }
    } else {
        // Padrão: usa -f best como no script shell
        $args[] = '--format best';
    }
    
    // Qualidade específica
    if (isset($options['quality']) && !empty($options['quality'])) {
        $quality = intval($options['quality']);
        $args[] = '--format "best[height<=' . $quality . ']/best"';
    }
    
    // Apenas áudio
    if (isset($options['audio_only']) && $options['audio_only']) {
        $args[] = '--extract-audio --audio-format mp3';
    }
    
    // Apenas informações
    if (isset($options['get_info']) && $options['get_info']) {
        $args[] = '--dump-json --no-download';
    }
    
    // Outras opções úteis
    $args[] = '--no-playlist'; // Baixa apenas o vídeo, não a playlist
    $args[] = '--write-info-json'; // Salva metadados
    $args[] = '--write-thumbnail'; // Salva thumbnail
    
    // Monta o comando completo - CORRIGIDO: sem aspas duplas extras
    $fullCommand = $command . ' ' . implode(' ', $args) . ' ' . escapeshellarg($url) . ' 2>&1';
    
    // Executa o comando
    $output = [];
    $returnCode = 0;
    exec($fullCommand, $output, $returnCode);
    
    return [
        'success' => $returnCode === 0,
        'output' => implode("\n", $output),
        'command' => $fullCommand,
        'return_code' => $returnCode
    ];
}

// Função para obter informações do vídeo
function getVideoInfo($url) {
    $command = 'yt-dlp --dump-json --no-download ' . escapeshellarg($url) . ' 2>&1';
    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0 && !empty($output)) {
        $jsonOutput = implode("\n", $output);
        $info = json_decode($jsonOutput, true);
        return $info;
    }
    
    return null;
}

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => '', 'data' => null];
    
    try {
        // Valida a URL
        $videoUrl = trim($_POST['video_url'] ?? '');
        if (empty($videoUrl)) {
            throw new Exception('URL do vídeo é obrigatória.');
        }
        
        if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            throw new Exception('URL inválida.');
        }
        
        if (!isValidVideoUrl($videoUrl)) {
            throw new Exception('URL não é de uma plataforma suportada.');
        }
        
        // Prepara opções
        $options = [
            'format' => $_POST['format'] ?? 'best',
            'quality' => $_POST['quality'] ?? '',
            'audio_only' => isset($_POST['audio_only']),
            'get_info' => isset($_POST['get_info'])
        ];
        
        // Se for apenas para obter informações
        if ($options['get_info']) {
            $info = getVideoInfo($videoUrl);
            if ($info) {
                $response['success'] = true;
                $response['message'] = 'Informações obtidas com sucesso!';
                $response['data'] = [
                    'title' => $info['title'] ?? 'N/A',
                    'duration' => isset($info['duration']) ? gmdate("H:i:s", $info['duration']) : 'N/A',
                    'uploader' => $info['uploader'] ?? 'N/A',
                    'view_count' => isset($info['view_count']) ? number_format($info['view_count']) : 'N/A',
                    'upload_date' => $info['upload_date'] ?? 'N/A',
                    'description' => substr($info['description'] ?? '', 0, 200) . '...',
                    'thumbnail' => $info['thumbnail'] ?? null
                ];
            } else {
                throw new Exception('Não foi possível obter informações do vídeo.');
            }
        } else {
            // Faz o download
            $result = downloadVideo($videoUrl, $options);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = 'Download concluído com sucesso!';
                $response['data'] = [
                    'output' => $result['output'],
                    'download_dir' => $downloadDir
                ];
            } else {
                throw new Exception('Erro no download: ' . $result['output']);
            }
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    // Se for uma requisição AJAX, retorna JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Download</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🎥 Resultado do Download</h1>
        </header>

        <main>
            <?php if (isset($response)): ?>
                <div class="result-container <?php echo $response['success'] ? 'success' : 'error'; ?>">
                    <h2><?php echo $response['success'] ? '✅ Sucesso!' : '❌ Erro'; ?></h2>
                    <p><?php echo htmlspecialchars($response['message']); ?></p>
                    
                    <?php if ($response['success'] && $response['data']): ?>
                        <?php if (isset($response['data']['title'])): ?>
                            <!-- Informações do vídeo -->
                            <div class="video-info">
                                <h3>Informações do Vídeo:</h3>
                                <ul>
                                    <li><strong>Título:</strong> <?php echo htmlspecialchars($response['data']['title']); ?></li>
                                    <li><strong>Duração:</strong> <?php echo htmlspecialchars($response['data']['duration']); ?></li>
                                    <li><strong>Canal:</strong> <?php echo htmlspecialchars($response['data']['uploader']); ?></li>
                                    <li><strong>Visualizações:</strong> <?php echo htmlspecialchars($response['data']['view_count']); ?></li>
                                    <li><strong>Data de Upload:</strong> <?php echo htmlspecialchars($response['data']['upload_date']); ?></li>
                                </ul>
                                
                                <?php if ($response['data']['thumbnail']): ?>
                                    <div class="thumbnail">
                                        <img src="<?php echo htmlspecialchars($response['data']['thumbnail']); ?>" 
                                             alt="Thumbnail do vídeo" style="max-width: 300px;">
                                    </div>
                                <?php endif; ?>
                                
                                <details>
                                    <summary>Descrição</summary>
                                    <p><?php echo nl2br(htmlspecialchars($response['data']['description'])); ?></p>
                                </details>
                            </div>
                        <?php else: ?>
                            <!-- Resultado do download -->
                            <div class="download-result">
                                <p><strong>Arquivos salvos em:</strong> <?php echo htmlspecialchars($response['data']['download_dir']); ?></p>
                                <details>
                                    <summary>Log do Download</summary>
                                    <pre><?php echo htmlspecialchars($response['data']['output']); ?></pre>
                                </details>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="actions">
                <a href="downloader.html" class="btn">← Voltar ao Formulário</a>
                <?php if (isset($response) && $response['success'] && !isset($response['data']['title'])): ?>
                    <a href="downloads/" class="btn" target="_blank">📁 Ver Arquivos Baixados</a>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>⚠️ <strong>Aviso:</strong> Respeite os direitos autorais e termos de uso das plataformas.</p>
        </footer>
    </div>
</body>
</html>