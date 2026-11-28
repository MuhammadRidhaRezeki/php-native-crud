<?php
/**
 * List students (+ search by name or NIM).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Student.php';

$pageTitle = 'Students';
$search = trim($_GET['q'] ?? '');
$filter = $search === '' ? null : $search;

$perPage    = 10;
$total      = Student::count($filter);
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min(max(1, (int) ($_GET['page'] ?? 1)), $totalPages);
$students   = Student::all($filter, $page, $perPage);
$firstShown = $total === 0 ? 0 : ($page - 1) * $perPage + 1;
$lastShown  = min($total, $page * $perPage);

/** Link to a list page, keeping the current search query. */
$pageUrl = static function (int $p) use ($search): string {
    $params = ['page' => $p];
    if ($search !== '') {
        $params['q'] = $search;
    }

    return 'index.php?' . http_build_query($params);
};

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Students <small class="text-muted">(<?= $total ?>)</small></h1>
    <a href="create.php" class="btn btn-primary">+ Add Student</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-sm-8 col-md-6">
        <input type="search" name="q" class="form-control"
               placeholder="Search by name or NIM…" value="<?= e($search) ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
        <?php if ($search !== ''): ?>
            <a href="index.php" class="btn btn-link">Clear</a>
        <?php endif; ?>
    </div>
</form>

<?php if ($students === []): ?>
    <div class="alert alert-info">
        No students found<?= $search !== '' ? ' for “' . e($search) . '”' : '' ?>.
        <a href="create.php">Add the first one</a>.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>NIM</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Major</th>
                    <th>Year</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= (int) $student['id'] ?></td>
                        <td><?= e($student['nim']) ?></td>
                        <td><?= e($student['name']) ?></td>
                        <td><?= e($student['email']) ?></td>
                        <td><?= e($student['major']) ?></td>
                        <td><?= (int) $student['enrollment_year'] ?></td>
                        <td class="text-end text-nowrap">
                            <a href="edit.php?id=<?= (int) $student['id'] ?>"
                               class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="post" action="delete.php" class="d-inline"
                                  onsubmit="return confirm('Delete <?= e(addslashes($student['name'])) ?>? This cannot be undone.');">
                                <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ($totalPages > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <p class="text-muted mb-0">
            Showing <?= $firstShown ?>–<?= $lastShown ?> of <?= $total ?> students
        </p>
        <nav aria-label="Student list pages">
            <ul class="pagination mb-0">
                <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                    <a class="page-link" href="<?= $page <= 1 ? '#' : e($pageUrl($page - 1)) ?>"
                       aria-label="Previous page">&laquo;</a>
                </li>
                <?php
                // Numbered pages: always show first/last, plus a window around the current page.
                $shown = [];
                foreach (array_merge([1, $totalPages], range($page - 2, $page + 2)) as $p) {
                    if ($p >= 1 && $p <= $totalPages) {
                        $shown[$p] = true;
                    }
                }
                ksort($shown);
                $prev = 0;
                foreach (array_keys($shown) as $p):
                    if ($prev !== 0 && $p - $prev > 1): ?>
                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                    <?php endif; ?>
                    <li class="page-item<?= $p === $page ? ' active' : '' ?>">
                        <a class="page-link" href="<?= e($pageUrl($p)) ?>"><?= $p ?></a>
                    </li>
                <?php $prev = $p; endforeach; ?>
                <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                    <a class="page-link" href="<?= $page >= $totalPages ? '#' : e($pageUrl($page + 1)) ?>"
                       aria-label="Next page">&raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
