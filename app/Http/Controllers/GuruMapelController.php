<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\GuruMapel;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class GuruMapelController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $guruMapel = GuruMapel::with(['guru', 'mapel'])->get();
            return DataTables::of($guruMapel)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit-btn btn btn-warning btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="delete-btn btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $guru = Guru::all();
        $mapel = Mapel::all();
        return view('master.mapel.guru-mapel', compact('guru', 'mapel'));
    }

    public function create() {}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guru_id' => 'required|exists:guru,id',
            'mapel_id' => 'required|exists:mapel,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }

        GuruMapel::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Guru Mapel created successfully',
        ]);
    }

    public function edit($id)
    {
        $guruMapel = GuruMapel::with(['guru', 'mapel'])->findOrFail($id);
        return response()->json($guruMapel);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'guru_id' => 'required|exists:guru,id',
            'mapel_id' => 'required|exists:mapel,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }

        $guruMapel = GuruMapel::findOrFail($id);
        $guruMapel->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Guru Mapel updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $guruMapel = GuruMapel::findOrFail($id);
        $guruMapel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guru Mapel deleted successfully',
        ]);
    }
}
