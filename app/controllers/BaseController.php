<?php

class BaseController extends Controller {

    protected $user;

    public function setupUser()
    {
        $this->user = Auth::user();
    }

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    protected function autoJson($item, $defaultView) {

    }

}
