<?php

namespace App\Events\Drawal;

use App\Models\Drawal\Drawal;
use Illuminate\Queue\SerializesModels;

/**
 * Class DrawalDestroyed.
 */
class DrawalDestroyed
{
    use SerializesModels;

    /**
     * @var
     */
    public $drawal;

    /**
     * @param $drawal
     */
    public function __construct(Drawal $drawal)
    {
        $this->drawal = $drawal;
    }
}
