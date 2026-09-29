const search = document.getElementById("searchWebsite");

if (search) {
    const filterWebsites = function () {
        const keyword = search.value.trim().toLowerCase();
        const charts = document.querySelectorAll(".monitoring-chart");

        if (charts.length) {
            charts.forEach(function (chart) {
                chart.style.display = chart.innerText
                    .toLowerCase()
                    .includes(keyword)
                    ? ""
                    : "none";
            });
            return;
        }

        const rows = document.querySelectorAll("#websiteTable tr");

        rows.forEach(function (row) {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(keyword) ? "" : "none";
        });
    };

    search.addEventListener("input", filterWebsites);
    window.searchTable = filterWebsites;
}

const checkAllForm = document.getElementById("checkAllForm");
const checkAllButton = document.getElementById("checkAllButton");

if (checkAllForm && checkAllButton) {
    checkAllForm.addEventListener("submit", function () {
        checkAllButton.disabled = true;
        checkAllButton.textContent = "Sedang mengecek...";
    });
}
