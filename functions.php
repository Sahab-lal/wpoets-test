<?php

require __DIR__ . '/db.php';

function save_upload(string $field, string $destDir, array $allowedExts): ?string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed for ' . $field . '.');
    }

    $originalName = $_FILES[$field]['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExts, true)) {
        throw new RuntimeException('Invalid file type for ' . $field . '.');
    }

    $baseName = strtolower(pathinfo($originalName, PATHINFO_FILENAME));
    $baseName = preg_replace('/[^a-z0-9_-]/', '-', $baseName);
    $baseName = trim($baseName, '-') ?: 'file';
    $fileName = $baseName . '-' . uniqid('', true) . '.' . $extension;

    $targetDir = __DIR__ . '/' . $destDir;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
        throw new RuntimeException('Failed to create upload directory.');
    }

    $targetPath = $targetDir . '/' . $fileName;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath)) {
        throw new RuntimeException('Could not save uploaded file.');
    }

    return $destDir . '/' . $fileName;
}

function create_slide(mysqli $mysqli, array $payload): void
{
    $stmt = $mysqli->prepare('INSERT INTO slides (tab_key, tab_label, slide_title, slide_body, image_path, icon_path, position) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param(
        'ssssssi',
        $payload['tab_key'],
        $payload['tab_label'],
        $payload['slide_title'],
        $payload['slide_body'],
        $payload['image_path'],
        $payload['icon_path'],
        $payload['position']
    );
    $stmt->execute();
}

function update_slide(mysqli $mysqli, int $id, array $payload): void
{
    $stmt = $mysqli->prepare('UPDATE slides SET tab_key = ?, tab_label = ?, slide_title = ?, slide_body = ?, image_path = ?, icon_path = ?, position = ? WHERE id = ?');
    $stmt->bind_param(
        'ssssssii',
        $payload['tab_key'],
        $payload['tab_label'],
        $payload['slide_title'],
        $payload['slide_body'],
        $payload['image_path'],
        $payload['icon_path'],
        $payload['position'],
        $id
    );
    $stmt->execute();
}

function delete_slide(mysqli $mysqli, int $id): void
{
    $stmt = $mysqli->prepare('DELETE FROM slides WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
