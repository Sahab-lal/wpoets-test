<?php
require __DIR__ . '/db.php';

try {
    $mysqli = db_connect();
    $rows = db_fetch_all($mysqli, 'SELECT * FROM slides ORDER BY tab_key, position, id');
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h2>Database error</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

$tabs = [];
foreach ($rows as $row) {
    $key = $row['tab_key'];
    if (!isset($tabs[$key])) {
        $tabs[$key] = [
            'tab_key' => $row['tab_key'],
            'tab_label' => $row['tab_label'],
            'icon_path' => $row['icon_path'],
            'slides' => [],
        ];
    }
    $tabs[$key]['slides'][] = [
        'id' => (int) $row['id'],
        'slide_title' => $row['slide_title'],
        'slide_body' => $row['slide_body'],
        'image_path' => $row['image_path'],
        'position' => (int) $row['position'],
    ];
}

$tabList = array_values($tabs);

function render_carousel(array $tab, string $carouselId): void
{
    $slides = $tab['slides'];
    if (!$slides) {
        echo '<p>No slides available.</p>';
        return;
    }
    ?>
    <div id="<?= htmlspecialchars($carouselId) ?>" class="carousel slide" data-bs-ride="false">
        <div class="carousel-inner">
            <?php foreach ($slides as $slideIndex => $slide): ?>
                <div class="carousel-item<?= $slideIndex === 0 ? ' active' : '' ?>" style="--slide-image: url('<?= htmlspecialchars($slide['image_path']) ?>');">
                    <div class="carousel-item-body">
                        <div class="row g-4 align-items-stretch">
                            <div class="col-lg-7">
                                <div class="slide-card h-100">
                                    <div class="slider-meta">
                                        <img src="<?= htmlspecialchars($tab['icon_path']) ?>" alt="" width="26" height="26">
                                        <span><?= htmlspecialchars($tab['tab_label']) ?></span>
                                    </div>
                                    <h3><?= htmlspecialchars($slide['slide_title']) ?></h3>
                                    <p><?= htmlspecialchars($slide['slide_body']) ?></p>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="slide-image-panel h-100">
                                    <img src="<?= htmlspecialchars($slide['image_path']) ?>" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="carousel-controls">
            <button class="slide-btn" type="button" data-bs-target="#<?= htmlspecialchars($carouselId) ?>" data-bs-slide="prev" aria-label="Previous slide">&#8592;</button>
            <button class="slide-btn" type="button" data-bs-target="#<?= htmlspecialchars($carouselId) ?>" data-bs-slide="next" aria-label="Next slide">&#8594;</button>
        </div>
    </div>
    <?php
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WPoets - Slider Tabs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="page-wrap">
        <div class="container">
            <div class="hero">
                <div>
                    <h1>Design + Learning Studio</h1>
                    <p>Three connected areas. Pick a focus, explore each slide, and see the paired visual update in real time.</p>
                </div>
                <a class="btn btn-dark" href="admin.php">Manage slides</a>
            </div>

            <div class="layout">
                <div class="row g-4">
                    <div class="col-lg-3">
                        <div class="tabs d-none d-lg-block">
                            <div class="nav flex-column nav-pills" id="tabList" role="tablist" aria-orientation="vertical">
                                <?php foreach ($tabList as $index => $tab): ?>
                                    <button class="nav-link tab-trigger<?= $index === 0 ? ' active' : '' ?>" id="tab-btn-<?= $index ?>" data-bs-toggle="pill" data-bs-target="#tab-pane-<?= $index ?>" type="button" role="tab" aria-controls="tab-pane-<?= $index ?>" aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                        <img src="<?= htmlspecialchars($tab['icon_path']) ?>" alt="">
                                        <span><?= htmlspecialchars($tab['tab_label']) ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="accordion accordion-tabs d-lg-none" id="tabAccordion">
                            <?php foreach ($tabList as $index => $tab): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button<?= $index === 0 ? '' : ' collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#tab-<?= $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="tab-<?= $index ?>">
                                            <?= htmlspecialchars($tab['tab_label']) ?>
                                        </button>
                                    </h2>
                                    <div id="tab-<?= $index ?>" class="accordion-collapse collapse<?= $index === 0 ? ' show' : '' ?>" data-bs-parent="#tabAccordion">
                                        <div class="accordion-body">
                                            <?php render_carousel($tab, 'carousel-mobile-' . $index); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-lg-9 d-none d-lg-block">
                        <div class="tab-content" id="tabContent">
                            <?php foreach ($tabList as $index => $tab): ?>
                                <div class="tab-pane fade<?= $index === 0 ? ' show active' : '' ?>" id="tab-pane-<?= $index ?>" role="tabpanel" aria-labelledby="tab-btn-<?= $index ?>">
                                    <?php render_carousel($tab, 'carousel-' . $index); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
