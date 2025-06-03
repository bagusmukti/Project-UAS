<!-- modal_lapor.php -->
<div class="modal fade" id="modalLaporKeluhan" tabindex="-1" aria-labelledby="modalLaporLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form action="proses_pengaduan.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header mb-3"  style="background-color: rgb(0, 0, 255);">
          <h5 class="modal-title" id="modalLaporLabel" style="color: #fff;">Formulir Pengaduan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="color: #fff;"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="namaLengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="name" id="namaLengkap" placeholder="Masukkan Nama Lengkap Anda" required>
          </div>
          <div class="mb-3">
            <label for="emailUser" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="emailUser" placeholder="Masukkan Email Anda" required>
          </div>
          <div class="mb-3">
            <label for="isiLaporan" class="form-label">Isi Laporan</label>
            <textarea class="form-control" name="isilaporan" id="isiLaporan" rows="4" placeholder="Deskripsikan Laporan yang ingin anda adukan" required></textarea>
          </div>
          <div class="mb-3">
            <label for="fotoLaporan" class="form-label">Foto (opsional)</label>
            <input class="form-control" type="file" name="foto" id="fotoLaporan" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Kirim</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
