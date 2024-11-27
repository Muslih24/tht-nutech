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
                                <li class="breadcrumb-item active" aria-current="page">Detail Data </li>
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
                            @php
                            $image = $user->image ? asset('uploads/profile/' . $user->image ) : asset('assets/images/no_image.png' ); ;
                            @endphp
                            <img src="{{  $image }}" class="img-thumbnail mb-3 rounded-circle" width="200px">
                            <div class="mb-3 form-group">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="{{$user->nama}}" disabled>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}" disabled>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="role_id" class="form-label">Kategori Pengguna</label>
                                <select class="form-select" id="role_id" name="role_id" disabled>
                                    <option value="">Pilih Kategori Pengguna</option>
                                    @foreach($role as $r)
                                    <option value="{{$r->id}}" @if($r->id == $user->role_id) selected @endif>{{$r->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <a href="/profile/edit/{{$user->uuid}}" class="text-decoration-none">
                                <button class="btn btn-primary">Edit Profile</button>
                            </a>
                            <a href="javascript:void(0)" onclick="deleteData('{{$user->uuid}}')" class="btn btn-danger">Hapus Akun</a>

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
    function deleteData(uuid) {
        console.log(uuid);
        Swal.fire({
                title: "Apakah anda yakin ingin menghapus akun?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location = '/profile/delete/' + uuid;
                }
            })
    }
</script>
@endsection