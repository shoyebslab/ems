<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Employees';
$activePage = 'employees';
$search = trim((string) ($_GET['q'] ?? ''));
$department = trim((string) ($_GET['department'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));
$departments = $pdo->query('SELECT DISTINCT department FROM employees ORDER BY department')->fetchAll(PDO::FETCH_COLUMN);

$conditions = [];
$params = [];
if ($search !== '') {
	$conditions[] = '(employee_name LIKE :name OR employee_code LIKE :code)';
	$params['name'] = '%' . $search . '%';
	$params['code'] = '%' . $search . '%';
}
if ($department !== '' && in_array($department, $departments, true)) {
	$conditions[] = 'department = :department';
	$params['department'] = $department;
}
if (in_array($status, ['Active', 'Inactive'], true)) {
	$conditions[] = 'status = :status';
	$params['status'] = $status;
}
$sql = 'SELECT id, employee_code, employee_name, department, designation, joining_date, mobile_number, salary, status FROM employees';
if ($conditions) {
	$sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY employee_name';
$statement = $pdo->prepare($sql);
$statement->execute($params);
$employees = $statement->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
	<div><div class="eyebrow">DIRECTORY</div><h1>Employees</h1><div class="page-subtitle">Search, update, and manage employee records.</div></div>
	<a class="btn btn-primary" href="employee_add.php"><i class="bi bi-plus-lg me-1"></i> Register employee</a>
</section>
<section class="panel">
	<form class="filter-bar" method="get" action="employees.php">
		<div class="input-group filter-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-secondary"></i></span><input class="form-control border-start-0 ps-0" type="search" name="q" value="<?= e($search) ?>" placeholder="Name or employee code" aria-label="Search by name or employee code"></div>
		<select class="form-select" name="department" aria-label="Filter by department"><option value="">All departments</option><?php foreach ($departments as $item): ?><option value="<?= e($item) ?>" <?= $department === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select>
		<select class="form-select" name="status" aria-label="Filter by status"><option value="">All statuses</option><option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option></select>
		<button class="btn btn-outline-secondary" type="submit">Filter</button>
		<?php if ($search !== '' || $department !== '' || $status !== ''): ?><a class="btn btn-light" href="employees.php" aria-label="Clear filters"><i class="bi bi-x-lg"></i></a><?php endif; ?>
	</form>
	<div class="panel-heading"><h2><?= number_format(count($employees)) ?> employee<?= count($employees) === 1 ? '' : 's' ?></h2><a class="small text-decoration-none" href="report.php"><i class="bi bi-file-earmark-bar-graph me-1"></i>Payroll report</a></div>
	<?php if ($employees): ?>
		<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Employee</th><th>Department</th><th>Designation</th><th>Joined</th><th>Monthly salary</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
		<?php foreach ($employees as $employee): ?>
			<tr>
				<td><div class="employee-name"><?= e($employee['employee_name']) ?></div><div class="employee-meta"><?= e($employee['employee_code']) ?> · <?= e($employee['mobile_number']) ?></div></td>
				<td><?= e($employee['department']) ?></td><td><?= e($employee['designation']) ?></td><td><?= e(date('M j, Y', strtotime($employee['joining_date']))) ?></td><td>৳<?= number_format((float) $employee['salary'], 2) ?></td>
				<td><span class="status-pill status-<?= strtolower(e($employee['status'])) ?>"><?= e($employee['status']) ?></span></td>
				<td class="text-end"><span class="action-group">
					<a class="btn btn-sm btn-outline-secondary" href="employee_edit.php?id=<?= (int) $employee['id'] ?>" aria-label="Edit <?= e($employee['employee_name']) ?>" title="Edit"><i class="bi bi-pencil"></i></a>
					<form method="post" action="employee_delete.php" onsubmit="return confirm('Permanently delete <?= e(addslashes($employee['employee_name'])) ?>? This cannot be undone and removes the record from reports.');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $employee['id'] ?>"><input type="hidden" name="action" value="delete"><button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete <?= e($employee['employee_name']) ?>" title="Delete permanently"><i class="bi bi-trash3"></i></button></form>
				</span></td>
			</tr>
		<?php endforeach; ?>
		</tbody></table></div>
	<?php else: ?>
		<div class="empty-state">No employees match these filters. <a href="employee_add.php">Register an employee</a> or clear the search.</div>
	<?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
