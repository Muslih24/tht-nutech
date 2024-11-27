<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=User.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h3>{{ $title }}</h3>

<table border="1" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Kategori Pengguna</th>
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
                <td>{{ $r->email }}</td>
                <td>{{ $r->role }}</td>
            </tr>
        <?php
            $no++;
        }
        ?>
    </tbody>
</table>