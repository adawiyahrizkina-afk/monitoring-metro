const search = document.getElementById("searchWebsite");

if (search) {
    search.addEventListener("keyup", function () {
        const keyword = this.value.toLowerCase();

        const rows = document.querySelectorAll("#websiteTable tr");

        rows.forEach(function (row) {
            const text = row.innerText.toLowerCase();
            if (text.includes(keyword)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
}

const checkAllForm = document.getElementById("checkAllForm");
const checkAllButton = document.getElementById("checkAllButton");

if (checkAllForm && checkAllButton) {
    checkAllForm.addEventListener("submit", function () {
        checkAllButton.disabled = true;
        checkAllButton.textContent = "Sedang mengecek...";
    });
}
