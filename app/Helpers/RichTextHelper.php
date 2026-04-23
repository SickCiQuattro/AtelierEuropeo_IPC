<?php

namespace App\Helpers;

use DOMDocument;
use DOMElement;
use DOMNode;

class RichTextHelper
{
    private const ALLOWED_TAGS = [
        'p',
        'br',
        'strong',
        'em',
        'a',
        'ul',
        'li',
        'ol',
    ];

    public static function sanitize(?string $value): string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return '';
        }

        $containsHtml = $raw !== strip_tags($raw);
        if (!$containsHtml) {
            return nl2br(self::escapeTextPreservingQuotes($raw), false);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $wrappedHtml = '<div>' . $raw . '</div>';

        $previousErrors = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">' . $wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        $root = $document->getElementsByTagName('div')->item(0);
        if (!$root instanceof DOMElement) {
            return nl2br(self::escapeTextPreservingQuotes(strip_tags($raw)), false);
        }

        self::sanitizeNode($root, $document);

        $output = '';
        foreach (iterator_to_array($root->childNodes) as $node) {
            $output .= $document->saveHTML($node);
        }

        return trim($output);
    }

    private static function escapeTextPreservingQuotes(string $text): string
    {
        return htmlspecialchars($text, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
    }

    private static function sanitizeNode(DOMNode $parent, DOMDocument $document): void
    {
        for ($child = $parent->firstChild; $child !== null; $child = $nextSibling) {
            $nextSibling = $child->nextSibling;

            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if ($tag === 'b') {
                    $child = self::replaceElementTag($child, 'strong', $document);
                    $tag = 'strong';
                } elseif ($tag === 'i') {
                    $child = self::replaceElementTag($child, 'em', $document);
                    $tag = 'em';
                } elseif ($tag === 'div') {
                    $child = self::replaceElementTag($child, 'p', $document);
                    $tag = 'p';
                }

                if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                    self::unwrapElement($child);
                    continue;
                }

                if ($tag === 'a') {
                    $safeHref = self::normalizeHref($child->getAttribute('href'));
                    self::removeAllAttributes($child);

                    if ($safeHref === null) {
                        self::unwrapElement($child);
                        continue;
                    }

                    $child->setAttribute('href', $safeHref);
                    if (str_starts_with(strtolower($safeHref), 'http')) {
                        $child->setAttribute('target', '_blank');
                        $child->setAttribute('rel', 'noopener noreferrer');
                    }
                } else {
                    self::removeAllAttributes($child);
                }

                self::sanitizeNode($child, $document);

                if (
                    in_array($tag, ['p', 'li'], true)
                    && trim((string) $child->textContent) === ''
                    && !$child->getElementsByTagName('br')->length
                ) {
                    $child->parentNode?->removeChild($child);
                }

                continue;
            }

            if ($child->nodeType === XML_COMMENT_NODE) {
                $parent->removeChild($child);
            }
        }
    }

    private static function normalizeHref(?string $href): ?string
    {
        $candidate = trim((string) $href);
        if ($candidate === '') {
            return null;
        }

        $candidate = html_entity_decode($candidate, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (str_starts_with($candidate, '#') || str_starts_with($candidate, '/')) {
            return $candidate;
        }

        if (preg_match('/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/', $candidate) === 1) {
            return 'mailto:' . $candidate;
        }

        if (preg_match('/^www\\./i', $candidate) === 1) {
            $candidate = 'https://' . $candidate;
        }

        if (str_starts_with(strtolower($candidate), 'mailto:')) {
            $email = substr($candidate, 7);
            return filter_var($email, FILTER_VALIDATE_EMAIL) ? $candidate : null;
        }

        if (!filter_var($candidate, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($candidate, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $candidate;
    }

    private static function removeAllAttributes(DOMElement $element): void
    {
        while ($element->attributes->length > 0) {
            $attribute = $element->attributes->item(0);
            if ($attribute) {
                $element->removeAttributeNode($attribute);
            }
        }
    }

    private static function replaceElementTag(DOMElement $element, string $newTag, DOMDocument $document): DOMElement
    {
        $replacement = $document->createElement($newTag);

        while ($element->firstChild) {
            $replacement->appendChild($element->firstChild);
        }

        $parent = $element->parentNode;
        if ($parent) {
            $parent->replaceChild($replacement, $element);
        }

        return $replacement;
    }

    private static function unwrapElement(DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (!$parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }
}
