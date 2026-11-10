<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Register employee';
$activePage = 'employees';
$errors = [];
$employee = [
	'employee_code' => '', 'employee_name' => '', 'department' => '', 'designation' => '',
	'joining_date' => '', 'mobile_number' => '', 'salary' => '', 'status' => 'Active',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	foreach ($employee as $field => $default) {
		$employee[$field] = trim((string) ($_POST[$field] ?? $default));
	}
	if ($employee['employee_code'] === '' || strlen($employee['employee_code']) > 50) $errors[] = 'Enter an employee code (up to 50 characters).';
	if ($employee['employee_name'] === '' || strlen($employee['employee_name']) > 100) $errors[] = 'Enter an employee name (up to 100 characters).';
	if ($employee['department'] === '' || strlen($employee['department']) > 100) $errors[] = 'Enter a department (up to 100 characters).';
	if ($employee['designation'] === '' || strlen($employee['designation']) > 100) $errors[] = 'Enter a designation (up to 100 characters).';
	$date = DateTime::createFromFormat('Y-m-d', $employee['joining_date']);
	if (!$date || $date->format('Y-m-d') !== $employee['joining_date']) $errors[] = 'Choose a valid joining date.';
	if ($employee['mobile_number'] === '' || strlen($employee['mobile_number']) > 20) $errors[] = 'Enter a mobile number (up to 20 characters).';
	if (!is_numeric($employee['salary']) || (float) $employee['salary'] <= 0 || (float) $employee['salary'] > 99999999.99) $errors[] = 'Salary must be greater than zero and within the supported range.';
	if (!in_array($employee['status'], ['Active', 'Inactive'], true)) $errors[] = 'Choose a valid employment status.';

	if (!$errors) {
		try {
			$statement = $pdo->prepare('INSERT INTO employees (employee_code, employee_name, department, designation, joining_date, mobile_number, salary, status) VALUES (:employee_code, :employee_name, :department, :designation, :joining_date, :mobile_number, :salary, :status)');
			$statement->execute($employee);
			set_flash('success', 'Employee registered successfully.');
			redirect('employees.php');
		} catch (PDOException $exception) {
			if ($exception->getCode() === '23000') {
				$errors[] = 'That employee code is already in use.';
			} else {
				throw $exception;
			}
		}
	}
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><div class="eyebrow">EMPLOYEE RECORD</div><h1>Register employee</h1><div class="page-subtitle">Add a new person to the company directory.</div></div><a class="btn btn-outline-secondary" href="employees.php"><i class="bi bi-arrow-left me-1"></i> Back to employees</a></section>
<?php if ($errors): ?><div class="alert alert-danger"><strong>Please review the form:</strong><ul class="mb-0 mt-1"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="employee_add.php" class="form-panel">
	<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
	<h2 class="form-section-title">Personal and job details</h2>
	<div class="row g-3">
		<div class="col-md-6"><label class="form-label" for="employee_name">Full name *</label><input class="form-control" id="employee_name" name="employee_name" maxlength="100" required value="<?= e($employee['employee_name']) ?>"></div>
		<div class="col-md-6"><label class="form-label" for="employee_code">Employee code *</label><input class="form-control" id="employee_code" name="employee_code" maxlength="50" required value="<?= e($employee['employee_code']) ?>"></div>
		<div class="col-md-6"><label class="form-label" for="department">Department *</label><input class="form-control" id="department" name="department" maxlength="100" required value="<?= e($employee['department']) ?>" placeholder="e.g. Human Resources"></div>
		<div class="col-md-6"><label class="form-label" for="designation">Designation *</label><input class="form-control" id="designation" name="designation" maxlength="100" required value="<?= e($employee['designation']) ?>"></div>
		<div class="col-md-4"><label class="form-label" for="joining_date">Joining date *</label><input class="form-control" type="date" id="joining_date" name="joining_date" required value="<?= e($employee['joining_date']) ?>"></div>
		<div class="col-md-4"><label class="form-label" for="mobile_number">Mobile number *</label><input class="form-control" id="mobile_number" name="mobile_number" maxlength="20" required value="<?= e($employee['mobile_number']) ?>"></div>
		<div class="col-md-4"><label class="form-label" for="salary">Monthly salary (৳) *</label><input class="form-control" type="number" id="salary" name="salary" min="0.01" max="99999999.99" step="0.01" required value="<?= e($employee['salary']) ?>"></div>
		<div class="col-md-4"><label class="form-label" for="status">Status *</label><select class="form-select" id="status" name="status"><option value="Active" <?= $employee['status'] === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= $employee['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
	</div>
	<div class="d-flex justify-content-end gap-2 mt-4"><a class="btn btn-light" href="employees.php">Cancel</a><button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i> Save employee</button></div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
