<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ResetPasswordChangeForm extends Component
{
    public $authemail = null;
    public $authtoken = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($authemail, $authtoken)
    {
        $this->authemail = $authemail;
        $this->authtoken = $authtoken;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.reset-password-change-form');
    }
}
