var notificationManager = new NotificationManager();

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
console.log(list);
        // do something with list object
        for (item in list) {
            html = '<li>';
            html += '<a href="'+item.redirect_url+'">';

            html += '</a>';
        }
    }
};

notificationManager.addUINotificationSubscriber(defaultSubscriber);