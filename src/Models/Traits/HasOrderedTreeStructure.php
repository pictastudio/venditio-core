<?php

namespace PictaStudio\Venditio\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Nevadskiy\Tree\AsTree;
use Nevadskiy\Tree\Relations\Descendants;

trait HasOrderedTreeStructure
{
    use AsTree {
        children as protected unorderedChildren;
        descendants as protected unorderedDescendants;
    }

    public function children(): HasMany
    {
        return $this->unorderedChildren()
            ->orderBy($this->qualifyColumn('sort_order'))
            ->orderBy($this->getQualifiedKeyName());
    }

    public function descendants(): Descendants
    {
        return $this->unorderedDescendants()
            ->orderBy($this->qualifyColumn($this->getParentKeyName()))
            ->orderBy($this->qualifyColumn('sort_order'))
            ->orderBy($this->getQualifiedKeyName());
    }
}
