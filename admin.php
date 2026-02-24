<?php
require __DIR__ . '/functions.php';

$mysqli = db_connect();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $payload = [
        'tab_key' => trim($_POST['tab_key'] ?? ''),
        'tab_label' => trim($_POST['tab_label'] ?? ''),
        'slide_title' => trim($_POST['slide_title'] ?? ''),
        'slide_body' => trim($_POST['slide_body'] ?? ''),
        'position' => (int) ($_POST['position'] ?? 0),
    ];

    $existingImage = trim($_POST['existing_image_path'] ?? '');
    $existingIcon = trim($_POST['existing_icon_path'] ?? '');

    try {
        $uploadedImage = save_upload('image_file', 'uploads/images', ['jpg', 'jpeg', 'png', 'webp']);
        $uploadedIcon = save_upload('icon_file', 'uploads/icons', ['svg', 'png', 'jpg', 'jpeg', 'webp']);
    } catch (Throwable $e) {
        $error = $e->getMessage();
        $uploadedImage = null;
        $uploadedIcon = null;
    }

    $payload['image_path'] = $uploadedImage ?: $existingImage;
    $payload['icon_path'] = $uploadedIcon ?: $existingIcon;

    if (!$error) {
        if ($action === 'create') {
            if (!$payload['image_path'] || !$payload['icon_path']) {
                $error = 'Please upload both an image and an icon.';
            } else {
                create_slide($mysqli, $payload);
                $message = 'Slide created.';
            }
        }

        if ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            update_slide($mysqli, $id, $payload);
            $message = 'Slide updated.';
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            delete_slide($mysqli, $id);
            $message = 'Slide deleted.';
        }
    }
}

$slides = db_fetch_all($mysqli, 'SELECT * FROM slides ORDER BY tab_key, position, id');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Slides</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manage Slides</h1>
            <a href="index.php" class="btn btn-outline-dark">View site</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <form method="post" id="slide-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create" id="form-action">
                    <input type="hidden" name="id" value="" id="form-id">
                    <input type="hidden" name="existing_image_path" id="form-existing-image-path">
                    <input type="hidden" name="existing_icon_path" id="form-existing-icon-path">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tab Key</label>
                            <input class="form-control" name="tab_key" id="form-tab-key" >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tab Label</label>
                            <input class="form-control" name="tab_label" id="form-tab-label" >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Position</label>
                            <input class="form-control" type="number" name="position" id="form-position" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Slide Title</label>
                            <input class="form-control" name="slide_title" id="form-slide-title" >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image Upload</label>
                            <input class="form-control" type="file" name="image_file" id="form-image-file" accept=".jpg,.jpeg,.png,.webp" >
                            <div class="form-text" id="form-image-current"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Icon Upload</label>
                            <input class="form-control" type="file" name="icon_file" id="form-icon-file" accept=".svg,.png,.jpg,.jpeg,.webp" >
                            <div class="form-text" id="form-icon-current"></div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Slide Body</label>
                            <textarea class="form-control" name="slide_body" id="form-slide-body" rows="3" ></textarea>
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Save slide</button>
                        <button class="btn btn-outline-secondary" type="button" id="form-reset">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="h5">Current Slides</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tab</th>
                                <th>Title</th>
                                <th>Position</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($slides as $slide): ?>
                                <tr>
                                    <td><?= (int) $slide['id'] ?></td>
                                    <td><?= htmlspecialchars($slide['tab_label']) ?></td>
                                    <td><?= htmlspecialchars($slide['slide_title']) ?></td>
                                    <td><?= (int) $slide['position'] ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary edit-slide" type="button"
                                            data-id="<?= (int) $slide['id'] ?>"
                                            data-tab-key="<?= htmlspecialchars($slide['tab_key']) ?>"
                                            data-tab-label="<?= htmlspecialchars($slide['tab_label']) ?>"
                                            data-slide-title="<?= htmlspecialchars($slide['slide_title']) ?>"
                                            data-slide-body="<?= htmlspecialchars($slide['slide_body']) ?>"
                                            data-image-path="<?= htmlspecialchars($slide['image_path']) ?>"
                                            data-icon-path="<?= htmlspecialchars($slide['icon_path']) ?>"
                                            data-position="<?= (int) $slide['position'] ?>">Edit</button>
                                        <form method="post" class="d-inline delete-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>
    <script>
        const form = document.getElementById('slide-form');
        const resetBtn = document.getElementById('form-reset');

        $('.edit-slide').on('click', function () {
            $('#form-action').val('update');
            $('#form-id').val($(this).data('id'));
            $('#form-tab-key').val($(this).data('tab-key'));
            $('#form-tab-label').val($(this).data('tab-label'));
            $('#form-slide-title').val($(this).data('slide-title'));
            $('#form-slide-body').val($(this).data('slide-body'));
            $('#form-existing-image-path').val($(this).data('image-path'));
            $('#form-existing-icon-path').val($(this).data('icon-path'));
            $('#form-image-current').text('Current: ' + $(this).data('image-path'));
            $('#form-icon-current').text('Current: ' + $(this).data('icon-path'));
            $('#form-position').val($(this).data('position'));
            $('#form-image-file').prop('required', false).val('');
            $('#form-icon-file').prop('required', false).val('');
            form.scrollIntoView({ behavior: 'smooth' });
        });

        resetBtn.addEventListener('click', function () {
            form.reset();
            document.getElementById('form-action').value = 'create';
            document.getElementById('form-id').value = '';
            document.getElementById('form-existing-image-path').value = '';
            document.getElementById('form-existing-icon-path').value = '';
            document.getElementById('form-image-current').textContent = '';
            document.getElementById('form-icon-current').textContent = '';
            document.getElementById('form-image-file').required = true;
            document.getElementById('form-icon-file').required = true;
        });

        $('.delete-form').on('submit', function (event) {
            event.preventDefault();
            const targetForm = this;

            Swal.fire({
                title: 'Delete this slide?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    targetForm.submit();
                }
            });
        });
    </script>
</body>
</html>
