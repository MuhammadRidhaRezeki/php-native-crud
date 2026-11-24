<?php
/**
 * Delete a student. POST only — a GET link can never delete anything.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Student.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$student = $id > 0 ? Student::find($id) : null;

if ($student === null) {
    flash('danger', 'Student not found.');
} else {
    try {
        Student::delete($id);
        flash('success', "Student {$student['name']} deleted.");
    } catch (PDOException $e) {
        flash('danger', 'Database error: could not delete the student.');
    }
}

header('Location: index.php');
exit;
