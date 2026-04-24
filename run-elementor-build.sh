#!/usr/bin/env bash
# Roda o bootstrap do usuário admin (opcional) e o script PHP que atualiza Kit, header, footer e homepage.
#
# 1) Crie .env na raiz (veja .env.example) com GREEN_WP_ADMIN_PASS — ou exporte as variáveis no terminal.
# 2) Na pasta do projeto: bash run-elementor-build.sh

set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT"

if [[ -f "$ROOT/.env" ]]; then
	set -a
	# shellcheck source=/dev/null
	source "$ROOT/.env"
	set +a
fi

echo "==> Pasta do projeto: $ROOT"

echo "==> Garantindo que WordPress e banco estão rodando..."
docker compose up -d
sleep 3

WP_ID="$(docker compose ps -q wordpress 2>/dev/null || true)"
if [[ -z "$WP_ID" ]]; then
	echo "ERRO: Não achei o container 'wordpress'. Rode na pasta certa (onde está o docker-compose.yml)."
	exit 1
fi

NETWORK="$(docker inspect "$WP_ID" --format '{{range $k, $v := .NetworkSettings.Networks}}{{$k}} {{end}}' | awk '{print $1}')"
if [[ -z "$NETWORK" ]]; then
	echo "ERRO: Não consegui descobrir a rede Docker do WordPress."
	exit 1
fi

echo "==> Rede Docker usada: $NETWORK"

BUILD_FILE="$ROOT/green-native-elementor-build.php"
BOOTSTRAP_FILE="$ROOT/green-wp-admin-bootstrap.php"
if [[ ! -f "$BUILD_FILE" ]]; then
	echo "ERRO: Arquivo não encontrado: $BUILD_FILE"
	exit 1
fi
if [[ ! -f "$BOOTSTRAP_FILE" ]]; then
	echo "ERRO: Arquivo não encontrado: $BOOTSTRAP_FILE"
	exit 1
fi

WP_USER="${GREEN_WP_ADMIN_USER:-jana}"

echo "==> Bootstrap do usuário WP (cria/atualiza \"$WP_USER\" se GREEN_WP_ADMIN_PASS estiver definida)..."
docker run --rm \
	-v "$ROOT/wordpress:/var/www/html" \
	-v "$BOOTSTRAP_FILE:/var/www/html/green-wp-admin-bootstrap.php" \
	--network "$NETWORK" \
	-e WORDPRESS_DB_HOST=db:3306 \
	-e WORDPRESS_DB_USER=wpuser \
	-e WORDPRESS_DB_PASSWORD=wppass \
	-e WORDPRESS_DB_NAME=wordpress \
	-e GREEN_WP_ADMIN_USER="$WP_USER" \
	-e GREEN_WP_ADMIN_PASS="${GREEN_WP_ADMIN_PASS:-}" \
	-e GREEN_WP_ADMIN_EMAIL="${GREEN_WP_ADMIN_EMAIL:-}" \
	wordpress:cli wp eval-file green-wp-admin-bootstrap.php --allow-root

echo "==> Rodando build Elementor como \"$WP_USER\"..."
docker run --rm \
	-v "$ROOT/wordpress:/var/www/html" \
	-v "$BUILD_FILE:/var/www/html/green-native-elementor-build.php" \
	-v "$ROOT/green-homepage-sections.php:/var/www/html/green-homepage-sections.php" \
	--network "$NETWORK" \
	-e WORDPRESS_DB_HOST=db:3306 \
	-e WORDPRESS_DB_USER=wpuser \
	-e WORDPRESS_DB_PASSWORD=wppass \
	-e WORDPRESS_DB_NAME=wordpress \
	-e GREEN_WP_ADMIN_USER="$WP_USER" \
	wordpress:cli wp eval-file green-native-elementor-build.php --user="$WP_USER" --allow-root

# Porta publicada em docker-compose.yml (wordpress: \"8800:80\"), não 8000.
echo ""
echo "==> Pronto."
echo "    Site: http://localhost:8800/"
echo "    Admin: http://localhost:8800/wp-admin/ (usuário: ${WP_USER})"
echo "    Nota: subir o Docker (docker compose up -d) NÃO reaplica o layout — rode este script de novo quando mudar o .env ou o PHP."
