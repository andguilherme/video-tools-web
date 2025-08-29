# 🎥 Downloader de Vídeos - Exemplo PHP + yt-dlp

Este é um exemplo completo de como criar um formulário HTML que envia URLs para um script PHP que baixa vídeos usando a ferramenta `yt-dlp`.

## 📋 Funcionalidades

- ✅ Interface web amigável e responsiva
- ✅ Download de vídeos de múltiplas plataformas (YouTube, Vimeo, etc.)
- ✅ Opções de formato e qualidade
- ✅ Download apenas de áudio (MP3)
- ✅ Visualização de informações do vídeo sem download
- ✅ Validação de URLs
- ✅ Tratamento de erros
- ✅ Design moderno e responsivo

## 🛠️ Pré-requisitos

### 1. Servidor Web com PHP
- PHP 7.4 ou superior
- Servidor web (Apache, Nginx, ou servidor embutido do PHP)
- Extensões PHP: `json`, `curl`, `exec`

### 2. yt-dlp
O `yt-dlp` deve estar instalado no sistema e acessível via linha de comando.

#### Instalação do yt-dlp:

**Opção 1: Via pip (Python)**
```bash
pip install yt-dlp
```

**Opção 2: Download direto**
```bash
# Linux/macOS
sudo curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp
sudo chmod a+rx /usr/local/bin/yt-dlp

# Windows
# Baixe o executável de: https://github.com/yt-dlp/yt-dlp/releases
```

**Opção 3: Via package manager**
```bash
# Ubuntu/Debian
sudo apt install yt-dlp

# macOS (Homebrew)
brew install yt-dlp

# Arch Linux
sudo pacman -S yt-dlp
```

### 3. Composer (Opcional)
Para usar a biblioteca PHP wrapper:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 🚀 Instalação

### 1. Clone ou baixe os arquivos
```bash
git clone <este-repositorio>
cd youtube-downloader
```

### 2. Instale as dependências PHP (opcional)
```bash
composer install
```

### 3. Configure permissões
```bash
# Crie o diretório de downloads
mkdir downloads
chmod 755 downloads

# Garanta que o PHP pode executar comandos
# (verifique se exec() não está desabilitado no php.ini)
```

### 4. Configure o servidor web

#### Servidor embutido do PHP (para testes)
```bash
php -S localhost:8000
```

#### Apache
Certifique-se de que o módulo `mod_rewrite` está habilitado e configure o DocumentRoot para o diretório do projeto.

#### Nginx
Configure um server block apontando para o diretório do projeto.


## 🎯 Como Usar

1. **Acesse a interface web**
   - Abra `http://localhost:8000` (ou seu domínio)

2. **Insira a URL do vídeo**
   - Cole a URL do YouTube, Vimeo, ou outra plataforma suportada

3. **Configure as opções**
   - Escolha o formato (MP4, MP3, melhor qualidade, etc.)
   - Selecione a qualidade desejada
   - Marque "Apenas áudio" se quiser só o áudio
   - Marque "Apenas informações" para ver dados sem baixar

4. **Clique em "Baixar Vídeo"**
   - O processamento pode levar alguns minutos
   - Os arquivos serão salvos na pasta `downloads/`

## ⚙️ Configurações Avançadas

### Modificar o diretório de download
Edite a variável `$downloadDir` no arquivo `download.php`:
```php
$downloadDir = '/caminho/personalizado/downloads/';
```

### Adicionar mais formatos
Modifique o array de formatos no `download.php` e adicione as opções correspondentes no `index.html`.

### Limitar plataformas suportadas
Edite a função `isValidVideoUrl()` no `download.php` para adicionar ou remover domínios.

## 🔧 Solução de Problemas

### "yt-dlp: command not found"
- Verifique se o yt-dlp está instalado: `yt-dlp --version`
- Adicione o caminho do yt-dlp ao PATH do sistema
- No PHP, use o caminho completo: `/usr/local/bin/yt-dlp`

### "Permission denied"
- Verifique as permissões da pasta downloads: `chmod 755 downloads`
- Certifique-se de que o usuário do servidor web pode escrever na pasta

### "exec() has been disabled"
- Verifique o `php.ini` e remova `exec` da lista `disable_functions`
- Reinicie o servidor web após a alteração

### Downloads muito lentos
- Considere implementar processamento em background
- Use filas de trabalho (como Redis + workers)
- Implemente notificações via WebSocket ou polling

## 🛡️ Considerações de Segurança

### ⚠️ IMPORTANTE: Este é um exemplo educacional

**Para uso em produção, implemente:**

1. **Autenticação e autorização**
2. **Rate limiting** (limite de requisições por IP)
3. **Validação rigorosa de entrada**
4. **Sanitização de nomes de arquivo**
5. **Limite de tamanho de arquivo**
6. **Timeout para downloads longos**
7. **Logs de auditoria**
8. **Isolamento de processos**

### Exemplo de melhorias de segurança:
```php
// Limite de rate por IP
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitFile = "rate_limit_$ip.txt";
// Implementar lógica de rate limiting...

// Timeout para downloads
ini_set('max_execution_time', 300); // 5 minutos máximo

// Validação mais rigorosa
if (!filter_var($url, FILTER_VALIDATE_URL) || 
    !preg_match('/^https?:\/\//', $url)) {
    throw new Exception('URL inválida');
}
```

## 📄 Licença

Este projeto é fornecido como exemplo educacional. Use por sua própria conta e risco.

**Lembre-se de respeitar:**
- Direitos autorais dos conteúdos
- Termos de uso das plataformas
- Leis locais sobre download de conteúdo

## 🤝 Contribuições

Sinta-se à vontade para:
- Reportar bugs
- Sugerir melhorias
- Enviar pull requests
- Compartilhar casos de uso

## 📞 Suporte

Para dúvidas sobre:
- **yt-dlp**: https://github.com/yt-dlp/yt-dlp
- **PHP**: https://www.php.net/docs.php
- **Este exemplo**: Abra uma issue no repositório

---

**⚠️ Aviso Legal:** Este software é fornecido "como está", sem garantias. O uso é de responsabilidade do usuário. Sempre respeite os direitos autorais e termos de serviço das plataformas.

