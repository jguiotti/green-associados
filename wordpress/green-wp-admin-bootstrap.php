<?php
/**
 * Cria ou atualiza o usuário administrador principal (login configurável via ambiente).
 *
 * Segurança: defina a senha só na hora de rodar, nunca commite senhas no Git.
 * Exemplo no Mac (uma linha, na pasta do projeto):
 *   export GREEN_WP_ADMIN_USER=jana
 *   export GREEN_WP_ADMIN_PASS='sua_senha_aqui'
 *   (como no run-elementor-build.sh: monte o ficheiro em /var/www/html/green-wp-admin-bootstrap.php)
 *
 * Com o run-elementor-build.sh, use arquivo .env na raiz (veja comentários no script).
 *
 * @package GreenAssociados
 */

if ( ! defined( 'ABSPATH' ) ) {
	require_once __DIR__ . '/wp-load.php';
}

$login = getenv( 'GREEN_WP_ADMIN_USER' );
if ( ! is_string( $login ) || $login === '' ) {
	$login = 'jana';
}

$pass = getenv( 'GREEN_WP_ADMIN_PASS' );
$email_raw = getenv( 'GREEN_WP_ADMIN_EMAIL' );
$email     = ( is_string( $email_raw ) && $email_raw !== '' )
	? $email_raw
	: $login . '@greenassociados.local';

$user = get_user_by( 'login', $login );

if ( ! $user ) {
	if ( ! is_string( $pass ) || $pass === '' ) {
		echo "ERRO: usuário \"{$login}\" não existe. Defina GREEN_WP_ADMIN_PASS para criar a conta.\n";
		exit( 1 );
	}
	$uid = wp_create_user( $login, $pass, $email );
	if ( is_wp_error( $uid ) ) {
		echo 'ERRO ao criar usuário: ' . $uid->get_error_message() . "\n";
		exit( 1 );
	}
	$user = get_user_by( 'id', $uid );
	echo "OK: usuário \"{$login}\" criado.\n";
} else {
	echo "OK: usuário \"{$login}\" já existe.\n";
}

$user_obj = new WP_User( $user->ID );
$user_obj->set_role( 'administrator' );

if ( is_string( $pass ) && $pass !== '' ) {
	wp_set_password( $pass, $user->ID );
	echo "OK: senha atualizada para \"{$login}\".\n";
} else {
	echo "Aviso: GREEN_WP_ADMIN_PASS vazio — senha não foi alterada.\n";
}

echo "OK: \"{$login}\" é administrador (ID {$user->ID}).\n";
