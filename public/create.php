<?php
/**
 * Add a student (Post/Redirect/Get: refresh never re-submits the form).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Student.php';

$pageTitle = 'Add Student';
$errors = [];
$old = ['nim' => '', 'name' => '', 'email' => '', 'major' => '', 'enrollment_year' => (string) date('Y')];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'nim'              => trim($_POST['nim'] ?? ''),
        'name'             => trim($_POST['name'] ?? ''),
        'email'            => trim($_POST['email'] ?? ''),
        'major'            => trim($_POST['major'] ?? ''),
        'enrollment_year'  => trim($_POST['enrollment_year'] ?? ''),
    ];

    $errors = Student::validate($old);

    if ($errors === []) {
        try {
            Student::create([
                'nim'              => $old['nim'],
                'name'             => $old['name'],
                'email'            => $old['email'],
                'major'            => $old['major'],
                'enrollment_year'  => (int) $old['enrollment_year'],
            ]);
            flash('success', "Student {$old['name']} added.");
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: could not save the student (' . $e->getMessage() . ').';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="h3 mb-3">Add Student</h1>

<?php if ($errors !== []): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="bg-white p-4 rounded border">
    <div class="row g-3">
        <div class="col-md-6">
            <label for="nim" class="form-label">NIM *</label>
            <input type="text" id="nim" name="nim" class="form-control" maxlength="20"
                   required value="<?= e($old['nim']) ?>">
        </div>
        <div class="col-md-6">
            <label for="enrollment_year" class="form-label">Enrollment Year *</label>
            <input type="number" id="enrollment_year" name="enrollment_year" class="form-control"
                   min="2000" max="<?= (int) date('Y') ?>" required value="<?= e($old['enrollment_year']) ?>">
        </div>
        <div class="col-md-12">
            <label for="name" class="form-label">Full Name *</label>
            <input type="text" id="name" name="name" class="form-control" maxlength="100"
                   required value="<?= e($old['name']) ?>">
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email *</label>
            <input type="email" id="email" name="email" class="form-control" maxlength="150"
                   required value="<?= e($old['email']) ?>">
        </div>
        <div class="col-md-6">
            <label for="major" class="form-label">Major *</label>
            <input type="text" id="major" name="major" class="form-control" maxlength="100"
                   required value="<?= e($old['major']) ?>" placeholder="e.g. Informatics Engineering">
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Save Student</button>
        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
