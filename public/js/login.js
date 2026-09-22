function pindah() {
    const berpindah = document.getElementById("bagian-satu");
    // Cek apakah kotak sudah dalam posisi maju atau belum
    if (!berpindah.classList.contains('animasi-jalan')) {
        // JIKA BELUM MAJU: Jalankan animasi maju
        berpindah.classList.remove('animasi-kembali');
        berpindah.classList.add('animasi-jalan');
            // document.getElementById("bagian-satu").style.display = "none"; // Sembunyikan bagian satu
    } else {
        // JIKA SUDAH MAJU: Jalankan animasi mundur
        berpindah.classList.remove('animasi-jalan');
        berpindah.classList.add('animasi-kembali');
        // document.getElementById("bagian-satu").style.display = "flex"; // Tampilkan bagian satu
        // document.getElementById("bagian-dua").style.display = "none"; // Sembunyikan bagian dua
    }
}