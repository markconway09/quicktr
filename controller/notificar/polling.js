function fetchData() {
        fetch('controller/notificar/poll.php')
            .then(response => response.json())
            .then(data => {
                // Check if there's a notification
                if (data.notification) {
                    showToast(data.notification);
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }
    function showNotification(message) {
        // Check for notification permission
        if (Notification.permission === 'granted') {
            new Notification('Update', {
                body: message,
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification('Update', {
                        body: message,
                    });
                }
            });
        }
    }
    function showToast(message) {
        document.getElementById('toastMessage').innerText = message;
        var toastEl = document.getElementById('errorToast');
        var toast = new bootstrap.Toast(toastEl, { autohide : false });
        toast.show();
    }
    // Poll the server every 5 seconds
    setInterval(fetchData, 5000);