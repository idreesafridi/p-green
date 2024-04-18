<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ResetPasswordForm extends Component
{
    public $authemail = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($authemail = null)
    {
        $this->authemail = $authemail;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.reset-password-form');
    }
}
