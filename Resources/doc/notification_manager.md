# The NotificationManager javascript object #

The NotificationManager javascript object helps you manage notification to the notifications' recipients on your application pages.
It can be instantiated with no parameters if you include the DOM element (using the `{{ notification_manager_settings() }}` twig function) with all configuration settings
or, if you prefer, you can pass your custom parameters to the constructor:

```

var notificationManager = new NotificationManager({
	url: '/your/custom/url',
	interval: 30,
	...
});

```

### Properties ###

* **url**: this is the url that NotificationManager will call in a loop to check for new notifications
* **interval**: this is the interval - in seconds - that will be used for the loop
* **autoStart**: this property will tell to the NotificationManager if it has to start checking for new notifications automatically when it is instantiated
* **subscribers**: this property contains an array of all subscribers added to the NotificationManager object

### Methods ###

* **startCheckingForNotifications()** start checking for new notifications in a loop (according to the NotificationManager.interval property value)
* **stopCheckingForNotifications()** stop the check for new notifications loop
* **checkForNotifications()** check for new notifications (just once, no loop)
* **addUINotificationSubscriber(options)** add a new UINotificationSubscriber object to the NotificationManager building it from passed *options* parameter.
For example:

```
var defaultSubscriber = {
    UIElement: $('#notifications'),
    channel: 'default',
    onUpdate : function(ui, channel) {
        // update the UI, for example
        ui.children('.badge').html(channel.count);
    },
    onClick: function(event, subscriber) {
    	// do something when element is clicked, for example show a popover that will contain the notification list for the default channel
        event.preventDefault();
        $('#default-notification-popover').show();
    },
    onListUpdate: function(list) {
        // do something with list objects
        $.each(function(index, item){
        	// ...
        });
    }
};

notificationManager.addUINotificationSubscriber(defaultSubscriber);
```