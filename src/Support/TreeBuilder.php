<?php

namespace MetaFramework\Inputable\Support;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

trait TreeBuilder
{
    private object $tree;
    private EloquentCollection $collection;

    public function buildTree(): object
    {
        $this->tree = $this->collection->whereNull('parent')->tap(function ($items) {
            return $items;
        });

        self::subs($this->tree, $this->collection);

        return $this;
    }

    public function print(): string
    {
        $html = '';
        if ($this->tree->isNotEmpty()) {
            $html = '<ul>';
            foreach ($this->tree as $item) {
                $this->entry($html, $item, $parent = null);
            }
            $html .= '</ul>';
        }

        return $html;
    }

    private function entry(string &$html, $item, $parent = null): void
    {
        $html .= '<li>' . $item->translation('title');
        self::buildLevels($html, $item);
        $html .= '</li>';
    }

    private function buildLevels(string &$html, $collection): string
    {
        if ($collection->subs->isNotEmpty()) {
            $html .= '<ul>';
            foreach ($collection->subs as $items) {
                $this->entry($html, $items, $items->parent);
            }
            $html .= '</ul>';
        }
        return $html;
    }

    private static function subs(&$array, $collection)
    {
        foreach ($array as $item) {
            $item->subs = $collection->where('parent', $item->id)->tap(function ($items) {
                return $items;
            });
            self::subs($item->subs, $collection);
        }
        return $array;
    }
}
