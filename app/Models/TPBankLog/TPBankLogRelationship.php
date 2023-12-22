<?php

namespace App\Models\TPBankLog;

use App\Models\Bank\Bank;
use App\Models\Customer\Customer;
use App\Models\TPBankLogDetail\TPBankLogDetail;
use App\Models\Users\User;
use Google\Service\Monitoring\Custom;

/**
 * Class TPBankLogRelationship.
 */
trait TPBankLogRelationship
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
