<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HelpTextEntry extends Component
{

    public \App\Models\HelpTextEntry $helpTextEntry;

    public function __construct(
        public string $sectionId,
        public string $location,
    )
    {
        $this->helpTextEntry = \App\Models\HelpTextEntry::firstWhere('location', $location);
    }

    public function render(): View
    {
        return view('components.help-text-entry');
    }
}
