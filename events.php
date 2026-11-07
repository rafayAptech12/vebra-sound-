<?php
$pageTitle = 'All Events — vebra';
$pageDesc = 'Every vebra live date for 2026 — nine shows across Europe and a homecoming night in Brooklyn.';
$bodyClass = 'theme-magenta';
$activePage = 'events';
$activeSub = 'events';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE CAPTION TYPE 2
    ============================================= -->
    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="assets/images/page-cover-2.jpg" alt="" data-parallax-speed="0.15">
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep">/</li>
                    <li aria-current="page">Events</li>
                </ul>

                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    All events
                    <span class="page-caption__ghost" aria-hidden="true">On tour</span>
                </h1>

                <p class="page-caption__text" data-reveal="up" data-delay="0.25">
                    The full <strong>Nightform</strong> run &mdash; nine shows across Europe and one homecoming
                    night in Brooklyn. New dates are announced the first Monday of every month.
                </p>

                <div class="page-caption__meta" data-reveal="up" data-delay="0.35">
                    <div class="page-caption__stat"><strong data-count-to="9">0</strong><span>Shows</span></div>
                    <div class="page-caption__stat"><strong data-count-to="8">0</strong><span>Cities</span></div>
                    <div class="page-caption__stat"><strong data-count-to="2">0</strong><span>Sold out</span></div>
                    <div class="page-caption__stat"><strong>2026</strong><span>Season</span></div>
                </div>
            </div>
        </div>
    </section>


    <!-- UPCOMING GIGS
    ============================================= -->
    <section class="section gigs" data-tabs-scope data-preview-scope>
        <div class="gigs__bg" aria-hidden="true">
            <img src="assets/images/gigs-bg.png" alt="">
        </div>

        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">Upcoming gigs</span>
                    <h2 class="section-head__title" data-split="lines">On the <em>road</em> in 2026</h2>
                </div>
                <div class="stack stack-3">
                    <p class="section-head__text">Tickets go on general sale at 10:00 local time. The mailing list gets them a day early.</p>
                    <a href="contact.php" class="link-arrow" data-magnetic="0.2">Booking enquiries <i class="ri-arrow-right-up-line"></i></a>
                </div>
            </div>

            <div class="tabs" data-tabs role="tablist" aria-label="Filter gigs by month">
                <button type="button" class="tabs__btn is-active" data-tab="all" role="tab" id="tab-all" aria-controls="panel-all" aria-selected="true">All<sup>9</sup></button>
                <button type="button" class="tabs__btn" data-tab="may" role="tab" id="tab-may" aria-controls="panel-may" aria-selected="false">May<sup>3</sup></button>
                <button type="button" class="tabs__btn" data-tab="june" role="tab" id="tab-june" aria-controls="panel-june" aria-selected="false">June<sup>3</sup></button>
                <button type="button" class="tabs__btn" data-tab="july" role="tab" id="tab-july" aria-controls="panel-july" aria-selected="false">July<sup>3</sup></button>
                <span class="tabs__indicator" aria-hidden="true"></span>
            </div>

                <div class="tabs__panel is-active" data-tab-panel="all" role="tabpanel" id="panel-all" aria-labelledby="tab-all">
                    <div class="gigs__list">
                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-1.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">14</span>
                                <span class="gig-row__month"><strong>May</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">London</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Eventim Apollo</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>21:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=london" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-2.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">19</span>
                                <span class="gig-row__month"><strong>May</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Manchester</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Albert Hall</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>20:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=manchester" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-3.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">25</span>
                                <span class="gig-row__month"><strong>May</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Amsterdam</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>AFAS Live</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>19:30</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=amsterdam" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row gig-row--sold" data-reveal="up" data-preview-src="assets/images/gallery-4.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">02</span>
                                <span class="gig-row__month"><strong>June</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Vienna</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Wiener Stadthalle</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>20:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=vienna" class="gig-row__sold" style="display:inline-block; text-decoration:none;">Sold out</a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-5.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">10</span>
                                <span class="gig-row__month"><strong>June</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Cologne</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Lanxess Arena</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>22:30</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=cologne" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-6.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">21</span>
                                <span class="gig-row__month"><strong>June</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Paris</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Le Trianon</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>20:30</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=paris" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-7.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">04</span>
                                <span class="gig-row__month"><strong>July</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Lisbon</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Coliseu dos Recreios</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>21:30</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=lisbon" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row gig-row--sold" data-reveal="up" data-preview-src="assets/images/gallery-8.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">12</span>
                                <span class="gig-row__month"><strong>July</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Barcelona</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Sala Apolo</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>22:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=barcelona" class="gig-row__sold" style="display:inline-block; text-decoration:none;">Sold out</a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-5.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">26</span>
                                <span class="gig-row__month"><strong>July</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Brooklyn</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Kings Theatre</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>20:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=brooklyn" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tabs__panel" data-tab-panel="may" role="tabpanel" id="panel-may" aria-labelledby="tab-may">
                    <div class="gigs__list">
                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-1.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">14</span>
                                <span class="gig-row__month"><strong>May</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">London</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Eventim Apollo</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>21:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=london" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-2.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">19</span>
                                <span class="gig-row__month"><strong>May</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Manchester</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Albert Hall</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>20:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=manchester" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-3.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">25</span>
                                <span class="gig-row__month"><strong>May</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Amsterdam</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>AFAS Live</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>19:30</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=amsterdam" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tabs__panel" data-tab-panel="june" role="tabpanel" id="panel-june" aria-labelledby="tab-june">
                    <div class="gigs__list">
                        <div class="gig-row gig-row--sold" data-reveal="up" data-preview-src="assets/images/gallery-4.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">02</span>
                                <span class="gig-row__month"><strong>June</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Vienna</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Wiener Stadthalle</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>20:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=vienna" class="gig-row__sold" style="display:inline-block; text-decoration:none;">Sold out</a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-5.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">10</span>
                                <span class="gig-row__month"><strong>June</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Cologne</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Lanxess Arena</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>22:30</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=cologne" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-6.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">21</span>
                                <span class="gig-row__month"><strong>June</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Paris</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Le Trianon</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>20:30</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=paris" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tabs__panel" data-tab-panel="july" role="tabpanel" id="panel-july" aria-labelledby="tab-july">
                    <div class="gigs__list">
                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-7.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">04</span>
                                <span class="gig-row__month"><strong>July</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Lisbon</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Coliseu dos Recreios</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>21:30</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=lisbon" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>

                        <div class="gig-row gig-row--sold" data-reveal="up" data-preview-src="assets/images/gallery-8.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">12</span>
                                <span class="gig-row__month"><strong>July</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Barcelona</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Sala Apolo</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>22:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=barcelona" class="gig-row__sold" style="display:inline-block; text-decoration:none;">Sold out</a>
                            </div>
                        </div>

                        <div class="gig-row" data-reveal="up" data-preview-src="assets/images/gallery-5.jpg">
                            <div class="gig-row__date">
                                <span class="gig-row__day">26</span>
                                <span class="gig-row__month"><strong>July</strong>2026</span>
                            </div>
                            <div class="gig-row__venue">
                                <h3 class="gig-row__city">Brooklyn</h3>
                                <span class="gig-row__place"><i class="ri-map-pin-line"></i>Kings Theatre</span>
                            </div>
                            <span class="gig-row__time"><i class="ri-time-line"></i>20:00</span>
                            <div class="gig-row__action">
                                <a href="single-event.php?city=brooklyn" class="btn btn--sm" data-magnetic="0.25"><span class="btn__label">Buy ticket</span></a>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="row-preview" aria-hidden="true"><img src="assets/images/gallery-1.jpg" alt=""></div>

            <nav class="pagination-vebra" aria-label="Events pages">
                <span class="is-current">1</span>
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
