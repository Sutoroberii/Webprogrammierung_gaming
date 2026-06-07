<?php

class MetadataParser {

    public function parse(string $content): array {
        $content = trim($content);
        if (!str_starts_with($content, '---')) {
            return [];
        }

        $content = substr($content, 3);
        $end = strpos($content, "\n---");
        if ($end === false) {
            $end = strpos($content, "---");
            if ($end === false) {
                return [];
            }
        }

        $json = trim(substr($content, 0, $end));
        $decoded = json_decode($json, true);
        return is_array($decoded)
            ? $decoded
            : [];
    }

    public function parseFile(string $path): array {
        if (!file_exists($path)) {
            return [];
        }

        return $this->parse(file_get_contents($path));
    }
    
    public function removeMetadata(string $content): string {
        $content = trim($content);

        if (!str_starts_with($content, '---')) {
            return $content;
        }

        $end = strpos($content, "\n---", 3);

        if ($end === false) {
            $end = strpos($content, "---", 3);

            if ($end === false) {
                return $content;
            }

            return trim(substr($content, $end + 3));
        }

        return trim(substr($content, $end + 4));
    }

    public function encode(array $data, bool $pretty = true): string {
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT;
        }

        return "---\n"
            . json_encode($data, $flags)
            . "\n---";
    }
}