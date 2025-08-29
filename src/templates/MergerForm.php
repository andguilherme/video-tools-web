<?php include_once "../handlers/MergerHandler.php"  ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔗 Mesclar Vídeos</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/mergeStyle.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔗 Mesclar Vídeos</h1>
            <p>Una dois vídeos em um só arquivo</p>
        </header>

        <main>
            <?php if ($response): ?>
                <div class="result-container <?php echo $response['success'] ? 'success' : 'error'; ?>">
                    <h2><?php echo $response['success'] ? '✅ Sucesso!' : '❌ Erro'; ?></h2>
                    <p><?php echo htmlspecialchars($response['message']); ?></p>
                    
                    <?php if ($response['success'] && $response['data']): ?>
                        <div class="download-result">
                            <p><strong>Vídeos mesclados:</strong></p>
                            <p>📹 <?php echo htmlspecialchars($response['data']['video1_name']); ?></p>
                            <p>📹 <?php echo htmlspecialchars($response['data']['video2_name']); ?></p>
                            <p><strong>Resultado:</strong> <?php echo htmlspecialchars($response['data']['output_file']); ?></p>
                            <a href="<?php echo htmlspecialchars($response['data']['download_url']); ?>" 
                               class="btn download-btn" download>
                                📥 Baixar Vídeo Mesclado
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="video-uploads">
                    <div class="video-upload" onclick="document.getElementById('video1').click()">
                        <div class="video-number">1</div>
                        <div class="upload-icon">📹</div>
                        <h3>Primeiro Vídeo</h3>
                        <p>Clique para selecionar</p>
                        <input type="file" id="video1" name="video1" accept="video/*" required style="display: none;">
                        <div id="video1-name" class="file-name"></div>
                    </div>

                    <div class="video-upload" onclick="document.getElementById('video2').click()">
                        <div class="video-number">2</div>
                        <div class="upload-icon">📹</div>
                        <h3>Segundo Vídeo</h3>
                        <p>Clique para selecionar</p>
                        <input type="file" id="video2" name="video2" accept="video/*" required style="display: none;">
                        <div id="video2-name" class="file-name"></div>
                    </div>
                </div>

                <div class="merge-preview" id="merge-preview" style="display: none;">
                    <div style="display: flex; align-items: center; justify-content: center; flex-wrap: wrap;">
                        <span id="preview-video1">Vídeo 1</span>
                        <span class="merge-arrow">➕</span>
                        <span id="preview-video2">Vídeo 2</span>
                        <span class="merge-arrow">=</span>
                        <span id="preview-output">Vídeo Mesclado</span>
                    </div>
                </div>

                <div class="output-name">
                    <label for="output_name">📝 Nome do arquivo de saída (opcional):</label>
                    <input type="text" id="output_name" name="output_name" 
                           placeholder="video_mesclado.mp4" value="video_mesclado.mp4">
                </div>

                <button type="submit" class="btn btn-primary">🔗 Mesclar Vídeos</button>
                <a href="MergerForm.php" class="btn btn-primary">🔄 Reiniciar</a>
            </form>
        </main>

        <div class="actions">
            <a href="../../index.php" class="btn">← Voltar ao Menu</a>
        </div>

        <footer>
            <p>⚠️ <strong>Dica:</strong> Os vídeos serão unidos na ordem: primeiro vídeo + segundo vídeo.</p>
        </footer>
    </div>

    <script src="../../public/js/merge.js"></script>
</body>
</html>

