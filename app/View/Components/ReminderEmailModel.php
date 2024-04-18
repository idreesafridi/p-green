<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ReminderEmailModel extends Component
{
    public $modelId = null;
    public $folderName = null;
    public $conId = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($modelId, $folderName, $conId)
    {
        $this->modelId = $modelId;
        $this->folderName = $folderName;
        $this->conId = $conId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.reminder-email-model');
    }
}
