<?php

namespace App\Events\Branch;

use App\Models\Branch\Branch;
use Illuminate\Queue\SerializesModels;

/**
 * Class BranchDeleted.
 */
class BranchDeleted
{
    use SerializesModels;

    /**
     * @var
     */
    public $branch;

    /**
     * @param $branch
     */
    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
    }
}
