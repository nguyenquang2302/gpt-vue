<?php

namespace App\Events\PosBack;

use App\Models\PosBack\PosBack;
use Illuminate\Queue\SerializesModels;

/**
 * Class PosBackUpdated.
 */
class PosBackUpdated
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
