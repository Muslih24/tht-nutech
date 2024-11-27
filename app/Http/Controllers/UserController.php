<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $title = "Manajemen User";
        $role = Role::orderBy('id', 'asc')->get();
        $data = [
            'title' => $title,
            'auth' => $auth,
            'role' => $role
        ];
        return view("user.index", $data);
    }

    public function getData(Request $request)
    {
        $columns = array(
            0 => 'u.id',
            1 => 'u.nama',
            2 => 'u.email',
            3 => 'r.nama',
            4 => 'u.image',
            5 => 'u.id',
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
            $search = " AND (u.nama ILIKE '%$search%') AND (u.email ILIKE '%$search%') AND (r.nama ILIKE '%$search%')";
        }

        if (empty($request->filter)) {
            $filter = "";
        } else {
            $filter = $request->filter;
            $filter = " AND (u.role_id = '$filter')";
        }

        $query = DB::select("SELECT
            u.id
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            Where u.id is not null
            $search
            $filter
        ");
        $totalData = count($query);
        $totalFiltered = $totalData;

        $query = DB::select("SELECT
            u.id,
            u.uuid as u_uuid,
            u.nama as u_nama,
            u.email as u_email,
            r.nama as r_nama,
            u.image as image
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            Where u.id is not null
            $search
            $filter
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
                $nestedData['nama'] = $r->u_nama;
                $nestedData['email'] = $r->u_email;
                $nestedData['role'] = $r->r_nama;
                $image = $r->image ? asset("uploads/profile/" . $r->image) : asset("assets/images/no_image.png");
                $nestedData['image'] = '<img src="' . $image . '" class="img-thumbnail" width="50" height="50">';
                $nestedData['action'] = '
                    <div class="dropdown-basic">
                        <div class="dropdown">
                            <div class="btn-group mb-0">
                                    <a href="/user/edit/' . $r->u_uuid . '" class="btn btn-sm btn-warning me-1"><span class="fas fa-pencil text-white"></span></a>
                                    <a href="javascript:void(0)" onclick="deleteData(`' . $r->u_uuid . '`)" class="btn btn-sm btn-danger"><span class="fas fa-trash text-white"></a>
                                    
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
        $title = "Manajemen User";
        $role = Role::orderBy('id', 'asc')->get();
        $data = [
            'title' => $title,
            'auth' => $auth,
            'role' => $role
        ];
        return view("user.add", $data);
    }

    public function insert(Request $request)
    {
        $auth = Auth::user();
        $segment = $request->segment(1);
        $rules = [
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required',
            'password' => 'required',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,PNG,JPG,JPEG|max:100',
        ];
        $messages = [
            'nama.required' => 'Nama harus diisi!',
            'email.required' => 'Email harus diisi!',
            'email.unique' => 'Email sudah ada!',
            'email.email' => 'Email tidak valid!',
            'role_id.required' => 'Kategori Pengguna harus diisi!',
            'password.required' => 'Password harus diisi!',
            'profile.image' => 'Gambar harus berupa gambar!',
            'profile.mimes' => 'Gambar harus berformat jpeg, png, jpg!',
            'profile.max' => 'Gambar maksimal 100kb!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            if ($request->file('profile')) {
                $profile = time() . '.' . $request->file('profile')->guessExtension();
                $request->file('profile')->move('uploads/profile/', $profile);
            } else {
                $profile = "";
            }

            $proses = new User();
            $proses->nama = $request->nama;
            $proses->email = $request->email;
            $proses->role_id = $request->role_id;
            $proses->image = $profile;
            $proses->password = bcrypt($request->password);
            $proses->created_by = $auth->nama;
            $proses->created_at = date('Y-m-d H:i:s');
            $proses->save();

            DB::commit();
            return redirect()->intended($segment)->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            die;
            return redirect()->back()->with('error', 'Data gagal ditambahkan!');
        }
    }
    public function edit($id)
    {
        $auth = Auth::user();
        $title = "Manajemen User";
        $user = User::where('uuid', $id)->first();
        $role = Role::orderBy('id', 'asc')->get();
        $data = [
            'title' => $title,
            'auth' => $auth,
            'role' => $role,
            'user' => $user,
        ];
        return view("user.edit", $data);
    }
    public function update(Request $request, $id)
    {
        $auth = Auth::user();
        $segment = $request->segment(1);
        $rules = [
            'nama' => 'required',
            'email' => 'required|unique:users,email,' . $id . ',uuid',
            'role_id' => 'required',
            'profile' => 'nullable|mimes:jpeg,png,jpg,PNG,JPG,JPEG|max:100',
        ];
        $messages = [
            'nama.required' => 'Nama harus diisi!',
            'email.required' => 'Email harus diisi!',
            'email.unique' => 'Email sudah ada!',
            'role_id.required' => 'Kategori Pengguna harus diisi!',
            'profile.mimes' => 'Gambar harus berformat jpeg, png, jpg!',
            'profile.max' => 'Gambar maksimal 100kb!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $proses = User::where('uuid', $id)->first();
            if ($request->file('profile')) {
                $profile = time() . '.' . $request->file('profile')->guessExtension();
                if ($proses->image != "") {
                    (File::exists('uploads/profile/' . $proses->image) ? File::delete('uploads/profile/' . $proses->image) : '');
                }
                $request->file('profile')->move('uploads/profile/', $profile);
            } else {
                $profile = $proses->image;
            }
            $proses->nama = $request->nama;
            $proses->email = $request->email;
            $proses->role_id = $request->role_id;
            $proses->password = $request->password ? bcrypt($request->password) : $proses->password;
            $proses->image = $profile;
            $proses->updated_by = $auth->nama;
            $proses->updated_at = date('Y-m-d H:i:s');
            $proses->save();

            DB::commit();
            return redirect()->intended($segment)->with('success', 'Data berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            die;
            return redirect()->back()->with('error', 'Data gagal diubah!');
        }
    }
    public function delete($id)
    {
        $user = User::where('uuid', $id)->first();


        DB::beginTransaction();
        try {
            if ($user->image != "") {
                (File::exists('uploads/profile/' . $user->image) ? File::delete('uploads/profile/' . $user->image) : '');
            }
            $user->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Data gagal dihapus!');
        }
    }
    public function export()
    {
        $auth = Auth::user();
        $search = (!empty($_REQUEST['search']) ? $_REQUEST['search'] : "");
        $filter = (!empty($_REQUEST['filter']) ? $_REQUEST['filter'] : "");
        if (empty($search)) {
            $search = "";
        } else {
            $search = " AND (u.nama ILIKE '%$search%') AND (u.email ILIKE '%$search%') AND (r.nama ILIKE '%$search%')";
        }

        if (empty($filter)) {
            $filter = "";
        } else {
            $filter = " AND (role_id = '$filter')";
        }
        $query = DB::select("SELECT
            u.id,
            u.uuid as u_uuid,
            u.nama as u_nama,
            u.email as u_email,
            r.nama as r_nama,
            u.image as image
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            Where u.id is not null
            $search
            $filter
        ");

        $data = array();
        foreach ($query as $key => $r) {
            $nestedData['nama'] = $r->u_nama;
            $nestedData['email'] = $r->u_email;
            $nestedData['role'] = $r->r_nama;
            $data[] = $nestedData;
        }

        $data = json_decode(json_encode($data));

        $params = [
            'auth' => $auth,
            'data' => $data,
            'title' => 'User'
        ];
        return view("user.export", $params);
    }
}
