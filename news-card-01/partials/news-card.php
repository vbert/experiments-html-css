<?php
/**
 * NEWS CARDS – komponenty frontend-u (PHP 8.2)
 * --------------------------------------------
 *  renderNewsCard($data, $options = [])
 *  renderFeaturedStrip($items, $options = [])
 *
 * Dane $data:
 *   - url, title, subtitle?, lead?, regions? [ [name,url], ... ]
 *   - image: ['src', 'srcset' => ['480w' => '...', ...], 'ratio' => '16/9']
 *   - publishedAt (ISO-8601)
 *   - galleryCount? int
 *
 * Opcje $options:
 *   - variant: 'lead'|'featured'|'list' (default 'list')
 *   - showDate: bool (default true)
 *   - headingTag: string (default 'h2')
 *   - sizes: string (nadpisuje automatyczne)
 *   - return: bool (true = zwraca HTML zamiast echo)
 */

if (!function_exists('helperEscHtml')) {
    function helperEscHtml(?string $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
    }
}

if (!function_exists('helperFormatPublishedDate')) {
    function helperFormatPublishedDate(?string $publishedIso): ?string {
        if (!$publishedIso) {
            return null;
        }

        $dateSearch = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $dateReplace = ['poniedziałek', 'wtorek', 'środa', 'czwartek', 'piątek', 'sobota', 'niedziela', 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia'];

        try {
            $dt = new DateTime($publishedIso);
            return str_replace($dateSearch, $dateReplace, $dt->format('D, j M Y, H:i'));
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('helperHtmlEl')) {
    /**
     * Uniwersalny generator elementu HTML.
     * - wspiera class jako string|array, style jako string|array oraz podtablice data/aria
     * - wartości boolean: true => sam atrybut (np. itemscope), false/null => pomija
     * - content: string lub array (łączone bez separatora)
     */
    function helperHtmlEl(string $tag, array $attrs = [], string|array|null $content = null): string {
        $tag = strtolower($tag);

        if (!preg_match('/^[a-z][a-z0-9:-]*$/i', $tag)) {
            throw new InvalidArgumentException("Invalid tag name: {$tag}");
        }

        // class
        if (array_key_exists('class', $attrs)) {

            if (is_array($attrs['class'])) {
                $classes = array_filter(array_map('strval', $attrs['class']), fn($c) => $c !== '' && $c !== null && $c !== false);
                $attrs['class'] = implode(' ', array_unique($classes));

                if ($attrs['class'] === '') unset($attrs['class']);
            } elseif ($attrs['class'] === '' || $attrs['class'] === null) {
                unset($attrs['class']);
            }
        }

        // style
        if (isset($attrs['style']) && is_array($attrs['style'])) {
            $styleParts = [];
            foreach ($attrs['style'] as $k => $v) {
                if ($v === null || $v === false || $v === '') continue;
                $styleParts[] = "{$k}: {$v}";
            }

            if ($styleParts) $attrs['style'] = implode('; ', $styleParts);
            else unset($attrs['style']);
        }

        // data-*, aria-*
        foreach (['data' => 'data-', 'aria' => 'aria-'] as $sub => $prefix) {

            if (isset($attrs[$sub]) && is_array($attrs[$sub])) {
                foreach ($attrs[$sub] as $k => $v) {
                    if ($v === null || $v === false) continue;
                    $attrs["{$prefix}{$k}"] = $v;
                }
                unset($attrs[$sub]);
            }
        }

        // atrybuty → string
        $pairs = [];
        foreach ($attrs as $k => $v) {
            if ($v === null || $v === false) continue;

            $pairs[] = ($v === true)
                ? helperEscHtml((string)$k)
                : helperEscHtml((string)$k) . '="' . helperEscHtml((string)$v) . '"';
        }
        $attrStr = $pairs ? (' ' . implode(' ', $pairs)) : '';

        // void elements
        $voids = ['area','base','br','col','embed','hr','img','input','link','meta','param','source','track','wbr'];
        $isVoid = in_array($tag, $voids, true);

        if ($isVoid) {
            return "<{$tag}{$attrStr}>";
        }

        if (is_array($content)) {
            $content = implode('', array_map(static fn($c) => (string)$c, $content));
        }
        $content ??= $content;

        return "<{$tag}{$attrStr}>{$content}</{$tag}>";
    }
}

if (!function_exists('renderNewsCard')) {
    function renderNewsCard(array $data, array $options = []) {
        $options = array_merge([
            'variant'    => 'list',
            'showDate'   => true,
            'headingTag' => 'h2',
            'sizes'      => null,
            'return'     => false,
        ], $options);

        $title    = helperEscHtml($data['title'] ?? '');
        $subtitle = isset($data['subtitle']) ? helperEscHtml($data['subtitle']) : null;
        $lead     = isset($data['lead']) ? helperEscHtml($data['lead']) : null;
        $url      = helperEscHtml($data['url'] ?? '#');
        $imgAlt   = helperEscHtml($data['imgAlt'] ?? $title);
        $regions  = $data['regions'] ?? [];
        $galleryCount = isset($data['galleryCount']) ? (int)$data['galleryCount'] : 0;

        // --- obraz ---
        $image = $data['image']  ?? [];
        $ratio = $image['ratio'] ?? '16/9';
        $src   = $image['src']   ?? '';
        $srcset = '';

        if (!empty($image['srcset']) && is_array($image['srcset'])) {
            $pairs = [];
            foreach ($image['srcset'] as $w => $path) {
                $pairs[] = helperEscHtml($path) . ' ' . helperEscHtml($w);
            }
            $srcset = implode(', ', $pairs);
        }

        // --- domyślne sizes wg wariantu ---
        if (empty($options['sizes'])) {
            $options['sizes'] = match ($options['variant']) {
                'lead'     => '(min-width: 1400px) 933px, (min-width: 1024px) 66vw, 100vw',
                'featured' => '(min-width: 1400px) 456px, (min-width: 1024px) 33vw, (min-width: 768px) 45vw, 100vw',
                default    => '(min-width: 1024px) 360px, 100vw',
            };
        }

        $sizes = helperEscHtml($options['sizes']);

        // --- data publikacji ---
        $publishedIso = $data['publishedAt'] ?? null;
        $publishedHuman = helperFormatPublishedDate($publishedIso);

        // --- regiony ---
        $regionsHtml = '';
        if (!empty($regions)) {
            $links = [];
            foreach ($regions as $r) {
                $rName = helperEscHtml($r['name'] ?? '');
                if ($rName === '') continue;
                $rUrl  = helperEscHtml($r['url']  ?? '#');
                $links[] = helperHtmlEl('a', [
                    'href' => $rUrl,
                    'class' => 'news-card__region',
                    'itemprop' => 'articleSection',
                ], $rName);
            }
            if ($links) {
                $sep = helperHtmlEl('span', ['class' => 'news-card__region-sep'], '•');
                $regionsLinks = implode($sep, $links);
                $regionsHtml = helperHtmlEl('nav', [
                    'class' => 'news-card__regions',
                    'aria' => ['label' => 'Regiony'],
                ], $regionsLinks);
            }
        }

        // --- meta (data + galeria) ---
        $metaBits = [];

        if ($options['showDate'] && $publishedIso && $publishedHuman) {
            $publishedNCE = helperEscHtml($publishedHuman);
            $metaBits[] = helperHtmlEl('time', [
                'class' => 'news-card__time',
                'datetime' => $publishedNCE,
                'itemprop' => 'datePublished',
            ], $publishedHuman);
        }

        if ($galleryCount > 0) {
            $svg = '<svg class="news-card__gallery-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 5a1 1 0 0 0-.8.4L7.4 7H5a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-7a3 3 0 0 0-3-3h-2.4l-1.3-1.6a1 1 0 0 0-.8-.4H9.5Zm2.5 5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 .002 6.002A3 3 0 0 0 12 12Z"/></svg>';
            $metaBits[] = helperHtmlEl('span', [
                'class' => 'news-card__gallery',
                'title' => 'Galeria zdjęć',
            ], $svg . helperHtmlEl('span', ['class' => 'news-card__gallery-count'], (string)$galleryCount));
        }
        $metaHtml = $metaBits
            ? helperHtmlEl('div', ['class' => 'news-card__meta'], implode(helperHtmlEl('span', ['class' => 'news-card__meta-sep'], '•'), $metaBits))
            : '';

        // --- obrazek HTML ---
        $imgHtml = '';

        if ($src || $srcset) {
            $imgHtml = helperHtmlEl('img', [
                'src' => $src ?: null,
                'srcset' => $srcset ?: null,
                'sizes' => $sizes,
                'class' => 'news-card__img',
                'loading' => 'lazy',
                'decoding' => 'async',
                'alt' => $imgAlt,
                'itemprop' => 'image',
            ]);
        }
        $imageHtml = ($src || $srcset)
            ? helperHtmlEl('picture', [], $imgHtml)
            : '';

        // --- heading ---
        $Heading = in_array(strtolower($options['headingTag']), ['h1','h2','h3','h4','h5','h6'], true)
            ? strtolower($options['headingTag'])
            : 'h2';

        $subtitleHtml = $subtitle
            ? helperHtmlEl('h4', ['class' => 'news-card__subtitle', 'itemprop' => 'alternativeHeadline'], $subtitle)
            : '';
        $leadHtml = ($options['variant'] === 'lead' && $lead)
            ? helperHtmlEl('p', ['class' => 'news-card__lead', 'itemprop' => 'description'], $lead)
            : '';

        // --- składanie HTML ---
        $rootClass  = 'news-card news-card--' . helperEscHtml($options['variant']);
        $ratioCss   = '--ratio: ' . helperEscHtml($ratio);

        $media = helperHtmlEl('a', [
            'class' => 'news-card__media',
            'href' => $url,
            'itemprop' => 'url',
            'tabindex' => '-1',
            'aria' => ['hidden' => 'true'],
        ], helperHtmlEl('figure', [
            'class' => 'news-card__figure',
            'style' => $ratioCss,
        ], $imageHtml));

        $titleHtml = helperHtmlEl($Heading, [
            'class' => 'news-card__title',
            'itemprop' => 'headline',
        ], helperHtmlEl('a', [
            'href' => $url,
            'class' => 'news-card__title-link',
        ], $title));

        $body = helperHtmlEl('div', ['class' => 'news-card__body'], [
            $regionsHtml,
            $subtitleHtml,
            $titleHtml,
            $leadHtml,
            $metaHtml,
        ]);

        $html = helperHtmlEl('article', [
            'class' => $rootClass,
            'itemscope' => true,
            'itemtype' => 'https://schema.org/NewsArticle',
            'aria' => ['label' => $title],
        ], "{$media}{$body}");

        return !empty($options['return']) ? $html : print $html;
    }
}

/**
 * Pasek 3 promowanych (flex)
 */
if (!function_exists('renderFeaturedStrip')) {
    function renderFeaturedStrip(array $items, array $options = []): void {
        $items = array_slice($items, 0, 3);
        if (!$items) return;

        $headingTag = $options['headingTag'] ?? 'h2';

        echo '<div class="featured-strip" aria-label="Wiadomości promowane">';
        foreach ($items as $n) {
            renderNewsCard($n, [
                'variant'    => 'featured',
                'headingTag' => $headingTag,
            ]);
        }
        echo '</div>';
    }
}
