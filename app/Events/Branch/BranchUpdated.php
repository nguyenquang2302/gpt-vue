<?php

namespace App\Events\Branch;

use App\Models\Branch\Branch;
use Illuminate\Queue\SerializesModels;

/**
 * Class BranchUpdated.
 */
class BranchUpdated
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
