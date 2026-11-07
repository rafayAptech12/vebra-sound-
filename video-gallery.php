
<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Video Gallery — VEBRA';
$activePage = 'videos';
$activeSub = 'video-gallery';
$bodyClass = 'theme-magenta';
$basePath = $basePath ?? '';

/* Find the actual name column without assuming the schema. */
function vebraVideoNameColumn(mysqli $conn, string $table): ?string
{
    if (!in_array($table, ['artists', 'genres'], true)) {
        return null;
    }

    $result = $conn->query("SHOW COLUMNS FROM `$table`");
    if (!$result) {
        return null;
    }

    $columns = [];
    while ($column = $result->fetch_assoc()) {
        $columns[] = $column['Field'];
    }

    foreach (['artist_name', 'genre_name', 'name', 'title', 'username'] as $candidate) {
        if (in_array($candidate, $columns, true)) {
            return $candidate;
        }
    }

    return null;
}

/* Resolve relative paths against the project base path. */
function vebraVideoAsset(string $path, string $basePath): string
{
    $path = trim($path);

    if ($path === '') {
        return '';
    }

    if (preg_match('~^(https?://|/)~i', $path)) {
        return $path;
    }

    return $basePath . ltrim($path, '/');
}

$videos = [];
$errorMessage = '';

$artistNames = [];
$genreNames = [];

$artistColumn = vebraVideoNameColumn($conn, 'artists');
if ($artistColumn !== null) {
    $result = $conn->query(
        "SELECT id, `$artistColumn` AS display_name FROM artists"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $artistNames[$row['id']] = $row['display_name'];
        }
    }
}

$genreColumn = vebraVideoNameColumn($conn, 'genres');
if ($genreColumn !== null) {
    $result = $conn->query(
        "SELECT id, `$genreColumn` AS display_name FROM genres"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $genreNames[$row['id']] = $row['display_name'];
        }
    }
}

