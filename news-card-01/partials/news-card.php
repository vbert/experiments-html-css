<?php
/**
 * NEWS CARDS – komponenty frontendu (PHP 8.2)
 * --------------------------------------------
 *  renderNewsCard($data, $opts = [])
 *  renderFeaturedStrip($items, $opts = [])
 *
 * Dane $data:
 *   - url, title, subtitle?, lead?, regions? [ [name,url], ... ]
 *   - image: ['src', 'srcset' => ['480w' => '...', ...], 'ratio' => '16/9']
 *   - publishedAt (ISO-8601)
 *   - galleryCount? int
 *
 * Opcje $opts:
 *   - variant: 'lead'|'featured'|'list' (default 'list')
 *   - showDate: bool (default true)
 *   - headingTag: string (default 'h2')
 *   - sizes: string (nadpisuje automatyczne)
 *   - return: bool (true = zwraca HTML zamiast echo)
 */

if (!function_exists('nc_e')) {
    function nc_e(?string $v): string {
        return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

function renderNewsCard(array $data, array $opts = []) {
    $opts = array_merge([
        'variant'    => 'list',
        'showDate'   => true,
        'headingTag' => 'h2',
        'sizes'      => null,
        'return'     => false,
    ], $opts);

    $title    = nc_e($data['title'] ?? '');
    $subtitle = isset($data['subtitle']) ? nc_e($data['subtitle']) : null;
    $lead     = isset($data['lead']) ? nc_e($data['lead']) : null;
    $url      = nc_e($data['url'] ?? '#');
    $imgAlt   = nc_e($data['imgAlt'] ?? $title);
    $regions  = $data['regions'] ?? [];
    $galleryCount = isset($data['galleryCount']) ? (int)$data['galleryCount'] : 0;

    // --- obraz ---
    $image = $data['image'] ?? [];
    $ratio = $image['ratio'] ?? '16/9';
    $src   = $image['src']   ?? '';
    $srcset = '';
    if (!empty($image['srcset']) && is_array($image['srcset'])) {
        $pairs = [];
        foreach ($image['srcset'] as $w => $path) {
            $pairs[] = nc_e($path) . ' ' . nc_e($w);
        }
        $srcset = implode(', ', $pairs);
    }

    // --- domyślne sizes wg wariantu ---
    if (empty($opts['sizes'])) {
        $opts['sizes'] = match ($opts['variant']) {
            'lead'     => '(min-width: 1400px) 933px, (min-width: 1024px) 66vw, 100vw',
            'featured' => '(min-width: 1400px) 456px, (min-width: 1024px) 33vw, (min-width: 768px) 45vw, 100vw',
            default    => '(min-width: 1024px) 360px, 100vw',
        };
    }

    $sizes = nc_e($opts['sizes']);

    // --- data publikacji ---
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

    // --- regiony ---
    $regionsHtml = '';
    if (!empty($regions)) {
        $links = [];
        foreach ($regions as $r) {
            $rName = nc_e($r['name'] ?? '');
            if ($rName === '') continue;
            $rUrl  = nc_e($r['url']  ?? '#');
            $links[] = "<a href=\"{$rUrl}\" class=\"news-card__region\" itemprop=\"articleSection\">{$rName}</a>";
        }
        if ($links) {
            $regionsHtml = '<nav class="news-card__regions" aria-label="Regiony">' .
                           implode('<span class="news-card__region-sep">◆</span>', $links) .
                           '</nav>';
        }
    }

    // --- meta (data + galeria) ---
    $metaBits = [];
    if ($opts['showDate'] && $publishedIso && $publishedHuman) {
        $metaBits[] = "<time class=\"news-card__time\" datetime=\"" . nc_e($publishedIso) . "\" itemprop=\"datePublished\">{$publishedHuman}</time>";
    }
    if ($galleryCount > 0) {
        $metaBits[] =
            '<span class="news-card__gallery" title="Galeria zdjęć">' .
            '<svg class="news-card__gallery-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 5a1 1 0 0 0-.8.4L7.4 7H5a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-7a3 3 0 0 0-3-3h-2.4l-1.3-1.6a1 1 0 0 0-.8-.4H9.5Zm2.5 5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 .002 6.002A3 3 0 0 0 12 12Z"/></svg>' .
            '<span class="news-card__gallery-count">' . $galleryCount . '</span>' .
            '</span>';
    }
    $metaHtml = $metaBits ? '<div class="news-card__meta">' . implode('<span class="news-card__meta-sep">•</span>', $metaBits) . '</div>' : '';

    // --- obrazek HTML ---
    $imgAttrs = [];
    if ($src)    $imgAttrs[] = 'src="'.nc_e($src).'"';
    if ($srcset) $imgAttrs[] = 'srcset="'.nc_e($srcset).'"';
    $imgAttrs[] = 'sizes="'.$sizes.'"';
    $imgAttrs[] = 'class="news-card__img" loading="lazy" decoding="async" alt="'.$imgAlt.'" itemprop="image"';
    $imageHtml = ($src || $srcset)
        ? "<picture><img ".implode(' ', $imgAttrs)."></picture>"
        : '';

    // --- heading ---
    $Heading = in_array(strtolower($opts['headingTag']), ['h1','h2','h3','h4','h5','h6'], true)
        ? strtolower($opts['headingTag'])
        : 'h2';

    $subtitleHtml = $subtitle ? "<p class=\"news-card__subtitle\" itemprop=\"alternativeHeadline\">{$subtitle}</p>" : '';
    $leadHtml     = ($opts['variant'] === 'lead' && $lead)
        ? "<p class=\"news-card__lead\" itemprop=\"description\">{$lead}</p>"
        : '';

    // --- składanie HTML ---
    $rootClass  = 'news-card news-card--' . nc_e($opts['variant']);
    $ratioStyle = 'style="--ratio: ' . nc_e($ratio) . '"';

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
 * Pasek 3 promowanych (flex)
 */
function renderFeaturedStrip(array $items, array $opts = []): void {
    $items = array_slice($items, 0, 3);
    if (!$items) return;

    $headingTag = $opts['headingTag'] ?? 'h2';

    echo '<div class="featured-strip" aria-label="Wiadomości promowane">';
    foreach ($items as $n) {
        renderNewsCard($n, [
            'variant'    => 'featured',
            'headingTag' => $headingTag,
        ]);
    }
    echo '</div>';
}
