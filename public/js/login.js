function pindah() {
    const berpindah = document.getElementById("head");
    // Cek apakah kotak sudah dalam posisi maju atau belum
    if (!berpindah.classList.contains("animasi-jalan")) {
        // JIKA BELUM MAJU: Jalankan animasi maju
        berpindah.classList.remove("animasi-kembali");
        berpindah.classList.add("animasi-jalan");
    } else {
        // JIKA SUDAH MAJU: Jalankan animasi mundur
        berpindah.classList.remove("animasi-jalan");
        berpindah.classList.add("animasi-kembali");
    }
}
