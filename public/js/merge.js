  // Manipulação dos uploads de arquivo
        function setupFileInput(inputId, uploadAreaClass, fileNameId, previewId) {
            const fileInput = document.getElementById(inputId);
            const uploadArea = document.querySelector(`.${uploadAreaClass}`);
            const fileName = document.getElementById(fileNameId);
            const preview = document.getElementById(previewId);

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileName.textContent = `✅ ${this.files[0].name}`;
                    fileName.style.display = 'block';
                    uploadArea.classList.add('has-file');
                    if (preview) preview.textContent = this.files[0].name;
                    updatePreview();
                }
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    fileName.textContent = `✅ ${files[0].name}`;
                    fileName.style.display = 'block';
                    this.classList.add('has-file');
                    if (preview) preview.textContent = files[0].name;
                    updatePreview();
                }
            });
        }

        // Configura os dois inputs de arquivo
        setupFileInput('video1', 'video-upload', 'video1-name', 'preview-video1');
        setupFileInput('video2', 'video-upload', 'video2-name', 'preview-video2');

        // Atualiza o preview da mesclagem
        function updatePreview() {
            const video1 = document.getElementById('video1').files[0];
            const video2 = document.getElementById('video2').files[0];
            const preview = document.getElementById('merge-preview');
            const outputName = document.getElementById('output_name');

            if (video1 && video2) {
                preview.style.display = 'block';
                document.getElementById('preview-output').textContent = outputName.value || 'video_mesclado.mp4';
            }
        }

        // Atualiza o preview quando o nome de saída muda
        document.getElementById('output_name').addEventListener('input', updatePreview);