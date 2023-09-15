<?php

namespace App\Events\Drawal;

use App\Models\Drawal\Drawal;
use App\Services\TelegramService;
use Illuminate\Queue\SerializesModels;

/**
 * Class DrawalVerify.
 */
class DrawalVerify
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

        TelegramService::sendMessageConfirmRT($drawal);

    }
}
