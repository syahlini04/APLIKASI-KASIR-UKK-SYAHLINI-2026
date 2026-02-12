<?php
// Proses registrasi publik sudah ditutup
// Semua penambahan pengguna harus melalui admin
session_start();
header("Location: login.php?msg=registrasi-ditutup");
exit;

