<?php
/**
 * List students (+ search by name or NIM).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Student.php';

$pageTitle = 'Students';
$search = trim($_GET['q'] ?? '');
$students = Student::all($search === '' ? null : $search);
$total = Student::count();

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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
