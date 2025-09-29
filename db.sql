CREATE DATABASE db_laundry;
USE db_laundry;

CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100)
);

CREATE TABLE jenis_laundry (
    id_jenis INT AUTO_INCREMENT PRIMARY KEY,
    nama_jenis VARCHAR(100),
    harga INT
);

CREATE TABLE laundry (
    id_laundry INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT,
    id_jenis INT,
    tanggal_terima DATE,
    tanggal_selesai DATE,
    harga INT,
    jumlah INT,
    total INT,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_jenis) REFERENCES jenis_laundry(id_jenis)
);

INSERT INTO pelanggan (nama_pelanggan) VALUES ('Andi'), ('Budi'), ('Citra');
INSERT INTO jenis_laundry (nama_jenis, harga) VALUES 
('Jaket', 6000),
('Kaos', 8000),
('Selimut', 4000);