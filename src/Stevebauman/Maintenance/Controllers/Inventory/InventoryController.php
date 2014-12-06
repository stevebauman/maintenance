<?php 

namespace Stevebauman\Maintenance\Controllers\Inventory;

use Stevebauman\Maintenance\Validators\InventoryValidator;
use Stevebauman\Maintenance\Services\Inventory\InventoryService;
use Stevebauman\Maintenance\Controllers\BaseController;

class InventoryController extends BaseController {
        
        public function __construct(InventoryService $inventory, InventoryValidator $inventoryValidator) {
            $this->inventory = $inventory;
            $this->inventoryValidator = $inventoryValidator;
        }
    
	/**
	 * Display all inventory entries (paginated with search functionality)
	 *
	 * @return Response
	 */
	public function index(){

            $items = $this->inventory->setInput($this->inputAll())->getByPageWithFilter();
            
            return view('maintenance::inventory.index', array(
                'title' => 'Inventory',
                'items' => $items,
            ));
	}


	/**
	 * Show the form for creating an inventory
	 *
	 * @return Response
	 */
	public function create(){
            return view('maintenance::inventory.create', array(
                'title' => 'Add an Item to the Inventory',
            ));
	}


	/**
	 * Store a new inventory
	 *
	 * @return Response
	 */
	public function store(){
            
            if($this->inventoryValidator->passes()){
                
                $record = $this->inventory->setInput($this->inputAll())->create();
                
                if($record){
                    $this->message = sprintf('Successfully added item to the inventory: %s', link_to_route('maintenance.inventory.show', 'Show', array($record->id)));
                    $this->messageType = 'success';
                    $this->redirect = route('maintenance.inventory.index');

                } else{
                    $this->message = 'There was an error adding this item to the inventory. Please try again.';
                    $this->messageType = 'danger';
                    $this->redirect = route('maintenance.inventory.index');
                }
                
            } else{
                $this->errors = $this->inventoryValidator->getErrors();
            }
            
            return $this->response();
	}


	/**
	 * Display the specified inventory
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
            
            $item = $this->inventory->find($id);

            return view('maintenance::inventory.show', array(
                'title' => 'Viewing Inventory Item: '.$item->name,
                'item' => $item,
            ));
	}


	/**
	 * Displays the edit form for the specified inventory
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id){
            $item = $this->inventory->find($id);

            return view('maintenance::inventory.edit', array(
                'title' => 'Editing Inventory Item: '.$item->name,
                'item' => $item,
            ));
	}


	/**
	 * Updates the specified inventory
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id){
            if($this->inventoryValidator->passes()){
                
                $item = $this->inventory->setInput($this->inputAll())->update($id);
                
                if($item){
                    $this->message = sprintf('Successfully updated item: %s', link_to_route('maintenance.inventory.show', 'Show', array($item->id)));
                    $this->messageType = 'success';
                    $this->redirect = route('maintenance.inventory.show', array($item->id));
                    
                } else{
                    $this->message = 'There was an error trying to update this item. Please try again.';
                    $this->messageType = 'danger';
                    $this->redirect = route('maintenance.inventory.edit', array($item->id));
                }

            } else{
                $this->errors = $this->inventoryValidator->getErrors();
            }
            
            return $this->response();
	}


	/**
	 * Removes the specified inventory
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id){
            $this->inventory->destroy($id);

            $this->redirect = route('maintenance.inventory.index');
            $this->message = 'Successfully deleted item';
            $this->messageType = 'success';

            return $this->response();
	}
}