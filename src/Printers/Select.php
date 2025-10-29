<?php

namespace MetaFramework\Inputable\Printers;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Lang;
use MetaFramework\Inputable\Support\TreeBuilder;

class Select
{
    use TreeBuilder;

    private ?int $affected;
    private string $name;

    public function __construct(EloquentCollection $collection, string $name, ?int $affected = null)
    {
        $this->collection = $collection;
        $this->affected = $affected;
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
            $placeholder = '-- ' . Lang::get('mfw-inputable::messages.select_option') . ' --';
            $html = '<select class="form-control" name="' . $this->name . '" id="' . rtrim(str_replace(['[',']'],'_', $this->name),'_') . '"><option value="">' . $placeholder . '</option>';
            foreach ($this->tree as $item) {
                $this->entry($html, $item);
            }
            $html .= '</select>';
        }

        return $html;
    }

    private function entry(string &$html, $item, $parent = null, int $level = 0): void
    {
        $html .= '<option value="' . $item->id . '"' . ($this->affected == $item->id ? ' selected' : '') . '>' . str_repeat('- ', $level) . $item->translation('title');
        self::buildLevels($html, $item, ++$level);
        $html .= '</option>';
    }

    private function buildLevels(string &$html, $collection, int $level): string
    {
        if ($collection->subs->isNotEmpty()) {
            foreach ($collection->subs as $items) {
                $this->entry($html, $items, $items->parent, $level);
            }
        }

        return $html;
    }
}
