<?php
require_once __DIR__ . '/config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
	http_response_code(400);
	exit('A valid employee ID is required.');
}
$statement = $pdo->prepare('SELECT * FROM employees WHERE id = :id');
$statement->execute(['id' => $id]);
$employee = $statement->fetch();
if (!$employee) {
	http_response_code(404);
	exit('Employee not found.');
}

$pageTitle = 'Edit employee';
$activePage = 'employees';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	foreach (['employee_code', 'employee_name', 'department', 'designation', 'joining_date', 'mobile_number', 'salary', 'status'] as $field) {
		$employee[$field] = trim((string) ($_POST[$field] ?? ''));
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
			$statement = $pdo->prepare('UPDATE employees SET employee_code = :employee_code, employee_name = :employee_name, department = :department, designation = :designation, joining_date = :joining_date, mobile_number = :mobile_number, salary = :salary, status = :status WHERE id = :id');
			$statement->execute([
				'employee_code' => $employee['employee_code'], 'employee_name' => $employee['employee_name'],
				'department' => $employee['department'], 'designation' => $employee['designation'],
				'joining_date' => $employee['joining_date'], 'mobile_number' => $employee['mobile_number'],
				'salary' => $employee['salary'], 'status' => $employee['status'], 'id' => $id,
			]);
			set_flash('success', 'Employee record updated.');
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
<section class="page-heading"><div><div class="eyebrow">EMPLOYEE RECORD</div><h1>Edit employee</h1><div class="page-subtitle">Update information or change employment status.</div></div><a class="btn btn-outline-secondary" href="employees.php"><i class="bi bi-arrow-left me-1"></i> Back to employees</a></section>
<?php if ($errors): ?><div class="alert alert-danger"><strong>Please review the form:</strong><ul class="mb-0 mt-1"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="employee_edit.php?id=<?= (int) $id ?>" class="form-panel">
	<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
	<h2 class="form-section-title">Personal and job details</h2>
	<div class="row g-3">
		<div class="col-md-6"><label class="form-label" for="employee_name">Full name *</label><input class="form-control" id="employee_name" name="employee_name" maxlength="100" required value="<?= e($employee['employee_name']) ?>"></div>
		<div class="col-md-6"><label class="form-label" for="employee_code">Employee code *</label><input class="form-control" id="employee_code" name="employee_code" maxlength="50" required value="<?= e($employee['employee_code']) ?>"></div>
		<div class="col-md-6"><label class="form-label" for="department">Department *</label><input class="form-control" id="department" name="department" maxlength="100" required value="<?= e($employee['department']) ?>"></div>
		<div class="col-md-6"><label class="form-label" for="designation">Designation *</label><input class="form-control" id="designation" name="designation" maxlength="100" required value="<?= e($employee['designation']) ?>"></div>
		<div class="col-md-4"><label class="form-label" for="joining_date">Joining date *</label><input class="form-control" type="date" id="joining_date" name="joining_date" required value="<?= e($employee['joining_date']) ?>"></div>
		<div class="col-md-4"><label class="form-label" for="mobile_number">Mobile number *</label><input class="form-control" id="mobile_number" name="mobile_number" maxlength="20" required value="<?= e($employee['mobile_number']) ?>"></div>
		<div class="col-md-4"><label class="form-label" for="salary">Monthly salary (৳) *</label><input class="form-control" type="number" id="salary" name="salary" min="0.01" max="99999999.99" step="0.01" required value="<?= e($employee['salary']) ?>"></div>
		<div class="col-md-4"><label class="form-label" for="status">Status *</label><select class="form-select" id="status" name="status"><option value="Active" <?= $employee['status'] === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= $employee['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
	</div>
	<div class="d-flex justify-content-between align-items-center gap-2 mt-4"><span class="text-secondary small">Employee code: <?= e($employee['employee_code']) ?></span><div class="d-flex gap-2"><a class="btn btn-light" href="employees.php">Cancel</a><button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i> Save</button></div></div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
