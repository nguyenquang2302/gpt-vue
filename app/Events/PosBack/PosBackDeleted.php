<?php

namespace App\Events\PosBack;

use App\Models\PosBack\PosBack;
use Illuminate\Queue\SerializesModels;

/**
 * Class PosBackDeleted.
 */
class PosBackDeleted
{
    use SerializesModels;

    /**
     * @var
     */
    public $posBack;

    /**
     * @param $posBack
     */
    public function __construct(PosBack $posBack)
    {
        $this->posBack = $posBack;
    }
}
