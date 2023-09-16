<?php

namespace App\Events\Pos;

use App\Models\Pos\Pos;
use Illuminate\Queue\SerializesModels;

/**
 * Class PosStatusChanged.
 */
class PosStatusChanged
{
    use SerializesModels;

    /**
     * @var
     */
    public $bank;

    /**
     * @var
     */
    public $status;

    /**
     * PosStatusChanged constructor.
     *
     * @param  Pos  $bank
     * @param $status
     */
    public function __construct(Pos $bank, $status)
    {
        $this->bank = $bank;
        $this->status = $status;
    }
}
