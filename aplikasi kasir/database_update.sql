-- SCRIPT UPDATE DATABASE
-- Jalankan query ini di phpMyAdmin untuk menambahkan fitur customer tracking

-- Tambahkan kolom customer_id ke tabel transaksi
-- (Jika belum ada)
ALTER TABLE transaksi ADD COLUMN customer_id INT AFTER user_id;

-- Tambahkan foreign key untuk customer_id
ALTER TABLE transaksi ADD FOREIGN KEY (customer_id) REFERENCES user(user_id);

-- Untuk data transaksi lama, set customer_id = user_id (diasumsikan yang membeli adalah petugasnya)
UPDATE transaksi SET customer_id = user_id WHERE customer_id IS NULL;
