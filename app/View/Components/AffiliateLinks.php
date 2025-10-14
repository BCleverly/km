<?php

namespace App\View\Components;

use App\Models\Tasks\Task;
use Illuminate\View\Component;
use Illuminate\View\View;

class AffiliateLinks extends Component
{
    public function __construct(
        public Task $task,
        public bool $showPrimary = true,
        public bool $showAll = false,
        public string $class = ''
    ) {}

    public function render(): View
    {
        $affiliateLinks = $this->showAll 
            ? $this->task->affiliateLinks 
            : $this->task->primaryAffiliateLink;

        return view('components.affiliate-links', [
            'affiliateLinks' => $affiliateLinks,
        ]);
    }
}