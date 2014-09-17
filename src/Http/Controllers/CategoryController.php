<?php namespace Stevebauman\Maintenance\Http\Controllers;

use Stevebauman\Maintenance\Services\CategoryService;
use Stevebauman\Maintenance\Validators\CategoryValidator;
use Stevebauman\Maintenance\Http\Controllers\AbstractNestedSetController;

class CategoryController extends AbstractNestedSetController {
	
	public function __construct(CategoryService $category, CategoryValidator $categoryValidator){
		$this->service = $category;
		$this->serviceValidator = $categoryValidator;
		
		$this->indexTitle = 'All Categories';
		$this->createTitle = 'Create a Category';
		$this->editTitle = 'Edit Category';
	}
	
}