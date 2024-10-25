function fetchData() {
        fetch('notificar/poll.php')
            .then(response => response.json())
            .then(data => {
                // Check if there's a notification
                if (data.notification) {
                    showNotification(data.notification);
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
    // Poll the server every 5 seconds
    setInterval(fetchData, 5000);