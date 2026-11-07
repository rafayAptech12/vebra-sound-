<?php
$pageTitle = 'Journal & News — vebra';
$pageDesc = 'Studio diaries, tour announcements and press from the vebra camp.';
$bodyClass = 'theme-magenta';
$activePage = 'news';
$activeSub = 'news';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE CAPTION
    ============================================= -->
    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="assets/images/news-2.jpg" alt="" data-parallax-speed="0.12">
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page">News</li>
                </ul>

                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    Journal
                    <span class="page-caption__ghost" aria-hidden="true">Journal</span>
                </h1>

                <p class="page-caption__text" data-reveal="up" data-delay="0.2">
                    Studio diaries, tour announcements and the odd long read from the road.
                    Written by the band, published when we have something worth saying.
                </p>

                <div class="page-caption__meta" data-reveal="up" data-delay="0.3">
                    <div class="page-caption__stat"><strong data-count-to="7">0</strong><span>Latest posts</span></div>
                    <div class="page-caption__stat"><strong data-count-to="4">0</strong><span>Categories</span></div>
                    <div class="page-caption__stat"><strong data-count-to="2026">0</strong><span>Season</span></div>
                </div>
            </div>
        </div>
    </section>


    <!-- LATEST NEWS
    ============================================= -->
    <section class="section">
        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">Latest news</span>
                    <h2 class="section-head__title" data-split="lines">From the <em>vebra</em> camp</h2>
                </div>
                <p class="section-head__text">Everything we publish, newest first — records, rooms and the people who fill them.</p>
            </div>

            <div class="news-grid news-grid--page" data-reveal-group data-stagger="0.08">
                <!-- Feature -->
                <div>
                    <article class="media-card news-card">
                        <div class="media-card__media" data-cursor="Read">
                            <img src="assets/images/news-1.jpg" alt="Inside the making of Nightform">
                            <span class="pill pill--accent media-card__badge">Feature</span>
                            <span class="card-icon" aria-hidden="true"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                        <div class="media-card__body">
                            <div class="media-card__meta">
                                <span class="news-card__category">Studio</span>
                                <span class="news-card__date">March 04, 2026</span>
                            </div>
                            <h3 class="media-card__title"><a href="single-news.php?article=making-of-nightform">Inside the making of Nightform</a></h3>
                            <p class="media-card__excerpt">
                                Six weeks in a Catskills barn, two broken tape machines and a record that
                                refused to be finished in the city. Our engineer walks through every room
                                we recorded in and why the last take was always the one.
                            </p>
                            <div class="media-card__foot">
                                <a href="single-news.php?article=making-of-nightform" class="link-arrow" data-magnetic="0.2">Read the story <i class="ri-arrow-right-up-line"></i></a>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Post -->
                <div>
                    <article class="media-card news-card">
                        <div class="media-card__media" data-cursor="Read">
                            <img src="assets/images/news-2.jpg" alt="2026 world tour routing announced">
                            <span class="card-icon" aria-hidden="true"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                        <div class="media-card__body">
                            <div class="media-card__meta">
                                <span class="news-card__category">Tour</span>
                                <span class="news-card__date">Feb 21, 2026</span>
                            </div>
                            <h3 class="media-card__title"><a href="single-news.php?article=tour-routing">The 2026 routing is finally locked</a></h3>
                            <p class="media-card__excerpt">Fourteen countries, eighty-six rooms and one very long ferry crossing. Presale opens Friday.</p>
                        </div>
                    </article>
                </div>

                <!-- Post -->
                <div>
                    <article class="media-card news-card">
                        <div class="media-card__media" data-cursor="Read">
                            <img src="assets/images/news-3.jpg" alt="Halogen video shoot">
                            <span class="card-icon" aria-hidden="true"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                        <div class="media-card__body">
                            <div class="media-card__meta">
                                <span class="news-card__category">Video</span>
                                <span class="news-card__date">Feb 09, 2026</span>
                            </div>
                            <h3 class="media-card__title"><a href="single-news.php?article=halogen-video">Shooting &ldquo;Halogen&rdquo; in one take</a></h3>
                            <p class="media-card__excerpt">Four minutes, one Steadicam and a disused tram depot in Rotterdam. Nobody fell over.</p>
                        </div>
                    </article>
                </div>

                <!-- Post -->
                <div>
                    <article class="media-card news-card">
                        <div class="media-card__media" data-cursor="Read">
                            <img src="assets/images/gallery-4.jpg" alt="Backstage at Eventim Apollo">
                            <span class="card-icon" aria-hidden="true"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                        <div class="media-card__body">
                            <div class="media-card__meta">
                                <span class="news-card__category">Live</span>
                                <span class="news-card__date">Jan 30, 2026</span>
                            </div>
                            <h3 class="media-card__title"><a href="single-news.php?article=before-a-show">What actually happens before a show</a></h3>
                            <p class="media-card__excerpt">Soundcheck, silence, terrible sandwiches. A photographer followed us for a full day in London.</p>
                        </div>
                    </article>
                </div>

                <!-- Post -->
                <div>
                    <article class="media-card news-card">
                        <div class="media-card__media" data-cursor="Read">
                            <img src="assets/images/gallery-5.jpg" alt="Modular synth rig">
                            <span class="card-icon" aria-hidden="true"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                        <div class="media-card__body">
                            <div class="media-card__meta">
                                <span class="news-card__category">Gear</span>
                                <span class="news-card__date">Jan 12, 2026</span>
                            </div>
                            <h3 class="media-card__title"><a href="single-news.php?article=modular-rig">The modular rig that ate side B</a></h3>
                            <p class="media-card__excerpt">Nine modules, one dying spring reverb and the patch we could never quite recreate twice.</p>
                        </div>
                    </article>
                </div>

                <!-- Post -->
                <div>
                    <article class="media-card news-card">
                        <div class="media-card__media" data-cursor="Read">
                            <img src="assets/images/album-3.jpg" alt="Nightform artwork process">
                            <span class="card-icon" aria-hidden="true"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                        <div class="media-card__body">
                            <div class="media-card__meta">
                                <span class="news-card__category">Design</span>
                                <span class="news-card__date">Dec 18, 2025</span>
                            </div>
                            <h3 class="media-card__title"><a href="single-news.php?article=drawing-nightform">Drawing the Nightform sleeve</a></h3>
                            <p class="media-card__excerpt">Forty rejected covers, one foil block and the argument that produced the final artwork.</p>
                        </div>
                    </article>
                </div>

                <!-- Post -->
                <div>
                    <article class="media-card news-card">
                        <div class="media-card__media" data-cursor="Read">
                            <img src="assets/images/gallery-1.jpg" alt="vebra at the Brooklyn studio">
                            <span class="card-icon" aria-hidden="true"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                        <div class="media-card__body">
                            <div class="media-card__meta">
                                <span class="news-card__category">Band</span>
                                <span class="news-card__date">Nov 27, 2025</span>
                            </div>
                            <h3 class="media-card__title"><a href="single-news.php?article=ten-years">Ten years of Rivington Street</a></h3>
                            <p class="media-card__excerpt">The lease is signed for another decade. A short history of the room where all five records began.</p>
                        </div>
                    </article>
                </div>
            </div>

            <nav class="pagination-vebra" aria-label="News pagination">
                <span class="is-current" aria-current="page">1</span>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#" aria-label="Next page"><i class="ri-arrow-right-line"></i></a>
            </nav>
        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
