<?php

namespace App\Jobs\WorkRequest;

use App\Http\Requests\WorkRequest as WorkHttpRequest;
use App\Models\WorkRequest;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class Update extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var WorkHttpRequest
     */
    protected $request;

    /**
     * @var WorkRequest
     */
    protected $workRequest;

    /**
     * Constructor.
     *
     * @param WorkHttpRequest $request
     * @param WorkRequest     $workRequest
     */
    public function __construct(WorkHttpRequest $request, WorkRequest $workRequest)
    {
        $this->request = $request;
        $this->workRequest = $workRequest;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $this->workRequest->subject = $this->request->input('subject', $this->workRequest->subject);
        $this->workRequest->best_time = $this->request->input('best_time', $this->workRequest->best_time);
        $this->workRequest->description = $this->request->clean($this->request->input('description', $this->workRequest->description));

        return $this->workRequest->save();
    }
}
