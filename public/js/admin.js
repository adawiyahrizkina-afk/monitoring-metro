const search =
            document.getElementById('searchWebsite');

        search.addEventListener('keyup', function() {

            const keyword =
                this.value.toLowerCase();

            const rows =
                document.querySelectorAll(
                    '#websiteTable tr'
                );

            rows.forEach(function(row) {
                const text =
                    row.innerText.toLowerCase();
                if (text.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });