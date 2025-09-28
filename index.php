<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_laundry";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (isset($_POST['save'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_jenis     = $_POST['id_jenis'];
    $harga        = $_POST['harga'];
    $jumlah       = $_POST['jumlah'];
    $total        = $harga * $jumlah;

    $tanggal_terima  = date("Y-m-d");
    $tanggal_selesai = date("Y-m-d", strtotime("+3 days"));

    mysqli_query($koneksi, "INSERT INTO laundry 
        (id_pelanggan, id_jenis, tanggal_terima, tanggal_selesai, harga, jumlah, total)
        VALUES ('$id_pelanggan','$id_jenis','$tanggal_terima','$tanggal_selesai','$harga','$jumlah','$total')");
    header("Location: index.php");
    exit;
}

if (isset($_POST['update'])) {
    $id           = $_POST['id_laundry'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_jenis     = $_POST['id_jenis'];
    $harga        = $_POST['harga'];
    $jumlah       = $_POST['jumlah'];
    $total        = $harga * $jumlah;

    mysqli_query($koneksi, "UPDATE laundry SET 
        id_pelanggan='$id_pelanggan',
        id_jenis='$id_jenis',
        harga='$harga',
        jumlah='$jumlah',
        total='$total'
        WHERE id_laundry='$id'");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM laundry WHERE id_laundry='$id'");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Sistem Informasi Laundry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            margin: 20px;
        }

        h2 {
            text-align: center;
            color: #000000;
        }

        .container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        table {
            width: 65%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.5);
        }

        table th,
        table td {
            border: 1px solid #000000;
            padding: 8px;
            text-align: center;
        }

        table th {
            background: #000000;
            color: white;
        }

        .form-box {
            width: 30%;
            background: white;
            padding: 20px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.5);
            border: 1px solid black;
        }

        .form-box label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .form-box select,
        .form-box input {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-save {
            background: #28a745;
            color: white;
        }

        .btn-update {
            background: #ffc107;
            color: black;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 5px 10px;  
        }

        .btn-edit {
            background: #004cff;
            color: white;
            padding: 5px 10px;
        }

        .btn:hover {
            opacity: 0.9;
        }
        input {
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <h2>Sistem Informasi Laundry</h2>

    <div class="container">

        <!-- TABEL DATA -->
        <table>
            <tr>
                <th>No</th>
                <th>Pelanggan</th>
                <th>Tgl Terima</th>
                <th>Tgl Selesai</th>
                <th>Jenis</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            $sql = mysqli_query($koneksi, "SELECT l.*, p.nama_pelanggan, j.nama_jenis 
            FROM laundry l 
            JOIN pelanggan p ON l.id_pelanggan=p.id_pelanggan 
            JOIN jenis_laundry j ON l.id_jenis=j.id_jenis");
            while ($data = mysqli_fetch_assoc($sql)) {
                echo "<tr>
                <td>$no</td>
                <td>{$data['nama_pelanggan']}</td>
                <td>{$data['tanggal_terima']}</td>
                <td>{$data['tanggal_selesai']}</td>
                <td>{$data['nama_jenis']}</td>
                <td>Rp " . number_format($data['harga'], 0, ',', '.') . "</td>
                <td>{$data['jumlah']}</td>
                <td>Rp " . number_format($data['total'], 0, ',', '.') . "</td>
                <td>
                    <button class='btn btn-edit' onclick=\"editData('{$data['id_laundry']}','{$data['id_pelanggan']}','{$data['id_jenis']}','{$data['harga']}','{$data['jumlah']}')\">Edit</button>
                    <a class='btn btn-delete' href='?delete={$data['id_laundry']}' onclick=\"return confirm('Yakin hapus?')\">Delete</a>
                </td>
            </tr>";
                $no++;
            }
            ?>
        </table>

        <!-- FORM INPUT -->
        <div class="form-box">
            <form method="post">
                <input type="hidden" name="id_laundry" id="id_laundry">

                <label>Pilih Pelanggan</label>
                <select name="id_pelanggan" id="id_pelanggan" required>
                    <option value="">--Pilih--</option>
                    <?php
                    $q1 = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                    while ($row = mysqli_fetch_assoc($q1)) {
                        echo "<option value='{$row['id_pelanggan']}'>{$row['nama_pelanggan']}</option>";
                    }
                    ?>
                </select>

                <label>Pilih Jenis Laundry</label>
                <select name="id_jenis" id="id_jenis" required onchange="setHarga()">
                    <option value="">--Pilih--</option>
                    <?php
                    $q2 = mysqli_query($koneksi, "SELECT * FROM jenis_laundry");
                    while ($row = mysqli_fetch_assoc($q2)) {
                        echo "<option value='{$row['id_jenis']}' data-harga='{$row['harga']}'>{$row['nama_jenis']}</option>";
                    }
                    ?>
                </select>


                <label>Harga</label>
                <input type="number" name="harga" id="harga" required readonly>

                <label>Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" required>

                <label>Total</label>
                <input type="text" id="totalValue" readonly>

                <button type="submit" name="save" class="btn btn-save">SAVE</button>
                <button type="submit" name="update" class="btn btn-update">UPDATE</button>
            </form>
        </div>
    </div>

    <script>
        function setHarga() {
            var jenisSelect = document.getElementById('id_jenis');
            if (!jenisSelect) return;
            var selectedOption = jenisSelect.options[jenisSelect.selectedIndex];
            var harga = selectedOption ? selectedOption.getAttribute('data-harga') : 0;
            document.getElementById('harga').value = harga || 0;
            updateTotal();
        }

        function editData(id, id_pelanggan, id_jenis, harga, jumlah) {
            document.getElementById('id_laundry').value = id;
            document.getElementById('id_pelanggan').value = id_pelanggan;
            document.getElementById('id_jenis').value = id_jenis;
            document.getElementById('harga').value = harga;
            document.getElementById('jumlah').value = jumlah;
            updateTotal();
        }

        document.getElementById('harga').addEventListener('input', updateTotal);
        document.getElementById('jumlah').addEventListener('input', updateTotal);

        function updateTotal() {
            var harga = parseInt(document.getElementById('harga').value) || 0;
            var jumlah = parseInt(document.getElementById('jumlah').value) || 0;
            var total = harga * jumlah;
            document.getElementById('totalValue').value = "Rp " + total.toLocaleString('id-ID');
        }
    </script>

</body>

</html>