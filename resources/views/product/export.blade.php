<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Produk.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h3>{{ $title }}</h3>

<table border="1" width="100%">
    <thead>
        <tr>
            <th width="10%">No</th>
            <th>Nama Produk</th>
            <th>Kategori Produk</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Stok</th>
        </tr>

    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($data as $key => $r) {
        ?>
            <tr>
                <td>{{ $no }}</td>
                <td>{{ $r->nama }}</td>
                <td>{{ $r->kategori }}</td>
                <td>{{ $r->harga_beli }}</td>
                <td>{{ $r->harga_jual }}</td>
                <td>{{ $r->stok }}</td>
            </tr>
        <?php
            $no++;
        }
        ?>
    </tbody>
</table>