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
                                <li class="breadcrumb-item active" aria-current="page">{{ $title }} </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" id="search" class="form-control">
                </div>
                <div class="col-md-6">
                    <a href="/role/add" class="btn btn-danger" style="float: right;">Tambah Data</a>
                </div>
            </div>
        </div>
        <div class="conatiner-fluid">
            <div class="table-responsive">
                <table id="getData" class="table table-hover table-wbs no-border-wbs">
                    <thead class="table-primary" style="height: 4rem;">
                        <tr>
                            <th width="10%">No</th>
                            <th>Nama</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        getData();
    })

    $('#search').on('keyup', function() {
        var search = $(this).val();
        $('#getData').DataTable().destroy();
        getData(search);
    })

    function getData(search = null) {
        $('#getData').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "searching": false,
            "responsive": true,
            "lengthChange": false,
            // "lengthMenu": [10, 20, 50, 100, 200, 500],
            "ajax": {
                "url": "/role/getData",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "dataType": "json",
                "data": {
                    "search": search
                },
                "type": "POST"
            },
            "columns": [{
                    "data": "no",
                    className: 'text-center'
                },
                {
                    "data": "nama"
                },
                {
                    "data": "action",
                    className: 'text-center'
                },
            ],
            'order': [
                [0, 'asc']
            ],

        });
    }

    function deleteData(uuid) {
        Swal.fire({
                title: "Apakah anda yakin?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location = '/role/delete/' + uuid;
                }
            })
    }
</script>
@endsection