@extends('template.app')
@section('content')
<div class="content p-4 flex-grow-1">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 col-xl-12 grid-margin stretch-card">
                    <div class="col-md-6 ">
                        <h3>{{ $title }}</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item" aria-current="page">{{ $title }} </li>
                                <li class="breadcrumb-item active" aria-current="page">Tambah Data </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="conatiner-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="/user/insert" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3 form-group">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                                <div class="mb-3 form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3 form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3 form-group">
                                    <label for="role_id" class="form-label">Kategori Pengguna</label>
                                    <select class="form-select" id="role_id" name="role_id" required>
                                        <option value="">Pilih Kategori Pengguna</option>
                                        @foreach($role as $r)
                                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 form-group">
                                    <label for="file" class="form-label">Gambar</label>
                                    <input type="file" class="form-control" name="profile">
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
</script>
@endsection