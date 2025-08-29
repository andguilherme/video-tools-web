<?php include_once "../handlers/SplitHandler.php" ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✂️ Dividir Vídeos</title>
    <link rel="stylesheet" href="../../public/css/splitterStyle.css">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>✂️ Dividir Vídeos</h1>
            <p>Corte segmentos específicos dos seus vídeos</p>
        </header>

        <main>
            <?php if ($response): ?>
                <div class="result-container <?php echo $response['success'] ? 'success' : 'error'; ?>">
                    <h2><?php echo $response['success'] ? '✅ Sucesso!' : '❌ Erro'; ?></h2>
                    <p><?php echo htmlspecialchars($response['message']); ?></p>

                    <?php if ($response['success'] && $response['data']): ?>
                        <div class="download-result">
                            <p><strong>Segmento:</strong> <?php echo htmlspecialchars($response['data']['start_time']); ?> até
                                <?php echo htmlspecialchars($response['data']['end_time']); ?></p>
                            <p><strong>Arquivo:</strong> <?php echo htmlspecialchars($response['data']['output_file']); ?></p>
                            <a href="<?php echo htmlspecialchars($response['data']['download_url']); ?>"
                                class="btn download-btn" download>
                                📥 Baixar Vídeo Cortado
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="upload-area" onclick="document.getElementById('video_file').click()">
                    <div class="upload-icon">📁</div>
                    <h3>Selecione o vídeo para dividir</h3>
                    <p>Formatos suportados: MP4, AVI, MOV, MKV, etc.</p>
                    <input type="file" id="video_file" name="video_file" accept="video/*" required
                        style="display: none;">
                    <div id="file-name" class="file-name"></div>
                </div>

                <div class="time-help">
                    <h4>⏱️ Formato de Tempo</h4>
                    <p>Use o formato <strong>hh:mm:ss</strong> (horas:minutos:segundos)</p>
                    <div class="time-examples">
                        <div class="time-example">00:00:30</div>
                        <div class="time-example">00:01:15</div>
                        <div class="time-example">00:05:00</div>
                        <div class="time-example">01:30:45</div>
                    </div>
                </div>

                <div class="time-inputs">
                    <div class="time-input">
                        <label for="start_time">⏪ Tempo Inicial</label>
                        <input type="text" id="start_time" name="start_time" placeholder="00:00:00"
                            pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" required>
                    </div>
                    <div class="time-input">
                        <label for="end_time">⏩ Tempo Final</label>
                        <input type="text" id="end_time" name="end_time" placeholder="00:01:00"
                            pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">✂️ Dividir Vídeo</button>
                <a href="SplitterForm.php" class="btn btn-primary">🔄 Reiniciar</a>
            </form>
        </main>

        <div class="actions">
            <a href="../../index.php" class="btn">← Voltar ao Menu</a>
        </div>

        <footer>
            <p>⚠️ <strong>Dica:</strong> Certifique-se de que o tempo final seja maior que o inicial.</p>
        </footer>
    </div>
<script src="../../public/js/splitter.js"></script>
  </body>

</html>