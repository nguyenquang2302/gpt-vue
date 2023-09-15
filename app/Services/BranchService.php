<?php

namespace App\Services;

use App\Events\Branch\BranchCreated;
use App\Events\Branch\BranchDeleted;
use App\Events\Branch\BranchDestroyed;
use App\Events\Branch\BranchRestored;
use App\Events\Branch\BranchStatusChanged;
use App\Events\Branch\BranchUpdated;
use App\Models\Branch\Branch;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class BranchService.
 */
class BranchService extends BaseService
{
    /*
     * BranchService constructor.
     *
     * @param  Branch  $branch
     */
    public function __construct(Branch $branch)
    {
        $this->model = $branch;
    }

    /**
     * @param $type
     * @param  bool|int  $perPage
     * @return mixed
     */
    public function getByType($type, $perPage = false)
    {
        if (is_numeric($perPage)) {
            return $this->model::byType($type)->paginate($perPage);
        }

        return $this->model::byType($type)->get();
    }


    /**
     * @param  array  $data
     * @return Branch
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): Branch
    {
        DB::beginTransaction();

        try {
            $branch = $this->createBranch([
                'name' => $data['name'],
                'province_id' => $data['province_id'],
                'district_id' => $data['district_id'],
                'ward_id' => $data['ward_id'],
                'address' => $data['address'],
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this branch. Please try again.'));
        }

        event(new BranchCreated($branch));

        DB::commit();

        return $branch;
    }

    /**
     * @param  Branch  $branch
     * @param  array  $data
     * @return Branch
     *
     * @throws \Throwable
     */
    public function update(Branch $branch, array $data = []): Branch
    {
        DB::beginTransaction();

        try {
            $branch->update([
                'name' => $data['name'],
                'province_id' => $data['province_id'],
                'district_id' => $data['district_id'],
                'ward_id' => $data['ward_id'],
                'address' => $data['address'],
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this branch. Please try again.'));
        }

        event(new BranchUpdated($branch));

        DB::commit();

        return $branch;
    }


    /**
     * @param  Branch  $branch
     * @param $status
     * @return Branch
     *
     * @throws GeneralException
     */
    public function mark(Branch $branch, $status): Branch
    {
        $branch->active = $status;

        if ($branch->save()) {
            event(new BranchStatusChanged($branch, $status));

            return $branch;
        }

        throw new GeneralException(__('There was a problem updating this branch. Please try again.'));
    }

    /**
     * @param  Branch  $branch
     * @return Branch
     *
     * @throws GeneralException
     */
    public function delete(Branch $branch): Branch
    {
        if ($branch->id === auth()->id()) {
            throw new GeneralException(__('You can not delete yourself.'));
        }

        if ($this->deleteById($branch->id)) {
            event(new BranchDeleted($branch));

            return $branch;
        }

        throw new GeneralException('There was a problem deleting this branch. Please try again.');
    }

    /**
     * @param  Branch  $branch
     * @return Branch
     *
     * @throws GeneralException
     */
    public function restore(Branch $branch): Branch
    {
        if ($branch->restore()) {
            event(new BranchRestored($branch));

            return $branch;
        }

        throw new GeneralException(__('There was a problem restoring this branch. Please try again.'));
    }

    /**
     * @param  Branch  $branch
     * @return bool
     *
     * @throws GeneralException
     */
    public function destroy(Branch $branch): bool
    {
        if ($branch->forceDelete()) {
            event(new BranchDestroyed($branch));

            return true;
        }

        throw new GeneralException(__('There was a problem permanently deleting this branch. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return Branch
     */
    protected function createBranch(array $data = []): Branch
    {
        return $this->model::create([
            'name' => $data['name'],
            'province_id' => $data['province_id'],
            'district_id' => $data['district_id'],
            'ward_id' => $data['ward_id'],
            'address' => $data['address'],
        ]);
    }
}
