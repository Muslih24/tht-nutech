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
                                <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
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
                            <form action="/product/update/{{ $product->uuid }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3 form-group">
                                            <label for="category_id" class="form-label">Kategori Produk</label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">Pilih Kategori Produk</option>
                                                @foreach($category as $c)
                                                <option value="{{ $c->id }}"
                                                    @if($product->category_id == $c->id) selected @endif>
                                                    {{ $c->nama }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-3 form-group">
                                            <label for="nama" class="form-label">Nama</label>
                                            <input type="text" class="form-control" id="nama" name="nama" value="{{ $product->nama }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3 form-group">
                                            <label for="harga_beli" class="form-label">Harga Beli</label>
                                            <input type="text" class="form-control" id="harga_beli" name="harga_beli" value="{{ number_format($product->harga_beli, 0, ',', '.') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3 form-group">
                                            <label for="harga_jual" class="form-label">Harga Jual</label>
                                            <input type="text" class="form-control" id="harga_jual" name="harga_jual" value="{{ number_format($product->harga_jual, 0, ',', '.') }}" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3 form-group">
                                            <label for="stok" class="form-label">Stok</label>
                                            <input type="text" class="form-control" id="stok" name="stok" value="{{ number_format($product->stok, 0, ',', '.') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                       <div class=" mb-3 form-group">
                                        <label for="gambar" class="form-label">Upload Gambar Produk</label>
                                        <div class="image-upload" id="image-upload">
                                            <input type="file" class="form-control" name="gambar" id="gambar" accept="image/png, image/jpg, image/jpeg" hidden>
                                            <div class="drop-zone" id="drop-zone">
                                                <p>Drag & Drop Gambar di sini atau klik untuk memilih file</p>
                                                @if($product->image)
                                                <img id="image-preview" src="{{ asset('uploads/product/'.$product->image) }}" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 100%; object-fit: contain; background-color:white; z-index:999">
                                                @else
                                                <img id="image-preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 100%; object-fit: contain; background-color:white; display:none;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
    $(document).ready(function() {

        function formatNumber(angka) {
            var number_string = angka.replace(/[^0-9,\.]/g, '');
            var split = number_string.split(',');
            var ribuan = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            var desimal = split[1] ? ',' + split[1] : '';
            return ribuan + desimal;
        }

        $('#harga_beli').on('input', function() {
            var hargaBeli = $(this).val().replace(/\./g, '').replace(',', '.');
            if (hargaBeli != '') {
                hargaBeli = parseFloat(hargaBeli);
                if (!isNaN(hargaBeli)) {
                    var hargaJual = Math.round(hargaBeli + (hargaBeli * 0.30));
                    $('#harga_jual').val(formatNumber(hargaJual.toString()));
                    $('#harga_beli').val(formatNumber(hargaBeli.toString()));
                }
            }
        });

        $('#stok').on('input', function() {
            var stok = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(formatNumber(stok));
        });

        const dropZone = $('#drop-zone');
        const fileInput = $('#gambar');
        const imagePreview = $('#image-preview');

        function previewImage(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.attr('src', e.target.result);
                imagePreview.show();
            };
            reader.readAsDataURL(file);
        }

        dropZone.on('dragover', function(e) {
            e.preventDefault();
            dropZone.addClass('drag-over');
        });

        dropZone.on('dragleave', function() {
            dropZone.removeClass('drag-over');
        });

        dropZone.on('drop', function(e) {
            e.preventDefault();
            dropZone.removeClass('drag-over');

            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    fileInput[0].files = e.originalEvent.dataTransfer.files;
                    previewImage(file);
                } else {
                    alert('Please drop a valid image file.');
                }
            }
        });

        dropZone.on('click', function() {
            fileInput.click();
        });

        fileInput.on('change', function() {
            const file = fileInput[0].files[0];
            if (file) {
                previewImage(file);
            }
        });
    });
</script>
@endsection