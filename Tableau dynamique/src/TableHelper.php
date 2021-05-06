<?php

namespace App;

class TableHelper {


    public static function sort(string $sortKey, string $label, array $data): string
    {
        $sort = $data['sort'] ?? null;
        $direction = $data['dir'] ?? null;
        $icon="";

        if($sort === $sortKey) {
            $icon = $direction === "asc" ? "⏶" : "⏷";
        }

        $url = URLHelper::withParams($data, [
            'sort' => $sortKey, 
            'dir' => $direction ===  'asc'  && $sort === $sortKey ? 'desc' : 'asc'
            
            ]);
        return <<<HTML
        <a href="?$url">$label $icon</a>
        HTML;
    }
}