const statusText = document.getElementById('realtimeStatus');
    const checkForm = document.getElementById('realtimeCheckForm');
    const checkButton = document.getElementById('realtimeCheckButton');
    const performanceHistory = {};
    let isRefreshing = false;

    function formatSeconds(milliseconds) {
        return milliseconds === null ? '-' : `${(milliseconds / 1000).toFixed(2).replace('.', ',')} detik`;
    }

    function calculateScore(website) {
        if (website.status === 'Offline' || website.response_time === null) return 0;
        return Math.max(0, Math.min(100, Math.round(100 - (website.response_time / 50))));
    }

    function formatMonitoringTime(value) {
        if (!value) return '--:--';
        const match = value.match(/(?:T|\s)(\d{2}:\d{2})/);
        return match ? match[1] : '--:--';
    }

    function drawWebsiteChart(websiteId) {
        const canvas = document.querySelector(`[data-chart-website-id="${websiteId}"] canvas`);
        if (!canvas) return;
        const chartContext = canvas.getContext('2d');
        const width = canvas.clientWidth || 500;
        const height = 210;
        const deviceRatio = window.devicePixelRatio || 1;
        canvas.width = width * deviceRatio;
        canvas.height = height * deviceRatio;
        chartContext.setTransform(deviceRatio, 0, 0, deviceRatio, 0, 0);
        chartContext.clearRect(0, 0, width, height);

        chartContext.strokeStyle = '#d9e5df';
        chartContext.fillStyle = '#55708c';
        chartContext.font = '12px Arial';

        [0, 25, 50, 75, 100].forEach(function(value) {
            const y = height - 38 - (value / 100) * (height - 58);
            chartContext.beginPath();
            chartContext.moveTo(42, y);
            chartContext.lineTo(width - 15, y);
            chartContext.stroke();
            chartContext.fillText(value, 12, y + 4);
        });

        const history = performanceHistory[websiteId] || [];
        if (!history.length) {
            chartContext.fillText('Belum ada data monitoring.', 55, height / 2);
            return;
        }

        const plotWidth = Math.max(width - 70, 1);
        const step = history.length === 1 ? 0 : plotWidth / (history.length - 1);
        chartContext.strokeStyle = '#168c68';
        chartContext.lineWidth = 3;
        chartContext.beginPath();

        const points = history.map(function(entry, index) {
            const x = 45 + (step * index);
            const y = height - 38 - (entry.score / 100) * (height - 58);
            return {
                x,
                y,
                score: entry.score,
                status: entry.status,
                time: entry.time
            };
        });

        chartContext.beginPath();
        points.forEach(function(point, index) {
            index === 0 ?
                chartContext.moveTo(point.x, point.y) :
                chartContext.lineTo(point.x, point.y);
        });
        chartContext.stroke();

        points.forEach(function(point, index) {
            const x = point.x;
            const y = point.y;
            chartContext.fillStyle = point.status === 'Offline' ? '#c83b3b' :
                point.status === 'Warning' ? '#d47b16' : '#168c68';
            chartContext.beginPath();
            chartContext.arc(x, y, 5, 0, Math.PI * 2);
            chartContext.fill();

            if (index % Math.max(1, Math.ceil(points.length / 6)) === 0 || index === points.length - 1) {
                chartContext.fillStyle = '#55708c';
                chartContext.textAlign = 'center';
                chartContext.fillText(point.time, x, height - 8);
            }
        });
        chartContext.textAlign = 'start';
    }

    function updateCharts(websites, checkedAt) {
        websites.forEach(function(website) {
            if (!performanceHistory[website.id]) performanceHistory[website.id] = [];
            performanceHistory[website.id].push({
                score: calculateScore(website),
                status: website.status,
                time: formatMonitoringTime(website.last_checked_at || checkedAt)
            });
            performanceHistory[website.id] = performanceHistory[website.id].slice(-12);
            const chartCard = document.querySelector(`[data-chart-website-id="${website.id}"]`);
            if (chartCard) {
                chartCard.querySelector('[data-field="score"]').textContent = `${calculateScore(website)} / 100`;
                updateChartEvents(chartCard, performanceHistory[website.id]);
            }
            drawWebsiteChart(website.id);
        });
    }

    function updateChartEvents(chartCard, history) {
        const eventList = chartCard.querySelector('[data-field="events"]');
        const events = history.filter(function(entry) {
            return entry.status === 'Warning' || entry.status === 'Offline';
        }).slice().reverse();

        eventList.replaceChildren();
        if (!events.length) {
            eventList.textContent = 'Tidak ada gangguan pada pengecekan terakhir.';
            return;
        }

        events.forEach(function(entry) {
            const event = document.createElement('span');
            event.className = entry.status === 'Offline' ? 'chart-event is-offline' : 'chart-event is-warning';
            event.textContent = `${entry.status === 'Offline' ? 'DOWN' : 'LAMBAT'} ${entry.time}`;
            eventList.appendChild(event);
        });
    }

    function updateTable(websites) {
        const statusClasses = {
            Online: 'status-online',
            Offline: 'status-offline',
            Warning: 'status-warning',
            'Belum Dicek': 'status-unchecked'
        };

        websites.forEach(function(website) {
            const row = document.querySelector(`[data-website-id="${website.id}"]`);
            if (!row) return;
            const statusClass = statusClasses[website.status] || 'status-unchecked';
            row.querySelector('[data-field="status"]').innerHTML = `<span class="${statusClass}">● ${website.status.toUpperCase()}</span>`;
            row.querySelector('[data-field="response"]').textContent = formatSeconds(website.response_time);
        });
    }

    async function refreshMonitoring() {
        if (isRefreshing) return;
        isRefreshing = true;
        checkButton.disabled = true;
        statusText.textContent = 'Sedang memperbarui hasil monitoring...';

        try {
            const response = await fetch(realtimeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) throw new Error('Gagal mengambil data monitoring.');
            const data = await response.json();
            updateTable(data.websites);
            updateCharts(data.websites, data.checked_at);
            statusText.textContent = `Diperbarui ${data.checked_at}. Pembaruan otomatis setiap 30 detik.`;
        } catch (error) {
            statusText.textContent = error.message;
        } finally {
            checkButton.disabled = false;
            isRefreshing = false;
        }
    }

    checkForm.addEventListener('submit', function(event) {
        event.preventDefault();
        refreshMonitoring();
    });

    initialWebsites.forEach(function(website) {
        performanceHistory[website.id] = website.last_checked_at ? [{
            score: calculateScore(website),
            status: website.status,
            time: formatMonitoringTime(website.last_checked_at)
        }] : [];
        const chartCard = document.querySelector(`[data-chart-website-id="${website.id}"]`);
        if (chartCard) {
            chartCard.querySelector('[data-field="score"]').textContent = `${calculateScore(website)} / 100`;
            updateChartEvents(chartCard, performanceHistory[website.id]);
        }
        drawWebsiteChart(website.id);
    });
    refreshMonitoring();
    setInterval(refreshMonitoring, 30000);