<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Overview';
$activePage = 'overview';
$summary = $pdo->query(
	"SELECT COUNT(*) AS total,
		SUM(status = 'Active') AS active,
		SUM(status = 'Inactive') AS inactive,
		COALESCE(SUM(CASE WHEN status = 'Active' THEN salary ELSE 0 END), 0) AS monthly_payroll
	 FROM employees"
)->fetch();
$departments = (int) $pdo->query('SELECT COUNT(DISTINCT department) FROM employees')->fetchColumn();
$recentEmployees = $pdo->query('SELECT id, employee_code, employee_name, department, designation, status FROM employees ORDER BY created_at DESC, id DESC LIMIT 6')->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
	<div><div class="eyebrow">EMS OPERATIONS</div><h1>Employee Management System</h1><div class="page-subtitle">A clear view of your employee directory and payroll.</div></div>
	<a class="btn btn-primary" href="employee_add.php"><i class="bi bi-plus-lg me-1"></i> Register employee</a>
</section>

<div class="row g-3 mb-4">
	<div class="col-6 col-xl-3"><div class="metric-card"><div class="d-flex justify-content-between align-items-start"><div class="metric-label">TOTAL EMPLOYEES</div><span class="metric-icon"><i class="bi bi-people"></i></span></div><div class="metric-value"><?= number_format((int) $summary['total']) ?></div><div class="metric-note">Across <?= number_format($departments) ?> departments</div></div></div>
	<div class="col-6 col-xl-3"><div class="metric-card"><div class="d-flex justify-content-between align-items-start"><div class="metric-label">ACTIVE STAFF</div><span class="metric-icon"><i class="bi bi-person-check"></i></span></div><div class="metric-value"><?= number_format((int) $summary['active']) ?></div><div class="metric-note">Currently on the directory</div></div></div>
	<div class="col-6 col-xl-3"><div class="metric-card"><div class="d-flex justify-content-between align-items-start"><div class="metric-label">INACTIVE STAFF</div><span class="metric-icon"><i class="bi bi-person-dash"></i></span></div><div class="metric-value"><?= number_format((int) $summary['inactive']) ?></div><div class="metric-note">Retained for record keeping</div></div></div>
	<div class="col-6 col-xl-3"><div class="metric-card"><div class="d-flex justify-content-between align-items-start"><div class="metric-label">MONTHLY PAYROLL</div><span class="metric-icon"><i class="bi bi-cash-stack"></i></span></div><div class="metric-value">৳<?= number_format((float) $summary['monthly_payroll'], 0) ?></div><div class="metric-note">Active employees only</div></div></div>
</div>

<section class="panel">
	<div class="panel-heading"><div><h2>Recently added</h2><div class="metric-note">Latest employee records</div></div><a class="btn btn-sm btn-outline-secondary" href="employees.php">View directory <i class="bi bi-arrow-right ms-1"></i></a></div>
	<?php if ($recentEmployees): ?>
		<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Employee</th><th>Department</th><th>Designation</th><th>Status</th><th></th></tr></thead><tbody>
		<?php foreach ($recentEmployees as $employee): ?>
			<tr><td><div class="employee-name"><?= e($employee['employee_name']) ?></div><div class="employee-meta"><?= e($employee['employee_code']) ?></div></td><td><?= e($employee['department']) ?></td><td><?= e($employee['designation']) ?></td><td><span class="status-pill status-<?= strtolower(e($employee['status'])) ?>"><?= e($employee['status']) ?></span></td><td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="employee_edit.php?id=<?= (int) $employee['id'] ?>" aria-label="Edit <?= e($employee['employee_name']) ?>"><i class="bi bi-pencil"></i></a></td></tr>
		<?php endforeach; ?>
		</tbody></table></div>
	<?php else: ?>
		<div class="empty-state">No employee records yet. <a href="employee_add.php">Register the first employee</a>.</div>
	<?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
