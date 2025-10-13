<?php
/**
 * NEWS CARDS – proste komponenty frontendu (PHP 8.2)
 * ---------------------------------------------------
 *  - renderNewsCard($data, $opts = [])
 *  - renderFeaturedStrip($items, $opts = [])
 *
 * Dane $data (najważniejsze pola):
 *   - url, title, subtitle?, lead? (tylko w 'lead'), regions? [ [name,url], ... ]
 *   - image: ['src' => '', 'srcset' => ['480w' => '...', '960w' => '...'], 'ratio' => '16/9']
 *   - publishedAt: ISO-8601 string
 *   - galleryCount?: int  (gdy >0 -> pokaż obok daty z ikoną aparatu)
 *
 * Opcje $opts:
 *   - variant: 'lead'|'featured'|'list' (default 'list')
 *   - showDate: bool (default true)
 *   - headingTag: 'h1'..'h6' (default 'h2')
 *   - sizes: string dla <img sizes="..."> (ma domyślne wartości per wariant)
 *   - return: bool -> zwróć HTML zamiast echo
 */

if (!function_exists('nc_e')) {
    function nc_e(?string $v): string
    {
        return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

function renderNewsCard(array $data, array $opts = [])
{
    $opts = array_merge([
        'variant' => 'list',
        'showDate' => true,
        'headingTag' => 'h2',
        'sizes' => null,
        'return' => false,
    ], $opts);

    // --- normalizacja danych ---
    $title = nc_e($data['title'] ?? '');
    $subtitle = isset($data['subtitle']) ? nc_e($data['subtitle']) : null;
    $lead = isset($data['lead']) ? nc_e($data['lead']) : null;
    $url = nc_e($data['url'] ?? '#');
    $imgAlt = nc_e($data['imgAlt'] ?? ($title ?: ''));
    $regions = $data['regions'] ?? [];
    $galleryCount = isset($data['galleryCount']) ? (int) $data['galleryCount'] : 0;

    // obraz
    $image = $data['image'] ?? [];
    $ratio = $image['ratio'] ?? '16/9';
    $src = $image['src'] ?? '';
    $srcset = '';
    if (!empty($image['srcset']) && is_array($image['srcset'])) {
        $pairs = [];
        foreach ($image['srcset'] as $w => $path) {
            $pairs[] = nc_e($path) . ' ' . nc_e($w);
        }
        $srcset = implode(', ', $pairs);
    }

    // sizes domyślne
    $sizes = $opts['sizes'] ?? match ($opts['variant']) {
        'lead' => '(min-width: 1200px) 933px, (min-width: 1024px) 800px, 100vw', // ~2/3 z 1400px
        'featured' => '(min-width: 1024px) 400px, (min-width: 768px) 45vw, 100vw',
        default => '(min-width: 1200px) 420px, (min-width: 1024px) 380px, 100vw',
    };

    // data publikacji
    $publishedIso = $data['publishedAt'] ?? null;
    $publishedHuman = null;
    if ($publishedIso) {
        try {
            $dt = new DateTime($publishedIso);
            $publishedHuman = $dt->format('d.m.Y, H:i');
        } catch (Throwable $e) {
            $publishedHuman = null;
        }
    }

    // gotowe kawałki HTML (żeby oddzielić logikę od szablonu)
    $Heading = in_array(strtolower($opts['headingTag']), ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)
        ? strtolower($opts['headingTag']) : 'h2';

    // regiony
    $regionsHtml = '';
    if (!empty($regions)) {
        $links = [];
        foreach ($regions as $r) {
            $rName = nc_e($r['name'] ?? '');
            if ($rName === '')
                continue;
            $rUrl = nc_e($r['url'] ?? '#');
            $links[] = "<a href=\"{$rUrl}\" class=\"news-card__region\" itemprop=\"articleSection\">{$rName}</a>";
        }
        if ($links) {
            $regionsHtml = '<nav class="news-card__regions" aria-label="Regiony">' .
                implode('<span class="news-card__region-sep">◆</span>', $links) .
                '</nav>';
        }
    }

    // podtytuł, lead
    $subtitleHtml = $subtitle ? "<p class=\"news-card__subtitle\" itemprop=\"alternativeHeadline\">{$subtitle}</p>" : '';
    $leadHtml = ($opts['variant'] === 'lead' && $lead)
        ? "<p class=\"news-card__lead\" itemprop=\"description\">{$lead}</p>"
        : '';

    // meta: data + liczba zdjęć (jeśli jest galeria)
    $metaBits = [];
    if ($opts['showDate'] && $publishedIso && $publishedHuman) {
        $metaBits[] = "<time class=\"news-card__time\" datetime=\"" . nc_e($publishedIso) . "\" itemprop=\"datePublished\">{$publishedHuman}</time><meta itemprop=\"dateModified\" content=\"" . nc_e($publishedIso) . "\">";
    }
    if ($galleryCount > 0) {
        // prosta ikona aparatu (inline SVG) + liczba
        $metaBits[] = '<span class="news-card__gallery" title="Galeria zdjęć">'
            . '<svg class="news-card__gallery-ico" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9.5 5a1 1 0 0 0-.8.4L7.4 7H5a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-7a3 3 0 0 0-3-3h-2.4l-1.3-1.6a1 1 0 0 0-.8-.4H9.5Zm2.5 5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 .002 6.002A3 3 0 0 0 12 12Z"/></svg>'
            . '<span class="news-card__gallery-count">' . (int) $galleryCount . '</span>'
            . '</span>';
    }
    $metaHtml = $metaBits ? '<div class="news-card__meta">' . implode('<span class="news-card__meta-sep">•</span>', $metaBits) . '</div>' : '';

    // obrazek
    $imgAttrs = 'class="news-card__img" loading="lazy" decoding="async" alt="' . $imgAlt . '" itemprop="image"';
    $imgAttrs .= $src ? ' src="' . nc_e($src) . '"' : '';
    $imgAttrs .= $srcset ? ' srcset="' . nc_e($srcset) . '"' : '';
    $imgAttrs .= ' sizes="' . nc_e($sizes) . '"';

    $imageHtml = ($src || $srcset)
        ? "<picture>\n          <img {$imgAttrs}>\n        </picture>"
        : '';

    $rootClass = 'news-card news-card--' . nc_e($opts['variant']);
    $ratioStyle = 'style="--ratio: ' . nc_e($ratio) . '"';

    // szablon
    $html = <<<HTML
<article class="{$rootClass}" itemscope itemtype="https://schema.org/NewsArticle" aria-label="{$title}">
  <a class="news-card__media" href="{$url}" itemprop="url" tabindex="-1" aria-hidden="true">
    <figure class="news-card__figure" {$ratioStyle}>
      {$imageHtml}
    </figure>
  </a>

  <div class="news-card__body">
    {$regionsHtml}
    {$subtitleHtml}

    <{$Heading} class="news-card__title" itemprop="headline">
      <a href="{$url}" class="news-card__title-link">{$title}</a>
    </{$Heading}>

    {$leadHtml}
    {$metaHtml}
  </div>
</article>
HTML;

    return !empty($opts['return']) ? $html : print $html;
}

/**
 * Pasek 3 promowanych (flex). Może być wywoływany wielokrotnie, a między paskami
 * możesz wstawiać reklamy/dodatkowe treści.
 *
 * @param array $items (weźmie max 3)
 * @param array $opts  ['headingTag' => 'h2', 'sizes' => '...']
 */
function renderFeaturedStrip(array $items, array $opts = []): void
{
    $items = array_slice($items, 0, 3);
    if (!$items)
        return;

    $sizes = $opts['sizes'] ?? '(min-width: 1200px) 420px, (min-width: 1024px) 33vw, (min-width: 768px) 45vw, 100vw';
    $headingTag = $opts['headingTag'] ?? 'h2';

    echo '<div class="featured-strip" aria-label="Wiadomości promowane">';
    foreach ($items as $n) {
        renderNewsCard($n, [
            'variant' => 'featured',
            'headingTag' => $headingTag,
            'sizes' => $sizes,
        ]);
    }
    echo '</div>';
}
