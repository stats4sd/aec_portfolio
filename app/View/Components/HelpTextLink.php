<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HelpTextLink extends Component
{

    public \App\Models\HelpTextEntry $helpTextEntry;

    public function __construct(
        public string $location,
        public string $type = 'collapse'
    )
    {
        $this->helpTextEntry = \App\Models\HelpTextEntry::firstWhere('location', $location);

    }

    public function render(): View
    {

        return view('components.help-text-link');
    }
}
