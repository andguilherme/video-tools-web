#!/bin/bash

# ==============================================================================
#  Instalador e Deployer Automático para "Video Tools Web"
# ==============================================================================
#
#  Este script automatiza a instalação do ambiente e o deploy local do projeto
#  em um sistema baseado em Debian/Ubuntu.
#
#  COMO USAR:
#  1. Dê permissão de execução ao script: chmod +x install.sh
#  2. Execute o script com privilégios de superusuário: sudo ./install.sh
#
# ==============================================================================

# --- Cores e Funções ---
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

function print_message() {
    local color=$1
    local message=$2
    echo -e "${color}${message}${NC}"
}

# --- Verificação Inicial ---
if [ "$EUID" -ne 0 ]; then
  print_message $RED "ERRO: Por favor, execute este script com 'sudo' (ex: sudo ./install.sh)"
  exit 1
fi

print_message $GREEN "=== Iniciando a Instalação do Ambiente para Video Tools Web ==="

# --- PASSO 1: Atualizar Pacotes ---
print_message $YELLOW "-> Atualizando a lista de pacotes do sistema..."
apt-get update -y || { print_message $RED "Falha ao atualizar pacotes."; exit 1; }

# --- PASSO 2: Instalar Dependências Essenciais ---
print_message $YELLOW "-> Instalando Apache, PHP e extensões..."
apt-get install -y apache2 php libapache2-mod-php php-mbstring php-json ffmpeg curl || { print_message $RED "Falha ao instalar pacotes essenciais."; exit 1; }

# --- PASSO 3: Instalar yt-dlp ---
print_message $YELLOW "-> Instalando ou atualizando o yt-dlp..."
curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp || { print_message $RED "Falha ao baixar o yt-dlp."; exit 1; }
chmod a+rx /usr/local/bin/yt-dlp
print_message $GREEN "yt-dlp instalado com sucesso!"

# --- PASSO 4: Configurar Permissões do Projeto ---
PROJECT_DIR=$(pwd )
PROJECT_NAME=$(basename "$PROJECT_DIR")
UPLOADS_DIR="${PROJECT_DIR}/uploads"
OUTPUTS_DIR="${PROJECT_DIR}/public/outputs"
DOWNLOAD_DIR="${PROJECT_DIR}/src/utils/video_downloader/outputs"
APACHE_USER="www-data"
SUDO_USER_NAME=${SUDO_USER:-$(whoami)}

print_message $YELLOW "-> Configurando permissões das pastas do projeto..."
mkdir -p $UPLOADS_DIR
mkdir -p $OUTPUTS_DIR
mkdir -p $DOWNLOAD_DIR

print_message $YELLOW "-> Adicionando o usuário '${SUDO_USER_NAME}' ao grupo '${APACHE_USER}'..."
usermod -a -G $APACHE_USER $SUDO_USER_NAME

chown -R $SUDO_USER_NAME:$APACHE_USER $UPLOADS_DIR
chown -R $SUDO_USER_NAME:$APACHE_USER $OUTPUTS_DIR
chown -R $SUDO_USER_NAME:$APACHE_USER $DOWNLOAD_DIR

print_message $YELLOW "-> Aplicando permissões 2775 (SGID) para colaboração..."
chmod -R 2775 $UPLOADS_DIR
chmod -R 2775 $OUTPUTS_DIR
chmod -R 2775 $DOWNLOAD_DIR

# --- PASSO 5: Deploy do Projeto no Apache ---
APACHE_ROOT="/var/www/html"
DESTINATION_PATH="${APACHE_ROOT}/${PROJECT_NAME}"

print_message $BLUE "\nComo você deseja colocar o projeto no servidor Apache?"
print_message $NC "1) Criar um LINK SIMBÓLICO (Recomendado para desenvolvimento)."
print_message $NC "2) COPIAR a pasta (Simula um ambiente de produção)."
read -p "Escolha uma opção (1 ou 2): " deploy_choice

if [ -d "$DESTINATION_PATH" ] || [ -L "$DESTINATION_PATH" ]; then
    print_message $YELLOW "-> Removendo instalação anterior em ${DESTINATION_PATH}..."
    rm -rf "$DESTINATION_PATH"
fi

case $deploy_choice in
    1)
        print_message $YELLOW "-> Criando link simbólico de '${PROJECT_DIR}' para '${DESTINATION_PATH}'..."
        ln -s "$PROJECT_DIR" "$DESTINATION_PATH"
        print_message $GREEN "Link simbólico criado!"
        ;;
    2)
        print_message $YELLOW "-> Copiando a pasta do projeto para '${APACHE_ROOT}'..."
        cp -r "$PROJECT_DIR" "$APACHE_ROOT"
        print_message $GREEN "Projeto copiado!"
        ;;
    *)
        print_message $RED "Opção inválida. O deploy foi pulado. Você pode fazer isso manually."
        ;;
esac

# --- PASSO 6: Habilitar Módulos do Apache e Reiniciar ---
print_message $YELLOW "\n-> Habilitando mod_rewrite e reiniciando o Apache..."
a2enmod rewrite
systemctl restart apache2 || { print_message $RED "Falha ao reiniciar o Apache."; exit 1; }

# --- Conclusão ---
print_message $GREEN "\n✅ Instalação e configuração concluídas com sucesso!"
print_message $NC "=============================================================="
print_message $NC "  Acesse seu projeto em:"
print_message $BLUE "  http://localhost/${PROJECT_NAME}/"
print_message $NC "\n  IMPORTANTE: Você precisa SAIR e LOGAR NOVAMENTE no seu terminal"
print_message $NC "  para que a sua nova associação ao grupo '${APACHE_USER}' tenha efeito."
print_message $NC "=============================================================="
