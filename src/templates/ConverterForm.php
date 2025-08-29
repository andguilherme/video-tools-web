<?php include_once "../handlers/ConverterHandler.php" ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔄 Converter Vídeos</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/converterStyle.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>🔄 Converter Vídeos</h1>
            <p>Converta seus vídeos para GIF ou extraia o áudio em MP3</p>
        </header>

        <main>
            <?php if ($response): ?>
                <div class="result-container <?php echo $response['success'] ? 'success' : 'error'; ?>">
                    <h2><?php echo $response['success'] ? '✅ Sucesso!' : '❌ Erro'; ?></h2>
                    <p><?php echo htmlspecialchars($response['message']); ?></p>

                    <?php if ($response['success'] && $response['data']): ?>
                        <div class="download-result">
                            <p><strong>Arquivo convertido:</strong>
                                <?php echo htmlspecialchars($response['data']['output_file']); ?></p>
                            <a href="<?php echo htmlspecialchars($response['data']['download_url']); ?>"
                                class="btn download-btn" download>
                                📥 Baixar <?php echo strtoupper($response['data']['format']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="upload-area" onclick="document.getElementById('video_file').click()">
                    <div class="upload-icon">📁</div>
                    <h3>Clique aqui ou arraste seu vídeo</h3>
                    <p>Formatos suportados: MP4, AVI, MOV, MKV, etc.</p>
                    <input type="file" id="video_file" name="video_file" accept="video/*" required
                        style="display: none;">
                    <div id="file-name" class="file-name"></div>
                </div>

                <div class="format-options">
                    <div class="format-option">
                        <input type="radio" id="gif" name="format" value="gif" required>
                        <label for="gif">
                            <div style="font-size: 2em;">🎞️</div>
                            <strong>GIF Animado</strong>
                            <p>Converte para GIF com alta qualidade</p>
                        </label>
                    </div>
                    <div class="format-option">
                        <input type="radio" id="mp3" name="format" value="mp3" required>
                        <label for="mp3">
                            <div style="font-size: 2em;">🎵</div>
                            <strong>Áudio MP3</strong>
                            <p>Extrai apenas o áudio em MP3</p>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">🔄 Converter Vídeo</button>
                <a href="ConverterForm.php" class="btn btn-primary">🔄 Reiniciar</a>
            </form>
        </main>

        <div class="actions">
            <a href="../../index.php" class="btn">← Voltar ao Menu</a>
        </div>

        <script src="../../public/js/converter.js"></script>

        <footer>
            <p>⚠️ <strong>Aviso:</strong> Arquivos são processados localmente e removidos após a conversão.</p>
        </footer>
    </div>
</body>

</html>