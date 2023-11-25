<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Pos\DeletePosRequest;
use App\Http\Requests\Backend\Pos\UpdatePosRequest;
use App\Models\Pos\Pos;
use App\Services\PosService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class PosController extends Controller
{

    /**
     * @var PosService
     */
    protected $posService;

    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @var PermissionService
     */
    protected $permissionService;


    /**
     * PosController constructor.
     *
     * @param  PosService  $posService
     * @param  RoleService  $roleService
     * @param  PermissionService  $permissionService
     */
    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    /**
     * Display a listing of the resource.
     */
    public function lists(Request $request)
    {

        return response([
            'poss' => Pos::query()
                    ->when($request->input('search'), function ($query, $search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })->get()
        ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rowsPerPage = $request->input('rowsPerPage'); 
        $page = $request->input('page'); 

        return response([
            'poss' => Pos::query()
                    ->when($request->input('search'), function ($query, $search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })->paginate($rowsPerPage),
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
     /**
     * @param  Pos  $pos
     * @return mixed
     */
    public function show(Pos $pos)
    {
        return response([
            'pos' => $pos
        ], Response::HTTP_OK);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePosRequest $request, Pos $pos)

    {
         $this->posService->update($pos, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeletePosRequest $request, Pos $pos)
    {
       
        $this->posService->delete($pos);

        return response([
            'msg' => 'Xoá thành công',
        ], Response::HTTP_OK);

    }
}
