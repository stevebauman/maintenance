<?php namespace Stevebauman\Maintenance\Validators;

use Stevebauman\Maintenance\Validators\AbstractValidator;

class AssetCategoryValidator extends AbstractValidator {
	
	protected $rules = array(
		'name' => 'required|max:20',
	);
	
}