<?php namespace Stevebauman\Maintenance\Services;

use Stevebauman\Maintenance\Models\WorkOrder;
use Stevebauman\Maintenance\Services\SentryService;

class WorkOrderService extends AbstractModelService {
	
	public function __construct(WorkOrder $workOrder, SentryService $sentry){
		$this->model = $workOrder;
		$this->sentry = $sentry;
        }
        
	public function getByPageWithFilter($data = array()){
		return $this->model
			->with(array(
				'category',
				'user',
                                'sessions',
			))
                        ->priority((array_key_exists('priority', $data) ? $data['priority'] : NULL))
                        ->subject((array_key_exists('subject', $data) ? $data['subject'] : NULL))
                        ->description((array_key_exists('description', $data) ? $data['description'] : NULL))
                        ->status((array_key_exists('status', $data) ? $data['status'] : NULL))
                        ->category((array_key_exists('work_order_category_id', $data) ? $data['work_order_category_id'] : NULL))
                        ->assets((array_key_exists('assets', $data) ? $data['assets'] : NULL))
                        ->orderBy('created_at', 'DESC')
			->paginate(25);
	}
	
	public function getByPageWithCategoryId($category_id){
		return $this->model
			->with(array(
				'status',
				'updates',
				'category',
				'user',
			))
			->where('work_order_category_id', $category_id)
			->paginate(25);
	}
	
	public function getMakes($make = NULL){
		return $this->model
			->select('make')
			->distinct()
			->where('make', 'LIKE', '%'.$make.'%')
			->get();
	}
	
	public function getModels($model = NULL){
		return $this->model
			->distinct()
			->select('model')
			->where('model', 'LIKE', '%'.$model.'%')
			->get();
	}
	
	public function getSerials($serial = NULL){
		return $this->model
			->distinct()
			->select('serial')
			->where('serial', 'LIKE', '%'.$serial.'%')
			->get();
	}
	
	public function create($data){
		$insert = array(
			'user_id' => $this->sentry->getCurrentUser()->id,
			'work_order_category_id' => $data['work_order_category_id'],
                        'location_id' => $data['location_id'],
			'status' => $data['status'],
                        'priority' => $data['priority'],
			'subject' => $data['subject'],
			'description' => $data['description'],
			'started_at' => $this->formatDateWithTime($data['started_at_date'], $data['started_at_time']),
			'completed_at' =>$this->formatDateWithTime($data['completed_at_date'], $data['completed_at_time']),
		);
		
		if($record = $this->model->create($insert)){
                    if(array_key_exists('assets', $data)){
                        $record->assets()->attach($data['assets']);
                    }
		
                    return $record;
		} return false;
	}
	
	public function update($id, $data){
		if($workOrder = $this->find($id)){
			$insert = array(
                            'work_order_category_id' => $data['work_order_category_id'],
                            'location_id' => $data['location_id'],
                            'status' => $data['status'],
                            'priority' => $data['priority'],
                            'subject' => $data['subject'],
                            'description' => $data['description'],
                            'started_at' => $this->formatDateWithTime($data['started_at_date'], $data['started_at_time']),
                            'completed_at' =>$this->formatDateWithTime($data['completed_at_date'], $data['completed_at_time']),
			);
			
			if($workOrder->update($insert)){
                            if(array_key_exists('assets', $data)){
                                $workOrder->assets()->sync($data['assets']);
                            }
                            
                            return $workOrder;
			} return false;
		}
	}
	
	private function formatDateWithTime($date, $time = NULL){
            if($date){
                    return date('Y-m-d H:i:s', strtotime($date. ' ' .$time));
            } return NULL;
	}
	
}