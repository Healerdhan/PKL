<?php

namespace App\Helpers;

class Message
{
  public const internalServerError = "Gagal! Terjadi kesalahan pada server";
  public const methodNotAllowed = "Gagal! Metode tidak diizinkan";
  public const forbidden = "Gagal! Anda tidak memiliki akses";
  public const unauthorized = "Gagal! Akun tidak terautentikasi";
  public const conflict = "Gagal! Data sudah ada";
  public const tooManyAttempts = "Gagal! Terlalu banyak percobaan";

  public const success = "Berhasil!";
  public const error = "Gagal!";

  public const successDelete = "Berhasil menghapus data";
  public const successUpdate = "Berhasil mengubah data";
  public const successCreate = "Berhasil menambahkan data";
  public const successGet = "Berhasil mendapatkan data";
  public const successLogin = "Berhasil Login";
  public const successDeleteFile = "Berhasil menghapus file";
  public const successLogout = "Berhasil Logout";

  public const errorDelete = "Gagal menghapus data";
  public const errorUpdate = "Gagal mengubah data";
  public const errorCreate = "Gagal menambahkan data";
  public const errorGet = "Gagal mendapatkan data";

  public const notFound = "Data tidak ditemukan";
}
