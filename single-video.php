<?php
require_once __DIR__ . '/includes/db.php';

$videoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

if ($videoId <= 0) {
    $firstVidRes = mysqli_query($conn, "SELECT id FROM videos WHERE status = 'approved' ORDER BY id ASC LIMIT 1");
    if ($firstVidRes && $firstRow = mysqli_fetch_assoc($firstVidRes)) {
        $videoId = (int)$firstRow['id'];
    }
}

$stmt = mysqli_prepare($conn, "
    SELECT v.*, a.name AS artist_name, g.name AS genre_name
    FROM videos v
    LEFT JOIN artists a ON a.id = v.artist_id
    LEFT JOIN genres g ON g.id = v.genre_id
    WHERE v.id = ? AND v.status = 'approved'
    LIMIT 1
");

$video = null;
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $videoId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $video = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$video) {
    http_response_code(404);
    $pageTitle = 'Video Not Found — VEBRA';
    $activePage = 'videos';
    require_once __DIR__ . '/includes/header.php';
    echo '<main class="section"><div class="container-vebra"><h1>Video not found</h1><p>This video may be unavailable or not approved yet.</p><a href="video-gallery.php">Back to videos</a></div></main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

function videoEscape($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$title = $video['title'] ?? 'Untitled Video';
$artistName = $video['artist_name'] ?? 'Unknown Artist';
$genreName = $video['genre_name'] ?? 'Music';
$year = $video['release_year'] ?? '';
$language = $video['language'] ?? '';
$thumbnail = $video['thumbnail'] ?? '';
$videoPath = trim((string)($video['video_path'] ?? ''));
$videoUrl = trim((string)($video['video_url'] ?? ''));

if ($thumbnail === '') {
    $thumbnail = 'assets/images/video-cover-1.jpg';
}

/* Recognize supported YouTube URL formats */
$youtubeId = '';
if ($videoUrl !== '') {
    $parts = parse_url($videoUrl);
    $host = strtolower($parts['host'] ?? '');
    $path = trim($parts['path'] ?? '', '/');

    if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'music.youtube.com'], true)) {
        parse_str($parts['query'] ?? '', $query);
        $youtubeId = $query['v'] ?? '';
        if (str_starts_with($path, 'embed/')) {
            $youtubeId = substr($path, 6);
        } elseif (str_starts_with($path, 'shorts/')) {
            $youtubeId = substr($path, 7);
        }
    } elseif ($host === 'youtu.be' || $host === 'www.youtu.be') {
        $youtubeId = explode('/', $path)[0] ?? '';
    }

    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $youtubeId)) {
        $youtubeId = '';
    }
}

