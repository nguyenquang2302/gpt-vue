<?php

namespace App\Services;

use App\Events\FundCategory\FundCategoryCreated;
use App\Events\FundCategory\FundCategoryDeleted;
use App\Events\FundCategory\FundCategoryDestroyed;
use App\Events\FundCategory\FundCategoryRestored;
use App\Events\FundCategory\FundCategoryStatusChanged;
use App\Events\FundCategory\FundCategoryUpdated;
use App\Models\FundCategory\FundCategory;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class FundCategoryService.
 */
class FundCategoryService extends BaseService
{
    /*
     * FundCategoryService constructor.
     *
     * @param  FundCategory  $fundCategory
     */
    public function __construct(FundCategory $fundCategory)
    {
        $this->model = $fundCategory;
    }

    /**
     * @param  array  $data
     * @return FundCategory
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): FundCategory
    {
        DB::beginTransaction();

        // try {
            $fundCategory = $this->createFundCategory([
                'name' => $data['name'],
                'type' => $data['type'],
                'active' => isset($data['active']) && $data['active'] === '1',
            ]);
        // } catch (Exception $e) {
        //     DB::rollBack();

        //     throw new GeneralException(__('There was a problem creating this fundCategory. Please try again.'));
        // }

        event(new FundCategoryCreated($fundCategory));

        DB::commit();

        return $fundCategory;
    }

    /**
     * @param  FundCategory  $fundCategory
     * @param  array  $data
     * @return FundCategory
     *
     * @throws \Throwable
     */
    public function update(FundCategory $fundCategory, array $data = []): FundCategory
    {
        DB::beginTransaction();

        // try {
            $fundCategory->update([
                'name' => $data['name'],
                'type' => $data['type'],
                'active' => isset($data['active']) && $data['active'] === '1',
            ]);
        // } catch (Exception $e) {
        //     DB::rollBack();

        //     throw new GeneralException(__('There was a problem updating this fundCategory. Please try again.'));
        // }

        event(new FundCategoryUpdated($fundCategory));

        DB::commit();

        return $fundCategory;
    }


    /**
     * @param  FundCategory  $fundCategory
     * @param $status
     * @return FundCategory
     *
     * @throws GeneralException
     */
    public function mark(FundCategory $fundCategory, $status): FundCategory
    {
        $fundCategory->active = $status;

        if ($fundCategory->save()) {
            event(new FundCategoryStatusChanged($fundCategory, $status));

            return $fundCategory;
        }

        throw new GeneralException(__('There was a problem updating this fundCategory. Please try again.'));
    }

    /**
     * @param  FundCategory  $fundCategory
     * @return FundCategory
     *
     * @throws GeneralException
     */
    public function delete(FundCategory $fundCategory): FundCategory
    {
        if ($fundCategory->id === auth()->id()) {
            throw new GeneralException(__('You can not delete yourself.'));
        }

        if ($this->deleteById($fundCategory->id)) {
            event(new FundCategoryDeleted($fundCategory));

            return $fundCategory;
        }

        throw new GeneralException('There was a problem deleting this fundCategory. Please try again.');
    }

    /**
     * @param  FundCategory  $fundCategory
     * @return FundCategory
     *
     * @throws GeneralException
     */
    public function restore(FundCategory $fundCategory): FundCategory
    {
        if ($fundCategory->restore()) {
            event(new FundCategoryRestored($fundCategory));

            return $fundCategory;
        }

        throw new GeneralException(__('There was a problem restoring this fundCategory. Please try again.'));
    }

    /**
     * @param  FundCategory  $fundCategory
     * @return bool
     *
     * @throws GeneralException
     */
    public function destroy(FundCategory $fundCategory): bool
    {
        if ($fundCategory->forceDelete()) {
            event(new FundCategoryDestroyed($fundCategory));

            return true;
        }

        throw new GeneralException(__('There was a problem permanently deleting this fundCategory. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return FundCategory
     */
    protected function createFundCategory(array $data = []): FundCategory
    {
        return $this->model::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'active' => isset($data['active']) && $data['active'] =='1',
        ]);
    }
}
