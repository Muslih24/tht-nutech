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
                <!-- <div class="col-md-3">
                    <select class="form-select" id="filter_category">
                        <option value="" selected>Semua</option>
                        @foreach($category as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                        @endforeach
                    </select>
                </div> -->
                <div class="col-md-3">
                    <button class="btn btn-success" onclick="exportData()" style="float:right">Export Data</button>
                </div>
                <div class="col-md-3">
                    <a href="/category/add" class="btn btn-danger" style="float: right;">Tambah Data</a>
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
        var filter_category = $('#filter_category').val();
        $('#getData').DataTable().destroy();
        getData(search, filter_category);
    })

    $('#filter_category').on('change', function() {
        var filter_category = $(this).val();
        var search = $('#search').val();
        $('#getData').DataTable().destroy();
        getData(search, filter_category);
    })

    function getData(search = null, filter_category = null) {
        $('#getData').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "searching": false,
            "responsive": true,
            "lengthChange": false,
            // "lengthMenu": [10, 20, 50, 100, 200, 500],
            "ajax": {
                "url": "/category/getData",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "dataType": "json",
                "data": {
                    "search": search,
                    "filter_category": filter_category
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
                [0, 'desc']
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
                    window.location = '/category/delete/' + uuid;
                }
            })
    }

    function exportData() {
        var search = $("#search").val();
        window.open("/category/export?&search=" + search + "", "_blank");
    }
</script>
@endsection