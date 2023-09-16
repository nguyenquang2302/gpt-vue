<?php

namespace App\Events\PosBack;

use App\Models\PosBack\PosBack;
use Illuminate\Queue\SerializesModels;

/**
 * Class PosBackStatusChanged.
 */
class PosBackStatusChanged
{
    use SerializesModels;

    /**
     * @var
     */
    public $posBack;

    /**
     * @var
     */
    public $status;

    /**
     * PosBackStatusChanged constructor.
     *
     * @param  PosBack  $posBack
     * @param $status
     */
    public function __construct(PosBack $posBack, $status)
    {
        $this->posBack = $posBack;
        $this->status = $status;
    }
}
