<?php

namespace App\Services;

use App\Events\PosConsignment\PosConsignmentCreated;
use App\Events\PosConsignment\PosConsignmentDeleted;
use App\Events\PosConsignment\PosConsignmentDestroyed;
use App\Events\PosConsignment\PosConsignmentRestored;
use App\Events\PosConsignment\PosConsignmentStatusChanged;
use App\Events\PosConsignment\PosConsignmentUpdated;
use App\Models\PosConsignment\PosConsignment;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class PosConsignmentService.
 */
class PosConsignmentService extends BaseService
{
    /*
     * PosConsignmentService constructor.
     *
     * @param  PosConsignment  $PosConsignment
     */
    public function __construct(PosConsignment $PosConsignment)
    {
        $this->model = $PosConsignment;
    }
}
