<?php
/**
 * Student model — the only place that talks to the `students` table.
 *
 * Every query uses a prepared statement with bound parameters.
 */

declare(strict_types=1);

final class Student
{
    /**
     * One page of students, newest first. Optional $search filters by name
     * or NIM. $page is 1-based; $perPage is clamped to 1–100.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(?string $search = null, int $page = 1, int $perPage = 10): array
    {
        $page    = max(1, $page);
        $perPage = min(100, max(1, $perPage));
        $offset  = ($page - 1) * $perPage;

        $sql = 'SELECT * FROM students';

        if ($search !== null && $search !== '') {
            $sql .= ' WHERE name LIKE :search OR nim LIKE :search';
        }

        $sql .= ' ORDER BY id DESC LIMIT :limit OFFSET :offset';

        $stmt = db()->prepare($sql);

        if ($search !== null && $search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM students WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @param array{nim: string, name: string, email: string, major: string, enrollment_year: int} $data
     */
    public static function create(array $data): int
    {
        $stmt = db()->prepare(
            'INSERT INTO students (nim, name, email, major, enrollment_year)
             VALUES (:nim, :name, :email, :major, :enrollment_year)'
        );

        $stmt->execute([
            ':nim'             => $data['nim'],
            ':name'            => $data['name'],
            ':email'           => $data['email'],
            ':major'           => $data['major'],
            ':enrollment_year' => $data['enrollment_year'],
        ]);

        return (int) db()->lastInsertId();
    }

    /**
     * @param array{nim: string, name: string, email: string, major: string, enrollment_year: int} $data
     */
    public static function update(int $id, array $data): bool
    {
        $stmt = db()->prepare(
            'UPDATE students
             SET nim = :nim, name = :name, email = :email,
                 major = :major, enrollment_year = :enrollment_year
             WHERE id = :id'
        );

        return $stmt->execute([
            ':nim'             => $data['nim'],
            ':name'            => $data['name'],
            ':email'           => $data['email'],
            ':major'           => $data['major'],
            ':enrollment_year' => $data['enrollment_year'],
            ':id'              => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $stmt = db()->prepare('DELETE FROM students WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Number of students, optionally filtered by the same $search as all().
     */
    public static function count(?string $search = null): int
    {
        $sql = 'SELECT COUNT(*) FROM students';

        if ($search !== null && $search !== '') {
            $sql .= ' WHERE name LIKE :search OR nim LIKE :search';
            $stmt = db()->prepare($sql);
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            $stmt->execute();

            return (int) $stmt->fetchColumn();
        }

        return (int) db()->query($sql)->fetchColumn();
    }

    /**
     * Validate form input. Returns an array of error messages (empty = valid).
     *
     * @param array<string, string> $data
     * @return string[]
     */
    public static function validate(array $data, ?int $ignoreId = null): array
    {
        $errors = [];

        $nim   = trim($data['nim'] ?? '');
        $name  = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $major = trim($data['major'] ?? '');
        $year  = (int) ($data['enrollment_year'] ?? 0);

        if ($nim === '') {
            $errors[] = 'NIM is required.';
        } elseif (strlen($nim) > 20) {
            $errors[] = 'NIM must be at most 20 characters.';
        }

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email is not a valid email address.';
        }

        if ($major === '') {
            $errors[] = 'Major is required.';
        }

        $currentYear = (int) date('Y');
        if ($year < 2000 || $year > $currentYear) {
            $errors[] = "Enrollment year must be between 2000 and {$currentYear}.";
        }

        // Uniqueness (skip the record being edited).
        if ($errors === []) {
            $sql = 'SELECT id FROM students WHERE (nim = :nim OR email = :email)';
            if ($ignoreId !== null) {
                $sql .= ' AND id <> :ignore_id';
            }
            $stmt = db()->prepare($sql);
            $params = [':nim' => $nim, ':email' => $email];
            if ($ignoreId !== null) {
                $params[':ignore_id'] = $ignoreId;
            }
            $stmt->execute($params);
            if ($stmt->fetch() !== false) {
                $errors[] = 'NIM or email is already used by another student.';
            }
        }

        return $errors;
    }
}
