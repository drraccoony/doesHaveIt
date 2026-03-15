<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OgImageController extends Controller
{
    private const WIDTH  = 1200;
    private const HEIGHT = 630;

    // Steam colour palette
    private const C_BG      = [27,  40,  56];   // #1b2838
    private const C_PANEL   = [22,  32,  45];   // slightly darker panel
    private const C_BLUE    = [102, 192, 244];  // #66c0f4
    private const C_MUTED   = [74,  97,  116];  // #4a6174
    private const C_WHITE   = [198, 212, 223];  // #c6d4df
    private const C_GREEN   = [91,  163, 43];   // #5ba32b
    private const C_RED     = [201, 71,  40];   // #c94728

    /** Common bold TTF font paths (Sail/Ubuntu + macOS fallback). */
    private const FONT_PATHS = [
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
        '/usr/share/fonts/truetype/ubuntu/Ubuntu-B.ttf',
        '/System/Library/Fonts/Supplemental/Arial Bold.ttf',
        '/System/Library/Fonts/Helvetica.ttc',
    ];

    public function generate(Request $request): Response
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:80',
            'owned' => 'nullable|boolean',
        ]);

        $title = isset($data['title']) ? strip_tags(trim($data['title'])) : null;
        $owned = $data['owned'] ?? null;

        $img = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        [$cBg, $cPanel, $cBlue, $cMuted, $cWhite, $cGreen, $cRed] = array_map(
            fn ($c) => imagecolorallocate($img, ...$c),
            [self::C_BG, self::C_PANEL, self::C_BLUE, self::C_MUTED, self::C_WHITE, self::C_GREEN, self::C_RED]
        );

        // Background
        imagefill($img, 0, 0, $cBg);

        // Subtle inner panel
        imagefilledrectangle($img, 60, 60, self::WIDTH - 60, self::HEIGHT - 60, $cPanel);

        // Top accent line
        imagefilledrectangle($img, 60, 60, self::WIDTH - 60, 66, $cBlue);

        $font = $this->findFont();

        if ($font !== null) {
            $this->renderTTF($img, $font, $title, $owned, $cBlue, $cMuted, $cWhite, $cGreen, $cRed);
        } else {
            $this->renderFallback($img, $title, $owned, $cBlue, $cWhite, $cGreen, $cRed);
        }

        ob_start();
        imagepng($img);
        imagedestroy($img);
        /** @var string $png */
        $png = ob_get_clean();

        return response($png)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    private function renderTTF(
        \GdImage $img,
        string $font,
        ?string $title,
        ?bool $owned,
        int $cBlue,
        int $cMuted,
        int $cWhite,
        int $cGreen,
        int $cRed,
    ): void {
        $W = self::WIDTH;
        $H = self::HEIGHT;

        // Site name — top centre
        $this->ttfCentreX($img, 28, $font, 'DoesValterHaveIt?', $cBlue, 130);

        if ($title !== null && $owned !== null) {
            // Large YES / NO
            $resultText  = $owned ? 'YES' : 'NO';
            $resultColor = $owned ? $cGreen : $cRed;
            $this->ttfCentreX($img, 160, $font, $resultText, $resultColor, 400);

            // Game title — wrapped if needed
            $wrapped = $this->wrapText($title, 42, $font, $W - 180);
            $lines   = explode("\n", $wrapped);
            $lineH   = 58;
            $startY  = 470 + ($lineH * (count($lines) - 1) * -0.5);
            foreach ($lines as $i => $line) {
                $this->ttfCentreX($img, 42, $font, $line, $cWhite, (int) ($startY + $i * $lineH));
            }
        } else {
            // Default brand image
            $this->ttfCentreX($img, 44, $font, 'Check whether Valter owns a Steam game.', $cMuted, 340);
            $this->ttfCentreX($img, 30, $font, 'Type a game name — get a YES or NO.', $cMuted, 410);
        }
    }

    /** Centre text horizontally and draw at $y (baseline). */
    private function ttfCentreX(\GdImage $img, float $size, string $font, string $text, int $color, int $y): void
    {
        $bbox = imagettfbbox($size, 0, $font, $text);
        $w    = abs($bbox[2] - $bbox[0]);
        $x    = (int) ((self::WIDTH - $w) / 2);
        imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
    }

    /** Word-wrap $text so each line fits within $maxWidth pixels at $size pt. */
    private function wrapText(string $text, float $size, string $font, int $maxWidth): string
    {
        $words  = explode(' ', $text);
        $lines  = [];
        $line   = '';

        foreach ($words as $word) {
            $test = $line === '' ? $word : "$line $word";
            $bbox = imagettfbbox($size, 0, $font, $test);
            if (abs($bbox[2] - $bbox[0]) > $maxWidth && $line !== '') {
                $lines[] = $line;
                $line    = $word;
            } else {
                $line = $test;
            }
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    /** Bitmap-font fallback (no TTF on system). */
    private function renderFallback(
        \GdImage $img,
        ?string $title,
        ?bool $owned,
        int $cBlue,
        int $cWhite,
        int $cGreen,
        int $cRed,
    ): void {
        $label = $owned === true ? 'YES' : ($owned === false ? 'NO' : 'DoesValterHaveIt?');
        $color = $owned === true ? $cGreen : ($owned === false ? $cRed : $cBlue);
        imagestring($img, 5, 60, 80, 'DoesValterHaveIt?', $cBlue);
        imagestring($img, 5, 60, 200, $label, $color);
        if ($title !== null) {
            imagestring($img, 3, 60, 260, $title, $cWhite);
        }
    }

    private function findFont(): ?string
    {
        foreach (self::FONT_PATHS as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
