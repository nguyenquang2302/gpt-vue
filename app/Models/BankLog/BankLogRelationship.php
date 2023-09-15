<?php

namespace App\Models\BankLog;

use App\Models\Bank\Bank;
use App\Models\Customer\Customer;
use App\Models\BankLogDetail\BankLogDetail;
use App\Models\Users\User;
use Google\Service\Monitoring\Custom;

/**
 * Class BankLogRelationship.
 */
trait BankLogRelationship
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userBelongto()
    {
        return $this->belongsTo(User::class, 'user_id_belongto', 'id');
    }

}
