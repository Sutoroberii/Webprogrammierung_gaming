<?php

class MarkdownParser
{
    public function parse(string $markdown): string
    {
        $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);

        $blocks = preg_split('/\n\s*\n/', trim($markdown));

        $html = '';

        foreach ($blocks as $block) {
            $html .= $this->renderBlock($block);
        }

        return $html;
    }

    public function parseFile(string $path): string
    {
        if (!file_exists($path)) {
            return '';
        }

        return $this->parse(
            file_get_contents($path)
        );
    }

    public function saveMarkdownFile(
        string $path,
        string $markdown
    ): bool {
        return file_put_contents(
            $path,
            $markdown
        ) !== false;
    }

    private function renderBlock(string $block): string
    {
        $block = trim($block);

        // Headings
        if (preg_match('/^(#{1,6})\s+(.*)$/s', $block, $m)) {
            $level = strlen($m[1]);

            return sprintf(
                "<h%d>%s</h%d>\n",
                $level,
                $this->renderInline($m[2]),
                $level
            );
        }

        // Horizontal rule
        if (preg_match('/^[-*_]{3,}$/', $block)) {
            return "<hr>\n";
        }

        // Blockquote
        if (str_starts_with($block, '>')) {
            $content = preg_replace('/^>\s?/m', '', $block);

            return sprintf(
                "<blockquote>%s</blockquote>\n",
                nl2br($this->renderInline($content), false)
            );
        }

        return sprintf(
            "<p>%s</p>\n",
            nl2br(
                $this->renderInline($block),
                false
            )
        );
    }

    private function renderInline(string $text): string
    {
        $text = htmlspecialchars(
            $text,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        // Code
        $text = preg_replace(
            '/`([^`]+)`/',
            '<code>$1</code>',
            $text
        );

        // Images
        $text = preg_replace(
            '/!\[([^\]]*)\]\(([^)]+)\)/',
            '<img src="$2" alt="$1">',
            $text
        );

        // Links
        $text = preg_replace(
            '/\[([^\]]+)\]\(([^)]+)\)/',
            '<a href="$2">$1</a>',
            $text
        );

        // Bold
        $text = preg_replace(
            '/\*\*(.+?)\*\*/s',
            '<strong>$1</strong>',
            $text
        );

        // Italic
        $text = preg_replace(
            '/\*(.+?)\*/s',
            '<em>$1</em>',
            $text
        );

        // Strikethrough
        $text = preg_replace(
            '/~~(.+?)~~/s',
            '<del>$1</del>',
            $text
        );

        return $text;
    }
}