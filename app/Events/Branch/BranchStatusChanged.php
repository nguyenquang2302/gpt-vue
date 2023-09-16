<?php

namespace App\Events\Branch;

use App\Models\Branch\Branch;
use Illuminate\Queue\SerializesModels;

/**
 * Class BranchStatusChanged.
 */
class BranchStatusChanged
{
    use SerializesModels;

    /**
     * @var
     */
    public $branch;

    /**
     * @var
     */
    public $status;

    /**
     * BranchStatusChanged constructor.
     *
     * @param  Branch  $branch
     * @param $status
     */
    public function __construct(Branch $branch, $status)
    {
        $this->branch = $branch;
        $this->status = $status;
    }
}
