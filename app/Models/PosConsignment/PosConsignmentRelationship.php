<?php

namespace App\Models\PosConsignment;

use App\Models\Pos\Pos;

/**
 * Class PosConsignmentRelationship.
 */
trait PosConsignmentRelationship
{
    /**
     * @return mixed
     */
    public function pos()
    {
        return $this->belongsTo(Pos::class);
    }
    
}
