<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FilePreviewModal extends Component
{
    public $modelId = null;
    public $filepath = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($modelId, $filepath)
    {
  
        $this->modelId = $modelId;
        $this->filepath = $filepath;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.file-preview-modal');
    }
}
