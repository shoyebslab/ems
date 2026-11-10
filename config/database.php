<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$dbHost = getenv('EMS_DB_HOST') ?: '127.0.0.1';
$dbName = getenv('EMS_DB_NAME') ?: 'ems';
$dbUser = getenv('EMS_DB_USER') ?: 'root';
$dbPass = getenv('EMS_DB_PASS') ?: '';

try {
	$pdo = new PDO(
		"mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
		$dbUser,
		$dbPass,
		[
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		]
	);
} catch (PDOException $exception) {
	error_log($exception->getMessage());
	http_response_code(500);
	exit('Database connection failed. Check the EMS database settings and import database.sql.');
}

function e(mixed $value): string
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}

	return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
	$submittedToken = $_POST['csrf_token'] ?? '';
	if (!is_string($submittedToken) || !hash_equals(csrf_token(), $submittedToken)) {
		http_response_code(400);
		exit('Invalid form token. Please go back, refresh the page, and try again.');
	}
}

function set_flash(string $type, string $message): void
{
	$_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function redirect(string $location): never
{
	header('Location: ' . $location);
	exit;
}
