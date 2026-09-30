const search = document.getElementById("searchWebsite");

const monitoringTable = document.getElementById("monitoringTable");

if (search && monitoringTable) {
    const rows = Array.from(
        monitoringTable.querySelectorAll("tbody tr[data-website-id]"),
    );
    const filters = Array.from(
        document.querySelectorAll("[data-status-filter]"),
    );
    const pageSizeSelect = document.getElementById("pageSize");
    const rangeLabel = document.getElementById("monitoringRange");
    const currentPageLabel = document.getElementById("currentPage");
    const previousButton = document.getElementById("previousPage");
    const nextButton = document.getElementById("nextPage");
    const noResults = document.getElementById("noMonitoringResults");
    const counts = {
        all: document.getElementById("countAll"),
        Offline: document.getElementById("countOffline"),
        Warning: document.getElementById("countWarning"),
        Online: document.getElementById("countOnline"),
        "Belum Dicek": document.getElementById("countUnchecked"),
    };
    let selectedStatus = "all";
    let currentPage = 1;

    function renderMonitoringList() {
        const keyword = search.value.trim().toLocaleLowerCase("id-ID");
        const pageSize = Number(pageSizeSelect.value);
        const matchingRows = rows.filter(function (row) {
            const matchesStatus =
                selectedStatus === "all" ||
                row.dataset.status === selectedStatus;
            const matchesSearch = row.textContent
                .toLocaleLowerCase("id-ID")
                .includes(keyword);
            return matchesStatus && matchesSearch;
        });
        const pageCount = Math.max(
            1,
            Math.ceil(matchingRows.length / pageSize),
        );
        currentPage = Math.min(currentPage, pageCount);
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, matchingRows.length);
        const visibleRows = new Set(matchingRows.slice(startIndex, endIndex));

        rows.forEach(function (row) {
            row.hidden = !visibleRows.has(row);
        });

        Object.keys(counts).forEach(function (status) {
            const count =
                status === "all"
                    ? rows.length
                    : rows.filter(function (row) {
                          return row.dataset.status === status;
                      }).length;
            counts[status].textContent = count;
        });

        noResults.hidden = rows.length === 0 || matchingRows.length > 0;
        rangeLabel.textContent = matchingRows.length
            ? `Menampilkan ${startIndex + 1}-${endIndex} dari ${matchingRows.length} website`
            : `${rows.length ? "0 website ditemukan" : "Belum ada website"}`;
        currentPageLabel.textContent = `${currentPage} / ${pageCount}`;
        previousButton.disabled = currentPage <= 1;
        nextButton.disabled = currentPage >= pageCount;
    }

    search.addEventListener("input", function () {
        currentPage = 1;
        renderMonitoringList();
    });
    pageSizeSelect.addEventListener("change", function () {
        currentPage = 1;
        renderMonitoringList();
    });
    filters.forEach(function (filter) {
        filter.addEventListener("click", function () {
            selectedStatus = filter.dataset.statusFilter;
            currentPage = 1;
            filters.forEach(function (button) {
                const isActive = button === filter;
                button.classList.toggle("is-active", isActive);
                button.setAttribute("aria-pressed", String(isActive));
            });
            renderMonitoringList();
        });
    });
    previousButton.addEventListener("click", function () {
        currentPage -= 1;
        renderMonitoringList();
    });
    nextButton.addEventListener("click", function () {
        currentPage += 1;
        renderMonitoringList();
    });

    window.refreshMonitoringList = renderMonitoringList;
    renderMonitoringList();
} else if (search) {
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
