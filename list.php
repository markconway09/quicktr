    <div class="container border border-secondary my-4 px-4 bg-dark rounded">
        <?php include 'ticket.php'; ?>
        <?php include 'listado.php'; ?>
    </div>

    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        function updatePages(selectedValue) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "index.php?pag=list", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send("valuePag=" + encodeURIComponent(selectedValue));
        }

        function updateSession(selectedValue) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "index.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send("valueLocal=" + encodeURIComponent(selectedValue));
        }
    </script>