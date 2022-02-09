<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;

use DataTables;

class UserController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*')->where('is_admin','!=',1);
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                        if($row->status){
                            return '<span class="badge badge-primary">Active</span>';
                        }else{
                            return '<span class="badge badge-danger">Deactive</span>';
                        }
                })
                ->addColumn('actions', function($row){
                        $btns = '<td class="text-right py-0 align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="#" class="btn btn-info"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="#" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>';

                        return $btns;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('status', $request->get('status'));
                    }
                    if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                            $search = $request->get('search');
                            $w->orWhere('name', 'LIKE', "%$search%")
                            ->orWhere('email', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['status','actions'])
                ->make(true);
        }

        return view('users.index');
    }

}