<?php

namespace App\Events\Pos;

use App\Models\Pos\Pos;
use Illuminate\Queue\SerializesModels;

/**
 * Class PosLoggedIn.
 */
class PosLoggedIn
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
