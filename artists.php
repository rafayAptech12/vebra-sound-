<?php
require_once __DIR__ . '/includes/db.php';

/* =========================================================
   FETCH ARTISTS
   Actual database: artists
   Fields: id, name, bio, image, created_at
   ========================================================= */

$dbArtists = [];

$res = mysqli_query(
    $conn,
    "SELECT
        a.id,
        a.name,
        a.bio,
        a.image,
        COUNT(DISTINCT s.id) AS release_count
     FROM artists a
     LEFT JOIN songs s
        ON s.artist_id = a.id
        AND s.status = 'approved'
     GROUP BY a.id, a.name, a.bio, a.image
     ORDER BY a.id ASC"
);

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $dbArtists[] = $row;
    }
}

/* Exclude demo artist when real artists exist */
$displayArtists = array_filter($dbArtists, function ($artist) {
    return !empty($artist['image'])
        || $artist['name'] !== 'VEBRA Demo Artist';
});

if (empty($displayArtists)) {
    $displayArtists = $dbArtists;
}

$displayArtists = array_values($displayArtists);

$pageTitle = 'Artists & The Band — VEBRA';
$activePage = 'artists';
$activeSub = 'artists';
$bodyClass = 'theme-magenta';

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE CAPTION -->
    <section class="page-caption" data-parallax-scope>

        <div class="page-caption__bg" aria-hidden="true">
            <img
                src="assets/images/artists-page-cover.jpg"
                alt=""
                data-parallax-speed="0.12"
            >
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">

                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <span aria-current="page">Artists</span>
                </nav>

                <h1 class="page-caption__title"
                    data-split="chars"
                    data-split-hero>
                    The artists
                    <span class="page-caption__ghost" aria-hidden="true">
                        The artists
                    </span>
                </h1>

                <p class="page-caption__text"
                   data-reveal="up"
                   data-delay="0.2">
                    Discover the artists behind the music.
                    Explore the voices, creators and performers shaping the VEBRA sound.
                </p>

                <div class="page-caption__meta"
                     data-reveal="up"
                     data-delay="0.3">

                    <div class="page-caption__stat">
                        <strong
                            data-count-to="<?= count($displayArtists) ?>"
                            data-count-suffix="">0</strong>
                        <span>Artists</span>
                    </div>

                    <div class="page-caption__stat">
                        <strong
                            data-count-to="<?= array_sum(array_map(function ($artist) {
                                return (int) $artist['release_count'];
                            }, $displayArtists)) ?>"
                            data-count-suffix="">0</strong>
                        <span>Approved releases</span>
                    </div>

                    <div class="page-caption__stat">
                        <strong>VEBRA</strong>
                        <span>Artist community</span>
                    </div>

                </div>

            </div>
        </div>
    </section>


    <!-- ARTISTS GRID -->
    <section class="section">
        <div class="container-vebra">

            <!-- SINGLE ADD ARTIST CTA -->
            <div class="section-head" style="margin-bottom:3rem;">
                <div>
                    <span class="section-label">Join the collective</span>
                    <h2 class="section-head__title">
                        Are you an <em>artist?</em>
                    </h2>
                </div>

                <p class="section-head__text">
                    Introduce yourself and apply to become part of the VEBRA artist directory.
                </p>

                <a href="add-artist.php" class="btn btn--solid">
                    <span class="btn__label">
                        <i class="ri-add-line"></i>
                        Add Artist
                    </span>
                </a>
            </div>


            <!-- ROSTER HEADING -->
            <div class="section-head">
                <div>
                    <span class="section-label">The roster</span>
                    <h2 class="section-head__title" data-split="lines">
                        Meet the <em>artists</em>
                    </h2>
                </div>

                <p class="section-head__text">
                    Explore the musicians and creators behind VEBRA's
                    music catalogue.
                </p>
            </div>


            <!-- ARTIST CARDS -->
            <div class="artists-grid"
                 data-reveal-group
                 data-stagger="0.09">

                <?php if (!empty($displayArtists)): ?>

                    <?php
                    $memberIdx = 1;

                    foreach ($displayArtists as $art):

                        $artId = (int) $art['id'];

                        $artName = htmlspecialchars(
                            $art['name'] ?? 'Artist',
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $artImg = !empty($art['image'])
                            ? htmlspecialchars($art['image'], ENT_QUOTES, 'UTF-8')
                            : 'assets/images/team-1.jpg';

                        $artBio = trim($art['bio'] ?? '');

                        $roleParts = preg_split('/[\.\n]/', $artBio);

                        $artRole = !empty($roleParts[0])
                            && mb_strlen(trim($roleParts[0])) < 40
                            ? htmlspecialchars(trim($roleParts[0]), ENT_QUOTES, 'UTF-8')
                            : 'Artist / Band';

                        $edgeLabel = str_pad(
                            $memberIdx++,
                            2,
                            '0',
                            STR_PAD_LEFT
                        ) . ' / Artist';
                    ?>

                        <a
                            href="single-artist.php?id=<?= $artId ?>"
                            class="member-card"
                            data-cursor="Profile"
                        >

                            <div class="member-card__media">
                                <img
                                    src="<?= $artImg ?>"
                                    alt="<?= $artName ?>"
                                    loading="lazy"
                                >
                            </div>

                            <span class="member-card__edge">
                                <?= $edgeLabel ?>
                            </span>

                            <div class="member-card__body">
                                <div>
                                    <h3 class="member-card__name">
                                        <?= $artName ?>
                                    </h3>

                                    <span class="member-card__role">
                                        <?= $artRole ?>
                                    </span>
                                </div>

                                <i class="ri-arrow-right-up-line"
                                   aria-hidden="true"></i>
                            </div>

                        </a>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>No artists have been added yet.</p>

                <?php endif; ?>

            </div>

        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
