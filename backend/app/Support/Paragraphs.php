<?php

namespace App\Support;

/**
 * Long stories written as one block read as paragraphs: the text is split at sentence ends
 * (never inside a word or a bold/link) into paragraphs of a few sentences. No word is changed or removed.
 */
class Paragraphs
{
    /** Paragraphs shorter than this are left as they are. */
    private const SPLIT_FROM = 420;

    /** A paragraph closes before a sentence that would take it past MAX characters, once it holds MIN_PARA. */
    private const MAX = 340;

    private const MIN_PARA = 120;

    /** A last paragraph shorter than this joins the one before it. */
    private const MIN_LAST = 90;

    /**
     * Plain text (the achiever's story): paragraphs separated by a blank line. Line breaks the editor wrote are kept.
     */
    public static function text(string $text): string
    {
        $blocks = preg_split('/\R\s*\R/u', trim($text)) ?: [];

        return collect($blocks)
            ->map(fn (string $block) => trim($block))
            ->filter()
            ->flatMap(fn (string $block) => str_contains($block, "\n") || mb_strlen($block) < self::SPLIT_FROM ? [$block] : self::group(self::sentences($block)))
            ->implode("\n\n");
    }

    /**
     * One block of the achievement body (`<p>…</p>`, a list, a heading…): a long `<p>` comes back as several `<p>`.
     *
     * @return list<string>
     */
    public static function html(string $block): array
    {
        if (! preg_match('#^\s*<p(\s[^>]*)?>(.*)</p>\s*$#su', $block, $m)) {
            return [$block];
        }
        [$attributes, $inner] = [$m[1] ?? '', $m[2]];
        if (mb_strlen(trim(html_entity_decode(strip_tags($inner)))) < self::SPLIT_FROM) {
            return [$block];
        }

        // Sentence ends outside any tag: the text between tags, at tag depth 0, is where a paragraph may end.
        $parts = preg_split('/(<[^>]+>)/u', $inner, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        $sentences = [''];
        $depth = 0;
        foreach ($parts as $part) {
            if (str_starts_with($part, '<')) {
                $sentences[array_key_last($sentences)] .= $part;
                if (preg_match('#^</#', $part)) {
                    $depth = max(0, $depth - 1);
                } elseif (! preg_match('#/>$|^<(br|img|hr|wbr)\b#i', $part)) {
                    $depth++;
                }

                continue;
            }
            if ($depth > 0) {
                $sentences[array_key_last($sentences)] .= $part;

                continue;
            }
            foreach (self::cut($part) as $i => $piece) {
                if ($i > 0) {
                    $sentences[] = '';
                }
                $sentences[array_key_last($sentences)] .= $piece;
            }
        }

        return array_map(fn (string $paragraph) => '<p'.$attributes.'>'.$paragraph.'</p>', self::group($sentences, true));
    }

    /**
     * @return list<string>
     */
    private static function sentences(string $text): array
    {
        return self::cut($text);
    }

    /**
     * Cut a run of text after each sentence end (". " "؟ " "! "), but not after a one-letter abbreviation such as "د.".
     *
     * @return list<string>
     */
    private static function cut(string $text): array
    {
        $pieces = preg_split('/(?<=[.!?؟])\s+/u', $text) ?: [$text];
        $out = [];
        foreach ($pieces as $piece) {
            $last = $out ? $out[array_key_last($out)] : null;
            if ($last !== null && preg_match('/(^|\s)\p{L}\.$/u', $last)) {
                $out[array_key_last($out)] = $last.' '.$piece;
            } else {
                $out[] = $piece;
            }
        }
        // Keep the space that followed each sentence end, so the joined text reads exactly as before.
        $count = count($out);

        return array_map(fn (string $piece, int $i) => $i < $count - 1 ? $piece.' ' : $piece, $out, array_keys($out));
    }

    /**
     * @param  list<string>  $sentences
     * @return list<string>
     */
    private static function group(array $sentences, bool $html = false): array
    {
        $length = fn (string $s) => mb_strlen($html ? html_entity_decode(strip_tags($s)) : $s);
        $paragraphs = [];
        $current = '';
        foreach ($sentences as $sentence) {
            if ($length($current) >= self::MIN_PARA && $length($current) + $length($sentence) > self::MAX) {
                $paragraphs[] = trim($current);
                $current = '';
            }
            $current .= $sentence;
        }
        if (trim($current) !== '') {
            if ($paragraphs && $length($current) < self::MIN_LAST) {
                $paragraphs[array_key_last($paragraphs)] .= ' '.trim($current);
            } else {
                $paragraphs[] = trim($current);
            }
        }

        return $paragraphs ?: [trim(implode('', $sentences))];
    }
}
