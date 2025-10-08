<?php

namespace MetaFramework\Inputable\Printers;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use MetaFramework\Inputable\Support\TreeBuilder;

class Checkboxes
{
    use TreeBuilder;

    private Collection $affected;
    private string $name;

    public function __construct(EloquentCollection $collection, string $name, ?Collection $affected)
    {
        $this->collection = $collection;
        $this->affected = $affected ?: collect();
        $this->name = $name;
    }

    public function __invoke(): string
    {
        return $this->buildTree()->print();
    }

    public function print(): string
    {
        $html = '';
        if ($this->tree->isNotEmpty()) {
            $html = '<ul class="list-unstyled checkbox-tree">';
            foreach ($this->tree as $item) {
                $this->entry($html, $item);
            }
            $html .= '</ul>';
        }

        return $html;
    }

    private function entry(string &$html, $item, $parent = null): void
    {
        $html .= '<li data-id="' . $item->id . '"' . ($parent ? ' class="child" data-parent="' . $parent . '"' : '') . '>';
        $html .= View::make('mfw-input::components.checkbox', [
            'value' => $item->id,
            'name' => $this->name,
            'affected' => $this->affected,
            'class' => '',
            'label' => $item->translation('title'),
            'forLabel' => str_replace(['[', ']'], '', $this->name) . $item->id,
            'isSelected' => $this->affected->contains($item->id),
            'switch' => false,
        ])->render();
        self::buildLevels($html, $item);
        $html .= '</li>';
    }

    private function buildLevels(string &$html, $collection): string
    {
        if ($collection->subs->isNotEmpty()) {
            $html .= '<ul class="list-unstyled ps-4">';
            foreach ($collection->subs as $items) {
                $this->entry($html, $items, $items->parent);
            }
            $html .= '</ul>';
        }

        return $html;
    }
}
