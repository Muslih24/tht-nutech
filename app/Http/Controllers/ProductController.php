<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $title = "Manajemen Product";
        $category = Category::orderBy('id', 'asc')->get();
        $data = [
            'title' => $title,
            'auth' => $auth,
            'category' => $category
        ];
        return view("product.index", $data);
    }

    public function getData(Request $request)
    {
        $auth = Auth::user();
        $columns = array(
            0 => 'u.id',
            1 => 'p.image',
            2 => 'p.nama',
            3 => 'c.nama',
            4 => 'p.harga_beli',
            5 => 'p.harga_jual',
            6 => 'p.stok',
            7 => 'u.id',
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
            $search = " AND ((p.nama ILIKE '%$search%') OR (c.nama ILIKE '%$search%') OR (CAST(p.harga_beli AS text) ILIKE '%$search%') OR (CAST(p.harga_jual AS text) ILIKE '%$search%') OR (CAST(p.stok AS text) ILIKE '%$search%'))";
        }

        if (empty($request->filter)) {
            $filter = "";
        } else {
            $filter = $request->filter;
            $filter = " AND (p.category_id = '$filter')";
        }

        if ($auth->role_id != 1) {
            $user = " AND (p.user_id = '$auth->id')";
        } else {
            $user = "";
        }

        $query = DB::select("SELECT
            p.id
            FROM products p
            INNER JOIN users u ON u.id = p.user_id
            INNER JOIN categories c ON c.id = p.category_id
            Where p.id is not null
            $filter
            $user
            $search
        ");
        $totalData = count($query);
        $totalFiltered = $totalData;

        $query = DB::select("SELECT
            p.id,
            p.uuid as p_uuid,
            p.image as image,
            p.nama as p_nama,
            c.nama as c_nama,
            p.harga_beli as harga_beli,
            p.harga_jual as harga_jual,
            p.stok as stok,
            u.nama as u_nama
            FROM products p
            INNER JOIN users u ON u.id = p.user_id
            INNER JOIN categories c ON c.id = p.category_id
            Where p.id is not null
            $filter
            $user
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
                $image = $r->image ? asset("uploads/product/" . $r->image) : asset("assets/images/no_image.png");
                $nestedData['image'] = '<img src="' . $image . '" class="img-thumbnail" width="50" height="50">';
                $nestedData['nama'] = $r->p_nama;
                $nestedData['kategori'] = $r->c_nama;
                $nestedData['harga_beli'] = "Rp " . number_format($r->harga_beli, 0, '.', '.');
                $nestedData['harga_jual'] = "Rp " . number_format($r->harga_jual, 0, '.', '.');
                $nestedData['stok'] =  number_format($r->stok, 0, '.', '.');
                $nestedData['action'] = '
                    <div class="dropdown-basic">
                        <div class="dropdown">
                            <div class="btn-group mb-0">
                                    <a href="/product/edit/' . $r->p_uuid . '" class="btn btn-sm btn-warning me-1"><span class="fas fa-pencil text-white"></span></a>
                                    <a href="javascript:void(0)" onclick="deleteData(`' . $r->p_uuid . '`)" class="btn btn-sm btn-danger"><span class="fas fa-trash text-white"></a>
                                    
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
        $title = "Manajemen Product";
        $category = Category::orderBy('id', 'asc')->get();
        $data = [
            'title' => $title,
            'auth' => $auth,
            'category' => $category
        ];
        return view("product.add", $data);
    }

    public function insert(Request $request)
    {
        $auth = Auth::user();
        $segment = $request->segment(1);
        $rules = [
            'nama' => 'required|unique:products,nama',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|numeric',
            'category_id' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,PNG,JPG,JPEG|max:100',
        ];
        $messages = [
            'nama.required' => 'Produk harus diisi!',
            'nama.unique' => 'Produk sudah ada!',
            'harga_beli.required' => 'Harga Beli harus diisi!',
            'harga_beli.numeric' => 'Harga Beli harus berupa angka!',
            'harga_jual.required' => 'Harga Jual harus diisi!',
            'harga_jual.numeric' => 'Harga Jual harus berupa angka!',
            'stok.required' => 'Stok harus diisi!',
            'stok.numeric' => 'Stok harus berupa angka!',
            'category_id.required' => 'Kategori harus diisi!',
            'gambar.image' => 'Gambar harus berupa gambar!',
            'gambar.mimes' => 'Gambar harus berformat jpeg, png, jpg!',
            'gambar.max' => 'Gambar maksimal 100kb!',
            'category_id.required' => 'Kategori Produk harus diisi!',
        ];


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            if ($request->file('gambar')) {
                $image = time() . '.' . $request->file('gambar')->guessExtension();
                $request->file('gambar')->move('uploads/product/', $image);
            } else {
                $image = "";
            }

            $proses = new Product();
            $proses->nama = $request->nama;
            $proses->user_id = $auth->id;
            $proses->harga_beli = str_replace('.', '', $request->harga_beli);
            $proses->harga_jual = str_replace('.', '', $request->harga_jual);
            $proses->stok = str_replace('.', '', $request->stok);
            $proses->category_id = $request->category_id;
            $proses->image = $image;
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
        $title = "Manajemen Product";
        $product = Product::where('uuid', $id)->first();
        $category = Category::orderBy('id', 'asc')->get();
        $data = [
            'title' => $title,
            'auth' => $auth,
            'category' => $category,
            'product' => $product,
        ];
        return view("product.edit", $data);
    }
    public function update(Request $request, $id)
    {
        $auth = Auth::user();
        $segment = $request->segment(1);
        $rules = [
            'nama' => 'required|unique:products,nama,' . $id . ',uuid',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|numeric',
            'category_id' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,PNG,JPG,JPEG|max:100',
        ];
        $messages = [
            'nama.required' => 'Produk harus diisi!',
            'nama.unique' => 'Produk sudah ada!',
            'harga_beli.required' => 'Harga Beli harus diisi!',
            'harga_beli.numeric' => 'Harga Beli harus berupa angka!',
            'harga_jual.required' => 'Harga Jual harus diisi!',
            'harga_jual.numeric' => 'Harga Jual harus berupa angka!',
            'stok.required' => 'Stok harus diisi!',
            'stok.numeric' => 'Stok harus berupa angka!',
            'category_id.required' => 'Kategori harus diisi!',
            'gambar.image' => 'Gambar harus berupa gambar!',
            'gambar.mimes' => 'Gambar harus berformat jpeg, png, jpg!',
            'gambar.max' => 'Gambar maksimal 100kb!',
            'category_id.required' => 'Kategori Produk harus diisi!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $proses = Product::where('uuid', $id)->first();
            if ($request->file('gambar')) {
                $image = time() . '.' . $request->file('gambar')->guessExtension();
                if ($proses->image != "") {
                    (File::exists('uploads/product/' . $proses->image) ? File::delete('uploads/product/' . $proses->image) : '');
                }
                $request->file('gambar')->move('uploads/product/', $image);
            } else {
                $image = $proses->image;
            }
            $proses->nama = $request->nama;
            $proses->harga_beli = str_replace('.', '', $request->harga_beli);
            $proses->harga_jual = str_replace('.', '', $request->harga_jual);
            $proses->stok = str_replace('.', '', $request->stok);
            $proses->category_id = $request->category_id;
            $proses->image = $image;
            $proses->updated_by = $auth->nama;
            $proses->updated_at = date('Y-m-d H:i:s');
            $proses->save();

            DB::commit();
            return redirect()->intended($segment)->with('success', 'Data berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()->with('error', 'Data gagal diubah!');
        }
    }
    public function delete($id)
    {
        $prodcut = Product::where('uuid', $id)->first();


        DB::beginTransaction();
        try {
            if ($prodcut->image != "") {
                (File::exists('uploads/product/' . $prodcut->image) ? File::delete('uploads/product/' . $prodcut->image) : '');
            }
            $prodcut->delete();
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
            $search = $search;
            $search = " AND ((p.nama ILIKE '%$search%') OR (c.nama ILIKE '%$search%') OR (CAST(p.harga_beli AS text) ILIKE '%$search%') OR (CAST(p.harga_jual AS text) ILIKE '%$search%') OR (CAST(p.stok AS text) ILIKE '%$search%'))";
        }

        if (empty($filter)) {
            $filter = "";
        } else {
            $filter = $filter;
            $filter = " AND (p.category_id = '$filter')";
        }

        if ($auth->role_id != 1) {
            $user = " AND (p.user_id = '$auth->id')";
        } else {
            $user = "";
        }
        $query = DB::select("SELECT
            p.id,
            p.nama as p_nama,
            c.nama as c_nama,
            p.harga_beli as harga_beli,
            p.harga_jual as harga_jual,
            p.stok as stok,
            u.nama as u_nama
            FROM products p
            INNER JOIN users u ON u.id = p.user_id
            INNER JOIN categories c ON c.id = p.category_id
            Where p.id is not null
            $filter
            $user
            $search
        ");

        $data = array();
        foreach ($query as $key => $r) {
            $nestedData['nama'] = $r->p_nama;
            $nestedData['kategori'] = $r->c_nama;
            $nestedData['harga_beli'] = "Rp " . number_format($r->harga_beli, 0, '.', '.');
            $nestedData['harga_jual'] = "Rp " . number_format($r->harga_jual, 0, '.', '.');
            $nestedData['stok'] =  number_format($r->stok, 0, '.', '.');
            $data[] = $nestedData;
        }

        $data = json_decode(json_encode($data));

        $params = [
            'auth' => $auth,
            'data' => $data,
            'title' => 'Product'
        ];
        return view("product.export", $params);
    }
}
