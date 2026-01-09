
<div class="field">
    <div class=""><span class="bs-messages-title">Messages:</span> <span id="notification" class="bs-notification">&nbsp;</span></div>
</div>

<script type="text/javascript">
    /**
     * Output a notification
     */
    const NOTIFICATION_TIMEOUT = 3500;

    function showNotification(message)
    {
        let notification = $('#notification');
        if (undefined != notification) {
            notification.html(message).show();
            notification.delay(NOTIFICATION_TIMEOUT).fadeOut();
        } else {
            alert("Could not find #notification element");
        }
    }
</script>
