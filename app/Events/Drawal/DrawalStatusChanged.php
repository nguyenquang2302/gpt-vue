<?php

namespace App\Events\Drawal;

use App\Models\Drawal\Drawal;
use Illuminate\Queue\SerializesModels;

/**
 * Class DrawalStatusChanged.
 */
class DrawalStatusChanged
{
    use SerializesModels;

    /**
     * @var
     */
    public $drawal;

    /**
     * @var
     */
    public $status;

    /**
     * DrawalStatusChanged constructor.
     *
     * @param  Drawal  $drawal
     * @param $status
     */
    public function __construct(Drawal $drawal, $status)
    {
        $this->drawal = $drawal;
        $this->status = $status;
    }
}
