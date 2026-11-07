<?php
require_once __DIR__ . '/includes/db.php';

// Fetch approved songs
$songs = [];
$res = mysqli_query($conn, "
    SELECT s.*, a.name AS artist_name, g.name AS genre_name
    FROM songs s
    LEFT JOIN artists a ON a.id = s.artist_id
    LEFT JOIN genres g ON g.id = s.genre_id
    WHERE s.status = 'approved'
    ORDER BY s.id ASC
");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $songs[] = $row;
    }
}

$pageTitle = 'Singles & Tracks — VEBRA';
$activePage = 'music';
$activeSub = 'singles';
$bodyClass = 'theme-magenta';

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE CAPTION -->
    <section class="page-caption">
        <div class="container-vebra">
            <div class="page-caption__inner">
                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <a href="discography.php">Music</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <span aria-current="page">Singles</span>
                </nav>

                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    Singles
                    <span class="page-caption__ghost" aria-hidden="true">Tracks</span>
                </h1>

                <p class="page-caption__text" data-reveal="up" data-delay="0.25">
                    Individual tracks, featured recordings and standalone releases from the VEBRA studio.
                </p>

                <div class="page-caption__meta" data-reveal="up" data-delay="0.35">
                    <div class="page-caption__stat"><strong data-count-to="<?= count($songs) ?>">0</strong><span>Approved Tracks</span></div>
                    <div class="page-caption__stat"><strong>High-Res</strong><span>Mastering</span></div>
                    <div class="page-caption__stat"><strong>2026</strong><span>Active Year</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- SINGLES LIST -->
    <section class="section">
        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">Audio Releases</span>
                    <h2 class="section-head__title" data-split="lines">All <em>Tracks</em></h2>
                </div>
                <p class="section-head__text">Click any track to open its dedicated listening room with full reviews and ratings.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                <?php if (!empty($songs)): ?>
                    <?php foreach ($songs as $s):
                        $sId = (int)$s['id'];
                        $sTitle = htmlspecialchars($s['title'] ?? 'Untitled Track');
                        $sArtist = htmlspecialchars($s['artist_name'] ?? 'VEBRA Artist');
                        $sGenre = htmlspecialchars($s['genre_name'] ?? 'Electronic');
                        $sYear = (int)($s['release_year'] ?? 2026);
                        $sArt = !empty($s['artwork']) ? htmlspecialchars($s['artwork']) : 'assets/images/album-1.jpg';
                        $sAudio = !empty($s['audio_path']) ? htmlspecialchars($s['audio_path']) : 'assets/musics/1.mp3';
                    ?>
                    <article class="album-card" style="background: var(--glass-bg); border: 1px solid var(--glass-brd); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column;">
                        <a href="track.php?id=<?= $sId ?>" class="album-card__cover" data-cursor="Listen" style="position: relative; display: block; overflow: hidden; aspect-ratio: 1/1;">
                            <img src="<?= $sArt ?>" alt="<?= $sTitle ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                            <span class="btn-circle btn-circle--solid album-card__play vebra-play-card-btn" data-url="<?= $sAudio ?>" data-title="<?= $sTitle ?>" data-artist="<?= $sArtist ?>" data-cover="<?= $sArt ?>" role="button" aria-label="Play <?= $sTitle ?>" style="position: absolute; right: 1rem; bottom: 1rem; z-index: 2; cursor: pointer;">
                                <i class="ri-play-fill"></i>
                            </span>
                        </a>
                        <div class="album-card__body" style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <span style="font-size: var(--fs-xs); color: var(--accent); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; display: block; margin-bottom: 4px;">
                                    <?= $sGenre ?> &middot; <?= $sYear ?>
                                </span>
                                <h3 class="album-card__title" style="margin-bottom: 4px;">
                                    <a href="track.php?id=<?= $sId ?>"><?= $sTitle ?></a>
                                </h3>
                                <span class="album-card__meta" style="color: var(--ink-70); font-size: var(--fs-sm); display: block; margin-bottom: 1rem;">
                                    <?= $sArtist ?>
                                </span>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <a href="track.php?id=<?= $sId ?>" class="btn btn--sm btn--solid" style="flex: 1; text-align: center;">
                                    <span class="btn__label"><i class="ri-play-fill"></i> Listen</span>
                                </a>
                                <a href="track.php?id=<?= $sId ?>#reviews" class="btn btn--sm" title="Reviews & Ratings">
                                    <span class="btn__label"><i class="ri-chat-smile-2-line"></i></span>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--ink-70);">No approved songs found in the database.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
