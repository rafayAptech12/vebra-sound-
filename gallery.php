<?php
$pageTitle = 'Gallery — vebra';
$pageDesc = 'Photographs from the vebra world tour, backstage and the Nightform studio sessions.';
$bodyClass = 'theme-magenta';
$activePage = 'more';
$activeSub = 'gallery';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE CAPTION
    ============================================= -->
    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="assets/images/gallery-2-4.jpg" alt="" data-parallax-speed="0.12">
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">
                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <span aria-current="page">Gallery</span>
                </nav>

                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    Gallery
                    <span class="page-caption__ghost" aria-hidden="true">Gallery</span>
                </h1>

                <p class="page-caption__text" data-reveal="up" data-delay="0.2">
                    Grain, sweat and gaffer tape. Shot on tour, backstage and in the Rivington Street room
                    where <em>Nightform</em> was made.
                </p>

                <div class="page-caption__meta" data-reveal="up" data-delay="0.3">
                    <div class="page-caption__stat">
                        <strong data-count-to="17" data-count-suffix="">0</strong>
                        <span>Photographs</span>
                    </div>
                    <div class="page-caption__stat">
                        <strong data-count-to="9" data-count-suffix="">0</strong>
                        <span>Cities</span>
                    </div>
                    <div class="page-caption__stat">
                        <strong>2024&ndash;2026</strong>
                        <span>Archive</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- GALLERY
    ============================================= -->
    <section class="section" data-filter-scope>
        <div class="container-vebra">
            <div class="gallery-toolbar">
                <div class="filter-list">
                    <button type="button" class="filter-list__btn is-active" data-filter="*" aria-pressed="true">All</button>
                    <button type="button" class="filter-list__btn" data-filter="live" aria-pressed="false">Live</button>
                    <button type="button" class="filter-list__btn" data-filter="backstage" aria-pressed="false">Backstage</button>
                    <button type="button" class="filter-list__btn" data-filter="studio" aria-pressed="false">Studio</button>
                </div>
                <span class="gallery-toolbar__count">17 photographs</span>
            </div>

            <div class="filter-grid filter-grid--masonry" data-filter-grid data-reveal-group data-stagger="0.06">
                <div class="filter-grid__item" data-category="live">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-1.jpg" data-caption="Wall of light — Eventim Apollo, London" data-cursor="View">
                        <img src="assets/images/gallery-2-1.jpg" alt="Wall of light — Eventim Apollo, London" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Wall of light</h2>
                                <span class="gallery-tile__meta">Eventim Apollo, London</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="studio">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-3-1.jpg" data-caption="Take nineteen — Rivington Street, Brooklyn" data-cursor="View">
                        <img src="assets/images/gallery-3-1.jpg" alt="Take nineteen — Rivington Street, Brooklyn" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Take nineteen</h2>
                                <span class="gallery-tile__meta">Rivington Street, Brooklyn</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="backstage">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-2.jpg" data-caption="Ten minutes out — Green room, Berlin" data-cursor="View">
                        <img src="assets/images/gallery-2-2.jpg" alt="Ten minutes out — Green room, Berlin" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Ten minutes out</h2>
                                <span class="gallery-tile__meta">Green room, Berlin</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="live">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-3-2.jpg" data-caption="Front row static — AFAS Live, Amsterdam" data-cursor="View">
                        <img src="assets/images/gallery-3-2.jpg" alt="Front row static — AFAS Live, Amsterdam" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Front row static</h2>
                                <span class="gallery-tile__meta">AFAS Live, Amsterdam</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="studio">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-3.jpg" data-caption="Tape and patience — Nightform sessions" data-cursor="View">
                        <img src="assets/images/gallery-2-3.jpg" alt="Tape and patience — Nightform sessions" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Tape and patience</h2>
                                <span class="gallery-tile__meta">Nightform sessions</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="live">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-3-3.jpg" data-caption="Encore smoke — Lanxess Arena, Cologne" data-cursor="View">
                        <img src="assets/images/gallery-3-3.jpg" alt="Encore smoke — Lanxess Arena, Cologne" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Encore smoke</h2>
                                <span class="gallery-tile__meta">Lanxess Arena, Cologne</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="backstage">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-4.jpg" data-caption="Set list, rewritten — Wiener Stadthalle" data-cursor="View">
                        <img src="assets/images/gallery-2-4.jpg" alt="Set list, rewritten — Wiener Stadthalle" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Set list, rewritten</h2>
                                <span class="gallery-tile__meta">Wiener Stadthalle</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="live">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-3-4.jpg" data-caption="Hands up at midnight — Primavera Sound" data-cursor="View">
                        <img src="assets/images/gallery-3-4.jpg" alt="Hands up at midnight — Primavera Sound" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Hands up at midnight</h2>
                                <span class="gallery-tile__meta">Primavera Sound</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="studio">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-5.jpg" data-caption="Juno-60 warm-up — Rivington Street, Brooklyn" data-cursor="View">
                        <img src="assets/images/gallery-2-5.jpg" alt="Juno-60 warm-up — Rivington Street, Brooklyn" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Juno-60 warm-up</h2>
                                <span class="gallery-tile__meta">Rivington Street, Brooklyn</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="backstage">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-3-5.jpg" data-caption="Load-in at seven — Paradiso, Amsterdam" data-cursor="View">
                        <img src="assets/images/gallery-3-5.jpg" alt="Load-in at seven — Paradiso, Amsterdam" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Load-in at seven</h2>
                                <span class="gallery-tile__meta">Paradiso, Amsterdam</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="live">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-6.jpg" data-caption="Second chorus — Roskilde Festival" data-cursor="View">
                        <img src="assets/images/gallery-2-6.jpg" alt="Second chorus — Roskilde Festival" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Second chorus</h2>
                                <span class="gallery-tile__meta">Roskilde Festival</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="studio">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-3-6.jpg" data-caption="Room mics only — Nightform sessions" data-cursor="View">
                        <img src="assets/images/gallery-3-6.jpg" alt="Room mics only — Nightform sessions" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Room mics only</h2>
                                <span class="gallery-tile__meta">Nightform sessions</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="backstage">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-7.jpg" data-caption="Van window, Utah — Tour diary 2026" data-cursor="View">
                        <img src="assets/images/gallery-2-7.jpg" alt="Van window, Utah — Tour diary 2026" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Van window, Utah</h2>
                                <span class="gallery-tile__meta">Tour diary 2026</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="live">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/carousel-1.jpg" data-caption="Last note, held — Le Trabendo, Paris" data-cursor="View">
                        <img src="assets/images/gallery-7.jpg" alt="Last note, held — Le Trabendo, Paris" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Last note, held</h2>
                                <span class="gallery-tile__meta">Le Trabendo, Paris</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="studio">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-8.jpg" data-caption="Strings for side B — Brooklyn, NY" data-cursor="View">
                        <img src="assets/images/gallery-2-8.jpg" alt="Strings for side B — Brooklyn, NY" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Strings for side B</h2>
                                <span class="gallery-tile__meta">Brooklyn, NY</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="live">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/carousel-2.jpg" data-caption="Sold out, again — Eventim Apollo, London" data-cursor="View">
                        <img src="assets/images/gallery-8.jpg" alt="Sold out, again — Eventim Apollo, London" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">Sold out, again</h2>
                                <span class="gallery-tile__meta">Eventim Apollo, London</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="filter-grid__item" data-category="backstage">
                    <a class="gallery-tile" data-lightbox="image" data-gallery="main" data-src="assets/images/gallery-2-9.jpg" data-caption="After the encore — Dressing room, Wien" data-cursor="View">
                        <img src="assets/images/gallery-2-9.jpg" alt="After the encore — Dressing room, Wien" loading="lazy">
                        <span class="gallery-tile__zoom" aria-hidden="true"><i class="ri-add-line"></i></span>
                        <div class="gallery-tile__caption">
                            <div>
                                <h2 class="gallery-tile__title">After the encore</h2>
                                <span class="gallery-tile__meta">Dressing room, Wien</span>
                            </div>
                        </div>
                    </a>
                </div>

                <p class="filter-empty">No photographs in this set yet — try another filter.</p>
            </div>
        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
