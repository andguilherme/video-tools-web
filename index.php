<?php
// index.php - Menu principal do Conversor de Vídeo Integrado
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎥 Vídeo Tools</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/mainStyle.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎥 Vídeo Tools</h1>
            <p>Todas as ferramentas de vídeo em um só lugar</p>
        </div>

        <div class="menu-grid">
            <a href="src/templates/ConverterForm.php" class="menu-item converter">
                <div class="menu-icon">🔄</div>
                <h3 class="menu-title">Converter Vídeos</h3>
                <p class="menu-description">Converta vídeos para GIF ou extraia áudio em MP3</p>
            </a>

            <a href="src/templates/SplitterForm.php" class="menu-item splitter">
                <div class="menu-icon">✂️</div>
                <h3 class="menu-title">Dividir Vídeos</h3>
                <p class="menu-description">Corte segmentos específicos dos seus vídeos</p>
            </a>

            <a href="src/templates/MergerForm.php" class="menu-item merger">
                <div class="menu-icon">🔗</div>
                <h3 class="menu-title">Mesclar Vídeos</h3>
                <p class="menu-description">Una dois ou mais vídeos em um só arquivo</p>
            </a>

            <a href="src/utils/video_downloader/downloader.html" class="menu-item downloader">
                <div class="menu-icon">📥</div>
                <h3 class="menu-title">Download YouTube</h3>
                <p class="menu-description">Baixe vídeos e áudios do YouTube</p>
            </a>
        </div>

        <div class="footer">
            <p>⚠️ <strong>Aviso:</strong> Respeite os direitos autorais e termos de uso das plataformas.</p>
        </div>
    </div>
</body>
</html>

