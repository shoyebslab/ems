<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Payroll report';
$activePage = 'reports';
$department = trim((string) ($_GET['department'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));
$departments = $pdo->query('SELECT DISTINCT department FROM employees ORDER BY department')->fetchAll(PDO::FETCH_COLUMN);
$conditions = [];
$params = [];
if ($department !== '' && in_array($department, $departments, true)) {
	$conditions[] = 'department = :department';
	$params['department'] = $department;
}
if (in_array($status, ['Active', 'Inactive'], true)) {
	$conditions[] = 'status = :status';
	$params['status'] = $status;
}
$where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
$statement = $pdo->prepare('SELECT COUNT(*) AS headcount, COALESCE(SUM(salary), 0) AS monthly_total, COALESCE(SUM(salary * 12), 0) AS annual_total, COALESCE(AVG(salary), 0) AS average_salary FROM employees' . $where);
$statement->execute($params);
$totals = $statement->fetch();
$statement = $pdo->prepare('SELECT employee_code, employee_name, department, designation, joining_date, salary, status FROM employees' . $where . ' ORDER BY department, employee_name');
$statement->execute($params);
$employees = $statement->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading no-print">
	<div><div class="eyebrow">PEOPLE ANALYTICS</div><h1>Payroll report</h1><div class="page-subtitle">Monthly and annual salary totals for your employee records.</div></div>
	<button class="btn btn-primary" type="button" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print report</button>
</section>
<div class="print-only mb-4"><div class="eyebrow"> EMS</div><h1>Employee payroll report</h1><div class="page-subtitle">Generated <?= e(date('F j, Y')) ?></div></div>
<section class="panel mb-3 no-print">
	<form class="filter-bar" method="get" action="report.php">
		<select class="form-select" name="department" aria-label="Filter report by department"><option value="">All departments</option><?php foreach ($departments as $item): ?><option value="<?= e($item) ?>" <?= $department === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select>
		<select class="form-select" name="status" aria-label="Filter report by status"><option value="">All statuses</option><option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option></select>
		<button class="btn btn-outline-secondary" type="submit">Apply filters</button>
		<?php if ($department !== '' || $status !== ''): ?><a class="btn btn-light" href="report.php">Clear</a><?php endif; ?>
	</form>
</section>
<div class="row g-3 mb-3">
	<div class="col-md-4"><div class="report-total"><div class="label">Employees in report</div><strong><?= number_format((int) $totals['headcount']) ?></strong></div></div>
	<div class="col-md-4"><div class="report-total"><div class="label">Monthly salary total</div><strong>৳<?= number_format((float) $totals['monthly_total'], 2) ?></strong></div></div>
	<div class="col-md-4"><div class="report-total"><div class="label">Annual salary total</div><strong>৳<?= number_format((float) $totals['annual_total'], 2) ?></strong><div class="metric-note">Average monthly salary: ৳<?= number_format((float) $totals['average_salary'], 2) ?></div></div></div>
</div>
<section class="panel">
	<div class="panel-heading"><h2>Salary by employee</h2><span class="metric-note">Annual salary = monthly salary × 12</span></div>
	<?php if ($employees): ?><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Employee</th><th>Department</th><th>Designation</th><th>Status</th><th class="text-end">Monthly salary</th><th class="text-end">Annual salary</th></tr></thead><tbody>
		<?php foreach ($employees as $employee): ?><tr><td><div class="employee-name"><?= e($employee['employee_name']) ?></div><div class="employee-meta"><?= e($employee['employee_code']) ?> · Joined <?= e(date('M Y', strtotime($employee['joining_date']))) ?></div></td><td><?= e($employee['department']) ?></td><td><?= e($employee['designation']) ?></td><td><span class="status-pill status-<?= strtolower(e($employee['status'])) ?>"><?= e($employee['status']) ?></span></td><td class="text-end">৳<?= number_format((float) $employee['salary'], 2) ?></td><td class="text-end">৳<?= number_format((float) $employee['salary'] * 12, 2) ?></td></tr><?php endforeach; ?>
		</tbody><tfoot><tr class="fw-bold"><td colspan="4">Total</td><td class="text-end">৳<?= number_format((float) $totals['monthly_total'], 2) ?></td><td class="text-end">৳<?= number_format((float) $totals['annual_total'], 2) ?></td></tr></tfoot></table></div><?php else: ?><div class="empty-state">No employees match the selected report filters.</div><?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
