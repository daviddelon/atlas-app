<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Description extends Model
{

    public function taxon () {
        return $this->belongsTo(Taxon::class);
    }

    public function getFormattedContent() {
        $content = $this->content;
        $parts = explode("\n", $content);
        $desc = $parts[0];
        $rest = $parts[1] ?? '';

        // Remove title from desc (assuming it starts with ** and ends with . – )
        $desc = preg_replace('/^\*\*.*?\*\* .*?\. – /', '', $desc);

        // Remove // separators
        $desc = str_replace('//', '', $desc);

        // Split into bullets on ;
        $bullets = explode(';', $desc);
        $bullets = array_map('trim', $bullets);
        $bullets = array_filter($bullets);

        // Parse rest
        $sections = explode(' – ', $rest);
        $ecologie = trim($sections[0] ?? '');
        $remaining = $sections[1] ?? '';
        if (strpos($remaining, ' = ') !== false) {
            list($repartition, $floraison) = explode(' = ', $remaining, 2);
            $repartition = trim($repartition);
            $floraison = trim($floraison);
        } else {
            $repartition = trim($remaining);
            $floraison = '';
        }

        // Build HTML
        $html = '<ul style="margin-left: 0; padding-left: 0; list-style-position: inside;">';
        foreach ($bullets as $bullet) {
            $bullet = preg_replace('/[ \t\n\r\0\x0B.;]+$/', '', $bullet);
            $bullet = rtrim($bullet);
            $bullet = ucfirst($bullet);
            $bullet .= '.';
            $html .= "<li>$bullet</li>";

        }
        $html .= '</ul>';
        $html .= "<br><strong>Écologie</strong> $ecologie<br>";
        if ($repartition) $html .= "<strong>Répartition</strong> $repartition<br>";
        if ($floraison) $html .= "<strong>Floraison</strong> $floraison<br>";
        return $html;
    }

}
