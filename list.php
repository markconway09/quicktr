    <div class="container border border-secondary my-4 px-4 bg-dark rounded">
        <?php include 'views/ticket.php'; ?>
        <?php include 'views/list_ajax.php'; ?>
    </div>

    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>