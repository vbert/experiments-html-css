<?php
require __DIR__ . '/partials/news-card.php';

// ====================
// Dane przykładowe
// ====================

// 1. JEDYNKA (lead)
$leadItem = [
    'url' => '/wiadomosci/most-w-swieciu',
    'title' => 'Otwarcie nowego mostu w Świeciu',
    'subtitle' => 'Utrudnienia w ruchu do 20:00',
    'lead' => 'Po dwóch latach budowy nowy most na Wiśle został otwarty. Kierowcy muszą jednak liczyć się z korkami w godzinach szczytu.',
    'image' => [
        'src' => 'https://picsum.photos/id/1025/960/540',
        'srcset' => [
            '480w' => 'https://picsum.photos/id/1025/480/270',
            '720w' => 'https://picsum.photos/id/1025/720/405',
            '960w' => 'https://picsum.photos/id/1025/960/540',
        ],
        'ratio' => '16/9',
    ],
    'publishedAt' => '2025-10-13T08:30:00+02:00',
    'regions' => [
        ['name' => 'Chojnice', 'url' => '/region/chojnice'],
        ['name' => 'Świecie', 'url' => '/region/swiecie'],
    ],
    'galleryCount' => 16
];

// 2. PROMOWANE / WYRÓŻNIONE
$promotedItems = [
    [
        'url' => '/wiadomosci/festiwal-smaku',
        'title' => 'Festiwal Smaku w Tucholi już w weekend',
        'subtitle' => 'Degustacje i koncerty na rynku',
        'image' => [
            'src' => 'https://picsum.photos/id/1043/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1043/480/270',
                '960w' => 'https://picsum.photos/id/1043/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-02-02T14:45:00+02:00',
        'regions' => [
            ['name' => 'Chojnice', 'url' => '/region/chojnice'],
            ['name' => 'Świecie', 'url' => '/region/swiecie'],
        ],
        'galleryCount' => 12
    ],
    [
        'url' => '/wiadomosci/noc-muzeow',
        'title' => 'Noc Muzeów w Chojnicach: bezpłatne wejście do galerii',
        'subtitle' => 'Degustacje i koncerty na rynku',
        'image' => [
            'src' => 'https://picsum.photos/id/1060/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1060/480/270',
                '960w' => 'https://picsum.photos/id/1060/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-10-12T10:00:00+02:00',
        'regions' => [
            ['name' => 'Chojnice', 'url' => '/region/chojnice'],
            ['name' => 'Świecie', 'url' => '/region/swiecie'],
        ],
        'galleryCount' => 12
    ],
    [
        'url' => '/wiadomosci/nowy-park-w-swieciu',
        'title' => 'Nowy park miejski otwarty w Świeciu',
        'subtitle' => 'Zielona strefa dla mieszkańców',
        'image' => [
            'src' => 'https://picsum.photos/id/1011/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1011/480/270',
                '960w' => 'https://picsum.photos/id/1011/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-10-11T17:20:00+02:00',
    ],
    [
        'url' => '/wiadomosci/remont-dk91',
        'title' => 'Rozpoczyna się remont DK91 w kierunku Grudziądza',
        'subtitle' => null,
        'image' => [
            'src' => 'https://picsum.photos/id/1036/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1036/480/270',
                '960w' => 'https://picsum.photos/id/1036/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-10-10T09:10:00+02:00',
        'galleryCount' => 10
    ],
    [
        'url' => '/wiadomosci/koncert-orkiestry',
        'title' => 'Orkiestra symfoniczna zagra w parku miejskim',
        'subtitle' => 'Wstęp wolny dla wszystkich',
        'image' => [
            'src' => 'https://picsum.photos/id/1050/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1050/480/270',
                '960w' => 'https://picsum.photos/id/1050/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-10-09T18:30:00+02:00',
    ],
    [
        'url' => '/wiadomosci/bieg-charytatywny',
        'title' => 'Rusza Bieg Charytatywny „Pomocna Dłoń”',
        'subtitle' => 'Zbiórka dla szpitala dziecięcego',
        'image' => [
            'src' => 'https://picsum.photos/id/1021/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1021/480/270',
                '960w' => 'https://picsum.photos/id/1021/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-10-08T11:15:00+02:00',
        'regions' => [
            ['name' => 'Chojnice', 'url' => '/region/chojnice'],
            ['name' => 'Świecie', 'url' => '/region/swiecie'],
        ],
        'galleryCount' => 5
    ],
];
$promotedChunks = array_chunk($promotedItems ?? [], 3);

// 3. LISTA POZOSTAŁYCH
$pagedItems = [
    [
        'url' => '/wiadomosci/warsztaty-fotograficzne',
        'title' => 'Warsztaty fotograficzne w bibliotece miejskiej',
        'subtitle' => null,
        'image' => [
            'src' => 'https://picsum.photos/id/1003/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1003/480/270',
                '960w' => 'https://picsum.photos/id/1003/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-10-07T12:00:00+02:00',
        'regions' => [
            ['name' => 'Chojnice', 'url' => '/region/chojnice'],
            ['name' => 'Świecie', 'url' => '/region/swiecie'],
        ],
    ],
    [
        'url' => '/wiadomosci/sesja-rady-miasta',
        'title' => 'Sesja rady miasta: nowe inwestycje w planach',
        'subtitle' => null,
        'image' => [
            'src' => 'https://picsum.photos/id/1005/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1005/480/270',
                '960w' => 'https://picsum.photos/id/1005/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-10-06T09:00:00+02:00',
        'regions' => [
            ['name' => 'Chojnice', 'url' => '/region/chojnice'],
            ['name' => 'Świecie', 'url' => '/region/swiecie'],
        ],
    ],
    [
        'url' => '/wiadomosci/nowy-plac-zabaw',
        'title' => 'Nowy plac zabaw na osiedlu Południe',
        'subtitle' => 'Powstał z budżetu obywatelskiego',
        'image' => [
            'src' => 'https://picsum.photos/id/1006/960/540',
            'srcset' => [
                '480w' => 'https://picsum.photos/id/1006/480/270',
                '960w' => 'https://picsum.photos/id/1006/960/540',
            ],
            'ratio' => '16/9',
        ],
        'publishedAt' => '2025-10-05T15:45:00+02:00',
        'regions' => [
            ['name' => 'Chojnice', 'url' => '/region/chojnice'],
            ['name' => 'Świecie', 'url' => '/region/swiecie'],
        ],
    ],
];
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Cards</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="page">

        <!-- MAIN: JEDYNKA + PROMOWANE -->
        <main>
            <section class="hero">
                <?php renderNewsCard($leadItem, [
                    'variant' => 'lead',
                    'headingTag' => 'h1',
                    // 'sizes' => '(min-width: 1400px) 933px, (min-width: 1024px) 66vw, 100vw'
                ]); ?>
            </section>

            <section class="featured-strip">
                <?php renderFeaturedStrip($promotedChunks[0] ?? [], [
                    // 'size' => '(min-width: 1400px) 456px, (min-width: 1024px) 33vw, (min-width: 768px) 45vw, 100vw'
                ]); ?>
            </section>

            <div class="ad-slot">
                <!-- reklama / moduł -->
                <!-- <h2>reklama / moduł</h2> -->
            </div>

            <section class="featured-strip">
                <?php renderFeaturedStrip($promotedChunks[1] ?? [], [
                    // 'size' => '(min-width: 1400px) 456px, (min-width: 1024px) 33vw, (min-width: 768px) 45vw, 100vw'
                ]); ?>
            </section>
        </main>

        <!-- POD MAIN: 2/3 LISTA + 1/3 ASIDE -->
        <div class="below-main">
            <section class="list-flex">
                <?php foreach ($pagedItems as $n) {
                    renderNewsCard($n, [
                        'variant' => 'list',
                        'headingTag' => 'h3',
                        // 'sizes' => '(min-width: 1024px) 360px, 100vw'
                    ]);
                } ?>
            </section>

            <aside class="aside-col">
                <section class="aside-box">
                    <div class="aside-box__label">Na skróty</div>
                    <ul class="quick-links">
                        <li><a href="/na-zywo">Słuchaj na żywo <span class="quick-links__meta">RMF FM</span></a></li>
                        <li><a href="/pogoda">Pogoda na jutro <span class="quick-links__meta">+ radar</span></a></li>
                        <li><a href="/program">Program dnia <span class="quick-links__meta">godz. 6-24</span></a></li>
                        <li><a href="/kontakt">Kontakt z redakcją <span class="quick-links__meta">napisz do nas</span></a></li>
                    </ul>
                </section>

                <section class="aside-box">
                    <div class="aside-box__label">Najczęściej czytane</div>
                    <ol class="top-list">
                        <li>
                            <a href="/wiadomosci/awaria-wodociagow">Awaria wodociągów: przerwy w dostawach na północy miasta</a>
                            <span class="top-list__meta">09:15</span>
                        </li>
                        <li>
                            <a href="/wiadomosci/nowy-tramwaj">Nowy tramwaj w testach. Jak wypada na ulicach?</a>
                            <span class="top-list__meta">08:40</span>
                        </li>
                        <li>
                            <a href="/sport/derby">Derby regionu: emocjonujący remis 2:2</a>
                            <span class="top-list__meta">wczoraj</span>
                        </li>
                        <li>
                            <a href="/magazyn/kuchnia">Kuchnia: pierogi z leśnymi grzybami według babcinego przepisu</a>
                            <span class="top-list__meta">wideo</span>
                        </li>
                    </ol>
                </section>

                <section class="aside-box aside-box--highlight">
                    <div class="aside-box__label">Newsletter</div>
                    <p class="aside-box__desc">Najważniejsze wiadomości dnia na Twojej skrzynce o 7:00. Bez spamu, tylko lokalne konkrety.</p>
                    <form class="newsletter-form" action="#" method="post">
                        <label class="sr-only" for="newsletter-email">Adres e-mail</label>
                        <input type="email" id="newsletter-email" name="email" placeholder="Twój e-mail" required>
                        <button type="submit">Zapisz mnie</button>
                    </form>
                    <p class="aside-box__note">W każdej chwili możesz się wypisać jednym kliknięciem.</p>
                </section>

                <section class="aside-box">
                    <div class="aside-box__label">Podcast dnia</div>
                    <div class="audio-card">
                        <div class="audio-card__time">12:45</div>
                        <p class="audio-card__title">Rozmowa z burmistrzem: kiedy nowe ścieżki rowerowe?</p>
                        <p class="audio-card__meta">18 min · prowadzi Katarzyna Nowicka</p>
                        <div class="audio-card__actions">
                            <button type="button" class="btn">▶ Posłuchaj</button>
                            <button type="button" class="btn btn--ghost">Zapisz na później</button>
                        </div>
                    </div>
                </section>
            </aside>
        </div>

    </div>
</body>

</html>
