        // Manipulação do upload de arquivo
        const fileInput = document.getElementById('video_file');
        const uploadArea = document.querySelector('.upload-area');
        const fileName = document.getElementById('file-name');

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = `Arquivo selecionado: ${this.files[0].name}`;
                fileName.style.display = 'block';
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
                fileName.textContent = `Arquivo selecionado: ${files[0].name}`;
                fileName.style.display = 'block';
            }
        });

        // Seleção de formato
        document.querySelectorAll('.format-option').forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });
    