var notificationManager = new NotificationManager({
    notificationGetUrl : $('#frydry-notification-setting').attr('data-get-url'),
    notificationCheckInterval : 15
});

var defaultSubscriber = {
    UIElement: $('#notifications'),
    channel: 'default',
    onUpdate : function(ui, channel) {
        ui.children('.badge').html(channel.count);
    },
    onClick: function(event, subscriber) {
        event.preventDefault();
    },
    onListUpdate: function(list) {
        // do something with list object
    }
};

notificationManager.addUINotificationSubscriber(defaultSubscriber);