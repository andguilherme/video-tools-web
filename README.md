# 🎥 Video Tools Web

Este é um conjunto de ferramentas web para manipulação de vídeos, incluindo conversão, divisão, mesclagem e download de vídeos do YouTube. O projeto é construído em PHP e utiliza `ffmpeg` e `yt-dlp` para o processamento de vídeo.

## 🚀 Funcionalidades

- **Converter Vídeos:** Converta vídeos para GIF animado ou extraia o áudio em formato MP3.
- **Dividir Vídeos:** Corte segmentos específicos de seus vídeos, definindo o tempo de início e fim.
- **Mesclar Vídeos:** Una dois vídeos em um único arquivo.
- **Download YouTube:** Baixe vídeos e áudios de diversas plataformas, incluindo YouTube, Vimeo, Dailymotion, Twitch, Facebook, Instagram e TikTok.

## 🛠️ Pré-requisitos

Para rodar esta aplicação, você precisará de um ambiente de servidor web com PHP e as seguintes ferramentas de linha de comando:

- **PHP (versão 7.4 ou superior):** Linguagem de programação para o backend.
- **Apache2 ou Nginx:** Servidor web para hospedar a aplicação.
- **FFmpeg:** Ferramenta de linha de comando para manipulação de áudio e vídeo.
- **yt-dlp:** Ferramenta de linha de comando para download de vídeos de diversas plataformas.

## 📦 Instalação e Configuração

Este projeto inclui um script de instalação (`install.sh`) que automatiza a configuração do ambiente em sistemas baseados em Debian/Ubuntu.

### Usando o `install.sh`

1.  **Clone o repositório:**
    ```bash
    git clone <URL_DO_SEU_REPOSITORIO>
    cd video-tools-web
    ```

2.  **Dê permissão de execução ao script:**
    ```bash
    chmod +x install.sh
    ```

3.  **Execute o script com privilégios de superusuário:**
    ```bash
    sudo ./install.sh
    ```
    O script irá:
    -   Atualizar a lista de pacotes do sistema.
    -   Instalar Apache, PHP (com `mbstring` e `json`), FFmpeg e `curl`.
    -   Baixar e instalar o `yt-dlp` em `/usr/local/bin/`.
    -   Criar as pastas `uploads/` e `outputs/` dentro do diretório do projeto.
    -   Configurar permissões adequadas para as pastas `uploads/` e `outputs/` para o usuário do Apache (`www-data`) e o usuário que executou o `sudo`.
    -   Perguntar se você deseja criar um link simbólico ou copiar a pasta do projeto para o diretório `/var/www/html/` do Apache.
    -   Habilitar o módulo `mod_rewrite` do Apache e reiniciar o serviço.

4.  **Acesse a aplicação:**
    Após a conclusão do script, você poderá acessar a aplicação no seu navegador através de:
    ```
    http://localhost/video-tools-web/
    ```
    (Se você clonou o repositório com um nome diferente, ajuste a URL de acordo).

    **Importante:** Se você executou o script com `sudo`, pode ser necessário **sair e logar novamente** no seu terminal para que as novas permissões de grupo tenham efeito.

### Instalação Manual (para outros sistemas ou configurações personalizadas)

1.  **Instale PHP, Apache/Nginx, FFmpeg e yt-dlp:**
    Certifique-se de que todas as dependências listadas em "Pré-requisitos" estão instaladas e configuradas corretamente em seu sistema.

2.  **Configure seu servidor web:**
    Configure seu servidor web (Apache ou Nginx) para servir os arquivos PHP do diretório do projeto.

3.  **Crie as pastas de upload e saída:**
    Crie as pastas `uploads/` e `outputs/` na raiz do projeto e certifique-se de que o usuário do servidor web (ex: `www-data` para Apache) tenha permissões de leitura e escrita nessas pastas.

    ```bash
    mkdir uploads outputs
    sudo chown -R www-data:www-data uploads outputs
    sudo chmod -R 775 uploads outputs
    ```

4.  **Ajuste as configurações do PHP (opcional, mas recomendado para arquivos grandes):**
    Edite seu `php.ini` (geralmente em `/etc/php/<versao>/apache2/php.ini` ou `/etc/php/<versao>/fpm/php.ini`) e aumente os valores de `upload_max_filesize` e `post_max_size`.

    ```ini
    upload_max_filesize = 512M
    post_max_size = 512M
    ```
    Após as alterações, reinicie seu servidor web.


**Aviso:** Respeite os direitos autorais e termos de uso das plataformas ao baixar e manipular conteúdo de vídeo.
