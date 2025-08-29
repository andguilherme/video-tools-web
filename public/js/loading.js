// Adiciona funcionalidade para mostrar loading no botão
        document.querySelector('.download-form').addEventListener('submit', function () {
            const btn = document.querySelector('.submit-btn');
            const btnText = document.querySelector('.btn-text');
            const btnLoading = document.querySelector('.btn-loading');

            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        });

        // Funcionalidade para desabilitar outros campos quando "apenas áudio" está marcado
        document.getElementById('audio_only').addEventListener('change', function () {
            const qualitySelect = document.getElementById('quality');
            const formatSelect = document.getElementById('format');

            if (this.checked) {
                qualitySelect.disabled = true;
                formatSelect.value = 'mp3';
                formatSelect.disabled = true;
            } else {
                qualitySelect.disabled = false;
                formatSelect.disabled = false;
            }
        });

        // Funcionalidade para desabilitar campos quando "apenas informações" está marcado
        document.getElementById('get_info').addEventListener('change', function () {
            const audioOnly = document.getElementById('audio_only');
            const qualitySelect = document.getElementById('quality');
            const formatSelect = document.getElementById('format');

            if (this.checked) {
                audioOnly.disabled = true;
                qualitySelect.disabled = true;
                formatSelect.disabled = true;
                document.querySelector('.submit-btn .btn-text').textContent = 'Obter Informações';
            } else {
                audioOnly.disabled = false;
                qualitySelect.disabled = false;
                formatSelect.disabled = false;
                document.querySelector('.submit-btn .btn-text').textContent = 'Baixar Vídeo';
            }
        });