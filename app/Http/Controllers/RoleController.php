<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $title = "Manajemen Role";
        $data = [
            'title' => $title,
            'auth' => $auth
        ];
        return view("role.index", $data);
    }

    public function getData(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'nama',
            2 => 'id',
        );

        $limit = $_POST['length'];
        $start = $_POST['start'];
        // $search = $request->search;
        $order = $columns[$_REQUEST['order']['0']['column']];
        $dir = $_REQUEST['order']['0']['dir'];

        if (empty($request->search)) {
            $search = "";
        } else {
            $search = $request->search;
            $search = "AND (nama ILIKE '%$search%')";
        }

        $query = DB::select("SELECT
            id
            FROM roles
            Where id is not null
            $search
        ");
        $totalData = count($query);
        $totalFiltered = $totalData;

        $query = DB::select("SELECT
            id,
            uuid,
            nama
            FROM roles
            Where id is not null
            $search
            ORDER BY $order $dir
            LIMIT $limit OFFSET $start
        ");

        if (!empty($_POST['search']['value'])) {
            $totalFiltered = $totalData;
        }

        $data = array();
        if (!empty($query)) {
            $no = $start + 1;
            foreach ($query as $key => $r) {
                $nestedData['no'] = $no;
                $nestedData['nama'] = $r->nama;
                $nestedData['action'] = '
                    <div class="dropdown-basic">
                        <div class="dropdown">
                            <div class="btn-group mb-0">
                                    <a href="/role/edit/' . $r->uuid . '" class="btn btn-sm btn-warning me-1"><span class="fas fa-pencil text-white"></span></a>
                                    <a href="javascript:void(0)" onclick="deleteData(`' . $r->uuid . '`)" class="btn btn-sm btn-danger"><span class="fas fa-trash text-white"></a>
                                    
                            </div>
                        </div>
                    </div>
                ';
                $data[] = $nestedData;
                $no++;
            }
        }

        $json_data = array(
            "draw"            => intval($_POST['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }


    public function add()
    {
        $auth = Auth::user();
        $title = "Manajemen Role";
        $data = [
            'title' => $title,
            'auth' => $auth
        ];
        return view("role.add", $data);
    }

    public function insert(Request $request)
    {
        $auth = Auth::user();
        $segment = $request->segment(1);
        $rules = [
            'nama' => 'required|unique:roles,nama',
        ];
        $messages = [
            'nama.required' => 'Role harus diisi!',
            'nama.unique' => 'Role sudah ada!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $proses = new Role();
            $proses->nama = $request->nama;
            $proses->created_by = $auth->nama;
            $proses->created_at = date('Y-m-d H:i:s');
            $proses->save();

            DB::commit();
            return redirect()->intended($segment)->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Data gagal ditambahkan!');
        }
    }
    public function edit($id)
    {
        $auth = Auth::user();
        $title = "Manajemen Role";
        $role = Role::where('uuid', $id)->first();
        $data = [
            'title' => $title,
            'auth' => $auth,
            'role' => $role
        ];
        return view("role.edit", $data);
    }
    public function update(Request $request, $id)
    {
        $auth = Auth::user();
        $segment = $request->segment(1);
        $rules = [
            'nama' => 'required|unique:roles,nama,' . $id . ',uuid',
        ];
        $messages = [
            'nama.required' => 'Role harus diisi!',
            'nama.unique' => 'Role sudah ada!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $proses = Role::where('uuid', $id)->first();
            $proses->nama = $request->nama;
            $proses->updated_by = $auth->nama;
            $proses->updated_at = date('Y-m-d H:i:s');
            $proses->save();

            DB::commit();
            return redirect()->intended($segment)->with('success', 'Data berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();die;
            return redirect()->back()->with('error', 'Data gagal diubah!');
        }
    }
    public function delete($id)
    {
        $role = Role::where('uuid', $id);
        DB::beginTransaction();
        try {
            $role->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Data gagal dihapus!');
        }
    }
}
