var notificationManager = new NotificationManager({
    interval: 30
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
        $.each(list, function(key, item){
            //...
        });
    }
};

notificationManager.addUINotificationSubscriber(defaultSubscriber);