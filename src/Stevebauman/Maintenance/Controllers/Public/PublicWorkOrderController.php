<?php

namespace Stevebauman\Maintenance\Controllers;

use Stevebauman\Maintenance\Validators\PublicWorkOrderValidator;
use Stevebauman\Maintenance\Services\WorkOrder\PublicService;
use Stevebauman\Maintenance\Controllers\BaseController;

class PublicWorkOrderController extends BaseController {
    
    public function __construct(
            PublicService $workOrder, 
            PublicWorkOrderValidator $workOrderValidator
        )
    {
        $this->workOrder = $workOrder;
        $this->workOrderValidator = $workOrderValidator;
    }
    
    public function index()
    {
        $workOrders = $this->workOrder->getByPageByUser();
        
        return view('maintenance::public.work-orders.index', array(
            'title' => 'My Work Requests',
            'workOrders' => $workOrders
        ));
    }
    
    public function create()
    {
        return view('maintenance::public.work-orders.create', array(
            'title' => 'Submit a Work Request'
        ));
    }
    
    public function store()
    {
        if($this->workOrderValidator->passes()){
            
            $record = $this->workOrder->setInput($this->inputAll())->create();
            
            $this->message = sprintf('Successfully submitted work order request. %s', link_to_route('maintenance.work-requests.show', 'Show', array($record->id)));
            $this->messageType = 'success';
            $this->redirect = route('maintenance.work-requests.index');
            
        } else{
            $this->errors = $this->workOrderValidator->getErrors();
            $this->redirect = route('maintenance.work-requests.create');
        }
        
        return $this->response();
    }
    
    public function show($id)
    {
        $workOrder = $this->workOrder->find($id);
        
        return view('maintenance::public.work-orders.show', array(
            'title' => 'Viewing Work Request',
            'workOrder' => $workOrder
        ));
    }
    
    public function edit($id)
    {
        $workOrder = $this->workOrder->find($id);
        
        return view('maintenance::public.work-orders.edit', array(
            'title' => 'Editing Work Request',
            'workOrder' => $workOrder
        ));
    }
    
    public function update($id)
    {
        if($this->workOrderValidator->passes()){
            
            $record = $this->workOrder->setInput($this->inputAll())->update($id);
            
            $this->message = sprintf('Successfully edited work order request. %s', link_to_route('maintenance.work-requests.show', 'Show', array($record->id)));
            $this->messageType = 'success';
            $this->redirect = route('maintenance.work-requests.index');
            
        } else{
            $this->errors = $this->workOrderValidator->getErrors();
            $this->redirect = route('maintenance.work-requests.edit');
        }
        
        return $this->response();
    }
    
    public function destroy($id)
    {
        if($this->workOrder->destroy($id)){
            $this->message = 'Successfully deleted work request';
            $this->messageType = 'success';
            $this->redirect = route('maintenance.work-requests.index');
        } else{
            $this->message = 'There was an error trying to delete your work request. Please try again.';
            $this->messageType = 'danger';
            $this->redirect = route('maintenance.work-requests.index');
        }
        
        return $this->response();
    }
    
}