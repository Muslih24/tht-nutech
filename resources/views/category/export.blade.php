<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Kategori Produk.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h3>{{ $title }}</h3>

<table border="1" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
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
            </tr>
        <?php
            $no++;
        }
        ?>
    </tbody>
</table>