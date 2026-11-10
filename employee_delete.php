<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	header('Allow: POST');
	exit('Method not allowed.');
}
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
	http_response_code(400);
	exit('A valid employee ID is required.');
}


if (($_POST['action'] ?? '') !== 'delete') {
	http_response_code(400);
	exit('Invalid employee action.');
}
$statement = $pdo->prepare('DELETE FROM employees WHERE id = :id');
$statement->execute(['id' => $id]);
set_flash($statement->rowCount() ? 'success' : 'warning', $statement->rowCount() ? 'Employee permanently deleted.' : 'Employee not found.');
redirect('employees.php');
