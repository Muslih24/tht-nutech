<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $title = "Profile";
        $role = Role::orderBy('id', 'asc')->get();
        $user = User::where('uuid', $auth->uuid)->first();
        $data = [
            'title' => $title,
            'auth' => $auth,
            'role' => $role,
            'user' => $user
        ];
        return view("profile.index", $data);
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
        return view("profile.edit", $data);
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
            echo $e->getMessage();die;
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
            Auth::logout();
            return redirect()->intended('/')->with('success', 'Akun berhasil dihapus, anda tidak bisa login kembali!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Data gagal dihapus!');
        }
    }
}
