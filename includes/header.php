<?php
$pageTitle = $pageTitle ?? 'Overview';
$activePage = $activePage ?? '';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= e($pageTitle) ?> | EMS</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
	<link href="assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">
	<aside class="sidebar">
		<a class="brand" href="index.php"><span class="brand-mark"><i class="bi bi-people-fill"></i></span><span><span class="brand-light">EMS</span><small>EMPLOYEE SYSTEM</small></span></a>
		<div class="sidebar-label">WORKSPACE</div>
		<nav class="nav flex-column">
			<a class="nav-link <?= $activePage === 'overview' ? 'active' : '' ?>" href="index.php"><i class="bi bi-grid-1x2"></i>Overview</a>
			<a class="nav-link <?= $activePage === 'employees' ? 'active' : '' ?>" href="employees.php"><i class="bi bi-person-vcard"></i>Employees</a>
			<a class="nav-link <?= $activePage === 'reports' ? 'active' : '' ?>" href="report.php"><i class="bi bi-bar-chart-line"></i>Reports</a>
		</nav>
		<div class="sidebar-bottom"><span class="online-dot"></span> Staff directory <span class="sidebar-version">EMS</span></div>
	</aside>
	<main class="main-panel">
		<header class="topbar">
			<button class="btn menu-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Open navigation"><i class="bi bi-list"></i></button>
			<div class="topbar-title"><?= e($pageTitle) ?></div>
		</header>
		<div class="offcanvas offcanvas-start mobile-nav" tabindex="-1" id="mobileNav" aria-label="Main navigation">
			<div class="offcanvas-header"><a class="brand" href="index.php"><span class="brand-mark"><i class="bi bi-people-fill"></i></span><span>people<span class="brand-light">desk</span></span></a><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
			<div class="offcanvas-body"><nav class="nav flex-column">
				<a class="nav-link <?= $activePage === 'overview' ? 'active' : '' ?>" href="index.php"><i class="bi bi-grid-1x2"></i>Overview</a>
				<a class="nav-link <?= $activePage === 'employees' ? 'active' : '' ?>" href="employees.php"><i class="bi bi-person-vcard"></i>Employees</a>
				<a class="nav-link <?= $activePage === 'reports' ? 'active' : '' ?>" href="report.php"><i class="bi bi-bar-chart-line"></i>Reports</a>
			</nav></div>
		</div>
		<div class="content-area">
			<?php if ($flash): ?>
				<div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
					<?= e($flash['message']) ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			<?php endif; ?>