$pageTitle = videoEscape($title) . ' — Video — VEBRA';
$activePage = 'videos';
$activeSub = 'single-video';
$bodyClass = 'theme-magenta';

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">
    <section class="page-caption">
        <div class="page-caption__bg" aria-hidden="true">
            <img src="<?= videoEscape($thumbnail) ?>" alt="">
        </div>
        <div class="container-vebra">
            <div class="page-caption__inner">
                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span>/</span>
                    <a href="video-gallery.php">Videos</a>
                    <span>/</span>
                    <span><?= videoEscape($title) ?></span>
                </nav>
                <h1 class="page-caption__title"><?= videoEscape($title) ?></h1>
                <p class="page-caption__text">
                    <?= videoEscape($artistName) ?>
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-vebra">
            <div class="content-layout">
                <div class="stack stack-5">
                    <div class="video-ambient">
                        <div class="video-frame">
                            <?php if ($youtubeId !== ''): ?>
                                <iframe
                                    src="https://www.youtube-nocookie.com/embed/<?= videoEscape($youtubeId) ?>?enablejsapi=1&origin=<?= urlencode('http://' . $_SERVER['HTTP_HOST']) ?>"
                                    title="<?= videoEscape($title) ?>"
                                    style="width:100%;aspect-ratio:16/9;border:0;border-radius:var(--radius-md)"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen>
                                </iframe>
                            <?php elseif ($videoPath !== '' && file_exists(__DIR__ . '/' . $videoPath)): ?>
                                <video controls playsinline preload="metadata"
                                    poster="<?= videoEscape($thumbnail) ?>"
                                    style="width:100%;border-radius:var(--radius-md)">
                                    <source src="<?= videoEscape($videoPath) ?>">
                                    Your browser does not support this video.
                                </video>
                            <?php elseif ($videoUrl !== ''): ?>
                                <div style="padding:40px;text-align:center;background:rgba(255,255,255,0.03);border-radius:var(--radius-md)">
                                    <h2>External Video</h2>
                                    <p style="color:var(--ink-40);margin-bottom:20px;">This video is available on an external platform.</p>
                                    <a href="<?= videoEscape($videoUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--solid">
                                        <span class="btn__label">Open Video Stream <i class="ri-arrow-right-up-line"></i></span>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div style="padding:20px;text-align:center;background:rgba(255,255,255,0.03);border-radius:var(--radius-md)">
                                    <img src="<?= videoEscape($thumbnail) ?>" alt="<?= videoEscape($title) ?>" style="max-width:100%;border-radius:var(--radius-md);margin-bottom:16px;">
                                    <h2>VEBRA Visual Media</h2>
                                    <p style="color:var(--ink-40);margin:0;">Official visual presentation for <?= videoEscape($title) ?>.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="stack stack-3">
                        <span class="section-label">Official video</span>
                        <h2 class="section-head__title"><?= videoEscape($title) ?></h2>
                    </div>

                    <div class="article__meta">
                        <?php if ($year): ?><span><i class="ri-calendar-line"></i> <?= videoEscape($year) ?></span><?php endif; ?>
                        <?php if ($language): ?><span><i class="ri-global-line"></i> <?= videoEscape($language) ?></span><?php endif; ?>
                        <span><i class="ri-music-2-line"></i> <?= videoEscape($genreName) ?></span>
                    </div>

                    <div class="rich-text">
                        <p><strong><?= videoEscape($title) ?></strong> by <?= videoEscape($artistName) ?>.</p>
                        <p>Explore more music videos from VEBRA.</p>
                    </div>

                    <div class="article__tags">
                        <a href="video-gallery.php" class="pill"><?= videoEscape($genreName) ?></a>
                        <a href="video-gallery.php" class="pill">Music Video</a>
                    </div>
                </div>

                <aside class="sidebar">
                    <div class="sidebar__block">
                        <h2 class="sidebar__title">More videos</h2>
                        <div class="mini-list">
                            <?php
                            $artParam = (int)($video['artist_id'] ?? 0);
                            $genParam = (int)($video['genre_id'] ?? 0);
                            $relatedStmt = mysqli_prepare($conn, "
                                SELECT v.id, v.title, v.thumbnail, a.name AS artist_name
                                FROM videos v
                                LEFT JOIN artists a ON a.id = v.artist_id
                                WHERE v.status = 'approved' AND v.id != ?
                                ORDER BY (v.artist_id = ? AND ? > 0) DESC, (v.genre_id = ? AND ? > 0) DESC, v.created_at DESC, v.id DESC
                                LIMIT 4
                            ");
                            if ($relatedStmt):
                                mysqli_stmt_bind_param($relatedStmt, 'iiiii', $videoId, $artParam, $artParam, $genParam, $genParam);
                                mysqli_stmt_execute($relatedStmt);
                                $relatedResult = mysqli_stmt_get_result($relatedStmt);
                                while ($related = mysqli_fetch_assoc($relatedResult)):
                                    $relatedThumb = !empty($related['thumbnail']) ? $related['thumbnail'] : 'assets/images/video-cover-1.jpg';
                                    $relatedArtist = !empty($related['artist_name']) ? $related['artist_name'] : 'VEBRA Artist';
                            ?>
                                <a href="single-video.php?id=<?= (int)$related['id'] ?>" class="mini-item">
                                    <img src="<?= videoEscape($relatedThumb) ?>" alt="<?= videoEscape($related['title']) ?>">
                                    <div>
                                        <h4><?= videoEscape($related['title']) ?></h4>
                                        <span><?= videoEscape($relatedArtist) ?></span>
                                    </div>
                                </a>
                            <?php
                                endwhile;
                                mysqli_stmt_close($relatedStmt);
                            endif;
                            ?>
                        </div>
                        <a href="video-gallery.php" class="link-arrow">All videos <i class="ri-arrow-right-up-line"></i></a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const vid = document.querySelector('.video-frame video');
    if (vid) {
        vid.addEventListener('play', function () {
            if (typeof Amplitude !== 'undefined' && Amplitude.getPlayerState() === 'playing') {
                Amplitude.pause();
            }
            document.querySelectorAll('audio').forEach(function (a) {
                if (!a.paused) a.pause();
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