/* Load approved videos from the real database. */
$result = $conn->query("
    SELECT *
    FROM videos
    WHERE status = 'approved'
    ORDER BY created_at DESC, id DESC
");

if ($result) {
    while ($video = $result->fetch_assoc()) {
        $video['artist_display'] =
            $artistNames[$video['artist_id'] ?? ''] ?? 'VEBRA';

        $video['genre_display'] =
            $genreNames[$video['genre_id'] ?? ''] ?? '';

        $videos[] = $video;
    }
} else {
    $errorMessage = 'Videos could not be loaded. Please check the database.';
}

// Choose an eligible video with genuine media source as the featured video
$featured = null;
foreach ($videos as $v) {
    $hasUrl = !empty(trim((string)($v['video_url'] ?? '')));
    $hasPath = !empty(trim((string)($v['video_path'] ?? '')));
    if ($hasUrl || $hasPath) {
        $featured = $v;
        break;
    }
}
if (!$featured && !empty($videos)) {
    $featured = $videos[0];
}

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="<?= e($basePath) ?>assets/images/video-bg.jpg"
                 alt=""
                 data-parallax-speed="0.12">
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">
                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                    <a href="<?= e($basePath) ?>index.php">Home</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <span aria-current="page">Video Gallery</span>
                </nav>

                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    Videos
                    <span class="page-caption__ghost" aria-hidden="true">Motion</span>
                </h1>

                <p class="page-caption__text" data-reveal="up" data-delay="0.2">
                    Official videos, live performances and visual stories from VEBRA.
                </p>

                <div class="page-caption__meta" data-reveal="up" data-delay="0.3">
                    <div class="page-caption__stat">
                        <strong><?= count($videos) ?></strong>
                        <span>Approved videos</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--glow">
        <div class="container-vebra">

            <?php if ($errorMessage !== ''): ?>

                <p role="alert"><?= e($errorMessage) ?></p>

            <?php elseif (empty($videos)): ?>

                <div class="video-gallery__featured video-ambient" data-reveal="up">
                    <h2>No approved videos yet</h2>
                    <p>Videos will appear here when they are added and approved.</p>
                </div>

            <?php else: ?>

                <?php
                $featuredTitle = (string)($featured['title'] ?? 'Untitled Video');
                $featuredThumb = vebraVideoAsset(
                    (string)($featured['thumbnail'] ?? ''),
                    $basePath
                );
                $featuredPath = vebraVideoAsset(
                    (string)($featured['video_path'] ?? ''),
                    $basePath
                );
                $featuredUrl = trim((string)($featured['video_url'] ?? ''));

                $featuredYoutubeId = '';
                if ($featuredUrl !== '') {
                    $parts = parse_url($featuredUrl);
                    $host = strtolower($parts['host'] ?? '');
                    $path = trim($parts['path'] ?? '', '/');
                    if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'music.youtube.com'], true)) {
                        parse_str($parts['query'] ?? '', $query);
                        $featuredYoutubeId = $query['v'] ?? '';
                        if (str_starts_with($path, 'embed/')) {
                            $featuredYoutubeId = substr($path, 6);
                        } elseif (str_starts_with($path, 'shorts/')) {
                            $featuredYoutubeId = substr($path, 7);
                        }
                    } elseif ($host === 'youtu.be' || $host === 'www.youtu.be') {
                        $featuredYoutubeId = explode('/', $path)[0] ?? '';
                    }
                    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $featuredYoutubeId)) {
                        $featuredYoutubeId = '';
                    }
                }
                ?>

                <div class="video-gallery__featured video-ambient" data-reveal="mask">
                    <div class="video-frame">

                        <?php if ($featuredYoutubeId !== ''): ?>
                            <iframe
                                src="https://www.youtube-nocookie.com/embed/<?= e($featuredYoutubeId) ?>"
                                title="<?= e($featuredTitle) ?>"
                                style="width:100%;aspect-ratio:16/9;border:0;border-radius:var(--radius-md)"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen>
                            </iframe>

                        <?php elseif ($featuredPath !== '' && file_exists(__DIR__ . '/' . $featuredPath)): ?>
                            <video
                                playsinline
                                controls
                                preload="metadata"
                                <?= $featuredThumb !== '' ? 'poster="' . e($featuredThumb) . '"' : '' ?>
                                style="width:100%;border-radius:var(--radius-md);">
                                <source src="<?= e($featuredPath) ?>">
                                Your browser does not support video playback.
                            </video>

                        <?php elseif ($featuredThumb !== ''): ?>
                            <div style="position:relative; aspect-ratio:16/9; overflow:hidden; border-radius:var(--radius-md);">
                                <a href="<?= e($basePath) ?>single-video.php?id=<?= (int)$featured['id'] ?>">
                                    <img src="<?= e($featuredThumb) ?>"
                                         alt="<?= e($featuredTitle) ?>"
                                         style="width:100%; height:100%; object-fit:cover;">
                                    <span class="video-card__play btn-play btn-play--glass" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
                                        <i class="ri-play-fill"></i>
                                    </span>
                                </a>
                            </div>

                        <?php else: ?>
                            <div style="padding:35px">
                                <i class="ri-video-line" aria-hidden="true"></i>
                                <p>Video file is not available yet.</p>
                            </div>
                        <?php endif; ?>

                        <div class="video-frame__caption">
                            <h2 class="video-frame__title">
                                <a href="<?= e($basePath) ?>single-video.php?id=<?= (int)$featured['id'] ?>">
                                    <?= e($featuredTitle) ?>
                                </a>
                            </h2>
                            <span><?= e((string)$featured['artist_display']) ?></span>
                        </div>
                    </div>
                </div>

                <div style="margin-top:3rem">
                    <div class="section-head">
                        <div>
                            <span class="section-label">The collection</span>
                            <h2 class="section-head__title">All <em>videos</em></h2>
                        </div>
                        <p class="section-head__text">
                            <?= count($videos) ?> approved videos
                        </p>
                    </div>

                    <div style="margin-bottom:24px">
                        <input
                            type="search"
                            id="video-search"
                            placeholder="Search videos or artists..."
                            aria-label="Search videos or artists"
                            style="width:100%;max-width:500px;padding:15px 18px;border:1px solid #39313d;border-radius:8px;background:#121016;color:#fff;font:inherit">
                    </div>

                    <div class="filter-grid video-gallery__grid"
                         id="video-grid"
                         data-filter-grid
                         data-reveal-group
                         data-stagger="0.08">

                        <?php foreach ($videos as $video): ?>
                            <?php
                            $videoId = (int)$video['id'];
                            $title = (string)($video['title'] ?? 'Untitled Video');
                            $artist = (string)$video['artist_display'];
                            $thumbnail = vebraVideoAsset(
                                (string)($video['thumbnail'] ?? ''),
                                $basePath
                            );
                            $videoPath = trim((string)($video['video_path'] ?? ''));
                            $videoUrl = trim((string)($video['video_url'] ?? ''));
                            $language = (string)($video['language'] ?? '');
                            $year = (string)($video['release_year'] ?? '');
                            $genre = (string)$video['genre_display'];
                            ?>

                            <div class="filter-grid__item video-library-card"
                                 data-search="<?= e(strtolower($title . ' ' . $artist . ' ' . $language . ' ' . $genre)) ?>">

                                <a href="<?= e($basePath) ?>single-video.php?id=<?= $videoId ?>"
                                   class="video-card"
                                   data-cursor="Play">

                                    <?php if ($thumbnail !== ''): ?>
                                        <img src="<?= e($thumbnail) ?>"
                                             alt="<?= e($title) ?>"
                                             loading="lazy">
                                    <?php else: ?>
                                        <div style="aspect-ratio:16/9;display:grid;place-items:center;background:#211b27;color:#d9ff00;font-size:42px">
                                            <i class="ri-video-line" aria-hidden="true"></i>
                                        </div>
                                    <?php endif; ?>

                                    <span class="video-card__play btn-play btn-play--glass"
                                          aria-hidden="true">
                                        <i class="ri-play-fill"></i>
                                    </span>

                                    <div class="video-card__body">
                                        <h3 class="video-card__title"><?= e($title) ?></h3>
                                        <span class="video-card__meta"><?= e($artist) ?></span>

                                        <span class="video-card__meta">
                                            <?= e($language) ?>
                                            <?php if ($year !== ''): ?>
                                                <?= $language !== '' ? ' · ' : '' ?><?= e($year) ?>
                                            <?php endif; ?>
                                            <?php if ($genre !== ''): ?>
                                                · <?= e($genre) ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </a>

                                <?php if ($videoPath === '' && $videoUrl !== ''): ?>
                                    <p style="padding:0 12px 12px">
                                        <a href="<?= e($videoUrl) ?>"
                                           target="_blank"
                                           rel="noopener noreferrer">
                                            Watch external video ↗
                                        </a>
                                    </p>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; ?>

                    </div>

                    <p id="no-video-match" hidden>No videos match your search.</p>
                </div>

            <?php endif; ?>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('video-search');
    const cards = document.querySelectorAll('.video-library-card');
    const empty = document.getElementById('no-video-match');

    if (!search) return;

    search.addEventListener('input', function () {
        const term = search.value.trim().toLowerCase();
        let visible = 0;

        cards.forEach(function (card) {
            const matches = (card.dataset.search || '').includes(term);
            card.hidden = !matches;
            if (matches) visible++;
        });

        if (empty) empty.hidden = visible !== 0;
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
