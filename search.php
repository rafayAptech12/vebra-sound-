<?php
require_once __DIR__ . '/includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$q = trim($_GET['q'] ?? '');
$typeFilter = trim($_GET['type'] ?? 'all');
$genreFilter = trim($_GET['genre'] ?? '');
$langFilter = trim($_GET['lang'] ?? '');
$artistFilter = trim($_GET['artist'] ?? '');
$yearFilter = trim($_GET['year'] ?? '');

$songResults = [];
$videoResults = [];
$genresList = [];

$resGenres = mysqli_query($conn, "SELECT name FROM genres ORDER BY name ASC");
if ($resGenres) {
    while ($row = mysqli_fetch_assoc($resGenres)) {
        $genresList[] = $row['name'];
    }
}

// Search Songs
if ($typeFilter === 'all' || $typeFilter === 'songs' || $typeFilter === 'music') {
    $songSql = "SELECT s.*, a.name AS artist, g.name AS genre FROM songs s LEFT JOIN artists a ON a.id = s.artist_id LEFT JOIN genres g ON g.id = s.genre_id WHERE s.status = 'approved'";
    $params = [];
    $types = '';

    if ($q !== '') {
        $songSql .= " AND (s.title LIKE ? OR a.name LIKE ? OR s.language LIKE ? OR g.name LIKE ?)";
        $wild = '%' . $q . '%';
        $params[] = $wild;
        $params[] = $wild;
        $params[] = $wild;
        $params[] = $wild;
        $types .= 'ssss';
    }
    if ($genreFilter !== '') {
        $songSql .= " AND g.name = ?";
        $params[] = $genreFilter;
        $types .= 's';
    }
    if ($langFilter !== '') {
        $songSql .= " AND s.language = ?";
        $params[] = $langFilter;
        $types .= 's';
    }
    if ($artistFilter !== '') {
        $songSql .= " AND a.name = ?";
        $params[] = $artistFilter;
        $types .= 's';
    }
    if ($yearFilter !== '') {
        $songSql .= " AND s.release_year = ?";
        $params[] = $yearFilter;
        $types .= 's';
    }
    $songSql .= " ORDER BY s.id DESC";

    $stmt = mysqli_prepare($conn, $songSql);
    if ($stmt) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $resS = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($resS)) {
            $songResults[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

// Search Videos
if ($typeFilter === 'all' || $typeFilter === 'videos' || $typeFilter === 'video') {
    $vidSql = "SELECT v.*, a.name AS artist, g.name AS genre FROM videos v LEFT JOIN artists a ON a.id = v.artist_id LEFT JOIN genres g ON g.id = v.genre_id WHERE v.status = 'approved'";
    $vParams = [];
    $vTypes = '';

    if ($q !== '') {
        $vidSql .= " AND (v.title LIKE ? OR a.name LIKE ? OR v.language LIKE ? OR g.name LIKE ?)";
        $wild = '%' . $q . '%';
        $vParams[] = $wild;
        $vParams[] = $wild;
        $vParams[] = $wild;
        $vParams[] = $wild;
        $vTypes .= 'ssss';
    }
    if ($genreFilter !== '') {
        $vidSql .= " AND g.name = ?";
        $vParams[] = $genreFilter;
        $vTypes .= 's';
    }
    if ($langFilter !== '') {
        $vidSql .= " AND v.language = ?";
        $vParams[] = $langFilter;
        $vTypes .= 's';
    }
    if ($artistFilter !== '') {
        $vidSql .= " AND a.name = ?";
        $vParams[] = $artistFilter;
        $vTypes .= 's';
    }
    if ($yearFilter !== '') {
        $vidSql .= " AND v.release_year = ?";
        $vParams[] = $yearFilter;
        $vTypes .= 's';
    }
    $vidSql .= " ORDER BY v.id DESC";

    $vStmt = mysqli_prepare($conn, $vidSql);
    if ($vStmt) {
        if (!empty($vParams)) {
            mysqli_stmt_bind_param($vStmt, $vTypes, ...$vParams);
        }
        mysqli_stmt_execute($vStmt);
        $resV = mysqli_stmt_get_result($vStmt);
        while ($row = mysqli_fetch_assoc($resV)) {
            $videoResults[] = $row;
        }
        mysqli_stmt_close($vStmt);
    }
}

$totalMatches = count($songResults) + count($videoResults);
$totalResultsCount = $totalMatches;
$pageTitle = ($q !== '' ? '"' . $q . '" — ' : '') . 'Search Catalog — VEBRA';
$activePage = 'search';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- SEARCH HERO
    ============================================= -->
    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="assets/images/hero-1.jpg" alt="" data-parallax-speed="0.12">
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">
                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <span aria-current="page">Search Catalogue</span>
                </nav>

                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    Search
                    <span class="page-caption__ghost" aria-hidden="true">Catalogue</span>
                </h1>

                <p class="page-caption__text" data-reveal="up" data-delay="0.2">
                    Search across all music, music videos, artists, genres, and release years in the VEBRA library.
                </p>

                <div class="page-caption__meta" data-reveal="up" data-delay="0.3">
                    <div class="page-caption__stat">
                        <strong><?= $totalMatches ?></strong>
                        <span>Results Found</span>
                    </div>
                    <div class="page-caption__stat">
                        <strong><?= count($songResults) ?></strong>
                        <span>Audio Tracks</span>
                    </div>
                    <div class="page-caption__stat">
                        <strong><?= count($videoResults) ?></strong>
                        <span>Videos</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- SEARCH FILTER BAR
    ============================================= -->
    <section class="section section--below-header" style="padding-top: 40px; padding-bottom: 20px;">
        <div class="container-vebra">
            <form method="GET" action="search.php" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 24px;">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-5">
                        <div class="field" style="margin: 0;">
                            <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" class="field__control" placeholder="Search by Title, Artist, Genre, Language..." style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 12px 16px; color: #fff;">
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <select name="type" class="field__control" style="background: #111; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 12px; color: #fff; width: 100%;">
                            <option value="all" <?= ($typeFilter === 'all' || $typeFilter === '') ? 'selected' : '' ?>>All Formats</option>
                            <option value="music" <?= ($typeFilter === 'music' || $typeFilter === 'songs') ? 'selected' : '' ?>>Music Only</option>
                            <option value="video" <?= ($typeFilter === 'video' || $typeFilter === 'videos') ? 'selected' : '' ?>>Videos Only</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <select name="genre" class="field__control" style="background: #111; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 12px; color: #fff; width: 100%;">
                            <option value="">All Genres</option>
                            <?php foreach ($genresList as $gn): ?>
                                <option value="<?= htmlspecialchars($gn) ?>" <?= $genreFilter === $gn ? 'selected' : '' ?>><?= htmlspecialchars($gn) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-1">
                        <input type="text" name="year" value="<?= htmlspecialchars($yearFilter) ?>" placeholder="Year" class="field__control" style="background: #111; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 12px; color: #fff; width: 100%;">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <button type="submit" class="btn btn--solid btn--block" data-magnetic="0.2" style="width: 100%;">
                            <span class="btn__label"><i class="ri-search-2-line"></i> Filter</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>


    <!-- RESULTS SECTION
    ============================================= -->
    <section class="section" style="padding-top: 30px;">
        <div class="container-vebra">

            <?php if ($totalMatches === 0): ?>
                <div style="text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
                    <i class="ri-search-eye-line" style="font-size: 3.5rem; color: var(--accent); opacity: 0.7; margin-bottom: 16px; display: inline-block;"></i>
                    <h2 style="font-family: 'Unbounded', sans-serif; font-size: 1.6rem; margin-bottom: 12px;">No matches found</h2>
                    <p style="color: var(--ink-40); max-width: 500px; margin: 0 auto 24px;">
                        We couldn't find any recordings matching "<strong><?= htmlspecialchars($q) ?></strong>". Try checking the spelling, resetting the filters, or explore our curated releases below.
                    </p>
                    <a href="search.php" class="btn btn--solid btn--sm"><span class="btn__label">Clear All Filters</span></a>
                </div>
            <?php endif; ?>

            <!-- SONGS RESULTS -->
            <?php if (!empty($songResults)): ?>
                <div style="margin-bottom: 60px;">
                    <div class="section-head" style="margin-bottom: 24px;">
                        <div>
                            <span class="section-label">Audio Releases (<?= count($songResults) ?>)</span>
                            <h2 class="section-head__title" style="font-size: 1.8rem;">Matching <em>Music Tracks</em></h2>
                        </div>
                    </div>

                    <div class="row g-4">
                        <?php foreach ($songResults as $song): ?>
                            <div class="col-lg-6">
                                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px; padding: 16px; display: flex; align-items: center; gap: 18px; transition: all 0.3s ease;">
                                    <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; flex-shrink: 0; position: relative;">
                                        <img src="<?= htmlspecialchars(!empty($song['artwork']) ? $song['artwork'] : 'assets/images/album-1.jpg') ?>" alt="<?= htmlspecialchars($song['title']) ?>" style="width:100%; height:100%; object-fit:cover;">
                                    </div>
                                    <div style="flex-grow: 1; min-width: 0;">
                                        <h3 style="font-family: 'Unbounded', sans-serif; font-size: 1.05rem; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <a href="track.php?id=<?= $song['id'] ?>" style="color: #fff; text-decoration: none;">
                                                <?= htmlspecialchars($song['title']) ?>
                                            </a>
                                        </h3>
                                        <div style="color: var(--ink-40); font-size: 0.9rem; margin-bottom: 8px;">
                                            <i class="ri-user-voice-line" style="color:var(--accent);"></i> <?= htmlspecialchars($song['artist'] ?? 'Unknown Artist') ?>
                                        </div>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <?php if (!empty($song['genre'])): ?>
                                                <span class="pill pill--sm" style="font-size: 0.72rem;"><?= htmlspecialchars($song['genre']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($song['release_year'])): ?>
                                                <span class="pill pill--sm" style="font-size: 0.72rem;"><?= htmlspecialchars($song['release_year']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($song['language'])): ?>
                                                <span class="pill pill--sm" style="font-size: 0.72rem;"><?= htmlspecialchars($song['language']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="flex-shrink: 0; text-align: right;">
                                        <button type="button" class="btn-circle btn-circle--solid vebra-play-card-btn" data-url="<?= htmlspecialchars($song['audio_path']) ?>" data-title="<?= htmlspecialchars($song['title']) ?>" data-artist="<?= htmlspecialchars($song['artist'] ?? 'VEBRA') ?>" data-cover="<?= htmlspecialchars(!empty($song['artwork']) ? $song['artwork'] : 'assets/images/album-1.jpg') ?>" aria-label="Play <?= htmlspecialchars($song['title']) ?>" style="width: 44px; height: 44px;">
                                            <i class="ri-play-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- VIDEOS RESULTS -->
            <?php if (!empty($videoResults)): ?>
                <div style="margin-bottom: 60px;">
                    <div class="section-head" style="margin-bottom: 24px;">
                        <div>
                            <span class="section-label">Visual Media (<?= count($videoResults) ?>)</span>
                            <h2 class="section-head__title" style="font-size: 1.8rem;">Matching <em>Music Videos</em></h2>
                        </div>
                    </div>

                    <div class="row g-4">
                        <?php foreach ($videoResults as $vid): ?>
                            <div class="col-md-6 col-lg-4">
                                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px; overflow: hidden;">
                                    <div style="position: relative; aspect-ratio: 16/9; overflow: hidden;">
                                        <img src="<?= htmlspecialchars(!empty($vid['thumbnail']) ? $vid['thumbnail'] : 'assets/images/video-cover-1.jpg') ?>" alt="<?= htmlspecialchars($vid['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <a href="single-video.php?id=<?= $vid['id'] ?>" class="btn-play" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                            <i class="ri-play-fill"></i>
                                        </a>
                                        <?php if (!empty($vid['release_year'])): ?>
                                            <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); font-size: 0.75rem; padding: 2px 6px; border-radius: 4px; color: #fff;">
                                                <?= htmlspecialchars($vid['release_year']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="padding: 16px;">
                                        <h3 style="font-family: 'Unbounded', sans-serif; font-size: 1rem; margin-bottom: 6px;">
                                            <a href="single-video.php?id=<?= $vid['id'] ?>" style="color: #fff; text-decoration: none;">
                                                <?= htmlspecialchars($vid['title']) ?>
                                            </a>
                                        </h3>
                                        <div style="color: var(--ink-40); font-size: 0.85rem; margin-bottom: 12px;">
                                            <?= htmlspecialchars($vid['artist'] ?? 'Unknown Artist') ?> &bull; <?= htmlspecialchars($vid['genre'] ?? 'Official') ?>
                                        </div>
                                        <a href="single-video.php?id=<?= $vid['id'] ?>" class="link-arrow" style="font-size: 0.85rem;">
                                            Watch Video <i class="ri-arrow-right-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
