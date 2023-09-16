<?php

namespace App\Events\Pos;

use App\Models\Pos\Pos;
use Illuminate\Queue\SerializesModels;

/**
 * Class PosUpdated.
 */
class PosUpdated
{
    use SerializesModels;

    /**
     * @var
     */
    public $bank;

    /**
     * @param $bank
     */
    public function __construct(Pos $bank)
    {
        $this->bank = $bank;
    }
}
