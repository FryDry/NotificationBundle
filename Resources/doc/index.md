# FryDryNotificationBundle #

The FryDry help you to manage in app notification of your Symfony2 application.
Please not that before installation you should have defined your Application's user class.
If you are using for example [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle), configure first your [User class](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/index.md#step-3-create-your-user-class)

1. [Installation](#installation)
2. [Bundle configuration](#bundle-configuration)
3. [Update your schema](#update-your-schema)
4. [Create notifiable objects](#create-notifiable-objects)
5. [Retrieve notification objects - javascript libraries](#retrieve-notification-objects)

## <a name="installation"></a>1. Installation ##
Composer installation is recommended.
Add the bundle to your composer.json

```php composer.phar require frydry/notificationbundle "dev-master"```

Then add the bundle to your AppKernel

```
<?php
// app/AppKernel.php

public function registerBundles()
{
	$bundles = array(
		// ...
		new FryDry\NotificationBundle\FryDryNotificationBundle()
	);
}

```

## <a name="bundle-configuration"></a>2. Bundle configuration ##

FryDryNotificationBundle requires at least that you provide your app User entity. Please note that your User entity class will be required to implement FryDryNotificationBundle's UserInterface, so:

#### 1. Add your user class in configuration ####

```
# app/config/config.yml

...
fry_dry_notification:
	...
	user_class: 'YourAppBundle\Entity\User'

```

#### 2. Declare your User class as a FryDryNotification UserInterface implementation ####

```
<?php
// src/YourAppBundle/Entity/User.php

use FryDry\NotificationBundle\Model\User\UserInterface

class User implements UserInterface {

	// your entity class code

}

```

At this point you have to provide an implementation for all UserInterface methods

```
<?php
// src/YourAppBundle/Entity/User.php

use FryDry\NotificationBundle\Model\User\UserInterface

class User implements UserInterface {

	// your entity class code

	...

	public function getId() {
		// your code
	}

	public function getName() {
		// your code
	}

	public function getProfileImage() {
		// your code
	}

}

```

Note: keep in mind that the getProfileImage method should return a string containing the path to user's profile image. You can return an empty string if you don't plan to print users' profile images thumbnail in notification lists.

## <a name="update-your-schema"></a>3. Update your schema ##
FryDryNotificationBundle already provide out of the box a Notification entity in order to automatically persist ntofication whenever a NotifiableObjectInterface (see next step) object instance is persisted.
Execute

`php app/console doctrine:schema:update --dump-sql`

or, if you use [DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle)

`php app/console doctrine:migrations:diff`

to inspect the changes that the bundle will bring to your database and

`php app/console doctrine:schema:update --force`

or, if you use DoctrineMigrationsBundle

`php app/console doctrine:migrations:migrate`

to apply those changes to your schema.

Remember that the Notification entity provided is just a base class that assures that everything in the bundle works fine but you can - at every moment - extend this class to create your custom Notification object. All you have to do in this case is to let FryDryNotificationBundle know of your custom entity in bundle configuration:

```

# app/config/config.yml

fry_dry_notification:
	notification_class: YourAppBundle\Entity\Notification

```

## <a name="create-notifiable-objects"></a>4. Create notifiable objects ##
FryDryNotificationBundle is based on the idea that when you are dealing with objects that have to be notified (by the object creator to the "recipient" of the object itself, e.g. a message, a comment on a post etc etc) the related notification objects will have two sets of properties

* Immutable properties - for example the ID and the class name of the object that generated the notification object, i.e. all the properties that are not intended to change in the future
* Dynamic properties - all those properties that could change during your application lifecycle (the redirect url when users must be redirected to when they click on a notification list item is a good example)

Keeping this in mind, when you want to declare an object as notifiable inside your application you will do this in two steps. Let's take for example a Message entity in your applcation, that you want to declare as a notifiable object in order to show an in-app notification to the message recipient when the message is sent.

```
# src/YourAppBundle/Entity/Message.php

class Message {

	protected $id;

	protected $text;

	protected $sender;

	protected $recipient;

	...

}
```

#### 1. Declare your class as a NotifiableObjectInterface implementation ####
First you will have to declare that your class implements the FryDry\NotificationBundle\Model\NotifiableObjectInterface (and also give an implementation of interface method, of course)


```
// src/YourAppBundle/Entity/Message.php

use FryDry\NotificationBundle\Model\NotifiableObjectInterface;

class Message implements NotifiableObjectInterface {

	protected $id;

	protected $text;

	protected $sender;

	protected $recipient;

	...

	// interface methods implementation

	public function getNotifier() {
		return $this->sender;
	}

	public function getNotificationRecipients() {
		return $this->recipient;
	}

}
``` 
Declaring your entity as an implementation of NotifiableObjectInterface will trigger the automatic creation of a Notification object whenever an object of this class is persisted.

#### 2. Configure dynamic properties of your notifiable object ####
Now you have to provide a configuration for the dynamic properties of your notifiable object.
So, assuming that 
* You have already created a `message_show` route in your application - in order to let notified user to read the message he/she has been notified of
* You have provided a translation for the `message_received` string
we will place in app/config/config.yml 

```

fry_dry_notification:
	
	...
	
	entities:
		message:
			class: YourAppBundle\Entity\Message
			channel: "message"
			redirect_router_path: message_show
			notification_message: message_received

```

As we said before, unlike the sender and the recipient of the notification, the channel (in which notifications generated by your Message entity will be grouped), the url (where user will be redirected when he/she clicks on the notification list item) and the translation string .
As we said before, unlike the sender and the recipient of the notification, other properties of your notification objects could be changed at any time, and all notification objects created before changing this configuration must follow the new configuration directives seamlessly.
These properties are:

* The notification channel (`channel`) in which notifications generated by your notifiable entity will be grouped
* The url (`redirect_router_path`) where user will be redirected when he/she clicks on the notification list item
* The message (`notification_message`) the user will see in the notifications list

You will see the advantage of this approach later on when we will introduce to you the FryDryNotificationBundle's javascript libraries.

## <a name="retrieve-notification-objects"></a>5. Retrieve notification objects - javascript libraries ##
So far we have configured our entities in order to generate automatically notification objects and, at this point, you should see those objects saved in your database.
In order to retrieve them FryDryNotificationBundle provide a little set of javascript libraries that you can include in your base layout in order to enable automatic server polling for new notifications and show them in your views.
All you have to do is to include FryDryNotificationBundle in your application's assetic configuration:

```
assetic:
	bundles: [ SomeBundle1, SomeBundle2, ..., FryDryNotificationBundle ]

```

Then include the javascript libraries in your view file

```
// app/Resources/views/base.html.twig - for example


...

{% javascripts
	'@FryDryNotificationBundle/Resources/public/js/notification-manager.js'
	output='js/compiled/frydry.main.js' %}

	<script type="text/javascript" src="{{ asset_url }}"></script>

{% endjavascripts %}

```

This will provide access to the NotificationManager javascript object that can help you manage the final notification process to the recipient (e.g. a badge on a navbar item).
You can also print this code by calling the twig function

```{{ notification_manager() }} ```

defined in FryDryNotificationBundle twig extension.
To get all things working you must also print another block with all the settings for the NotificationManager object.

``` {{ notification_settings() }} ```

that will print a div with id "frydry-notification-setting" containing a set of html data attributes with all the settings needed by the NotificationManager object.

Note: if you do not already include jquery in your page please notice that the NotificationManager object need the jquery library in order to work properly.
So, in that case, call the `notification_manager()` twig function passing the jquery parameter like this

```{{ notification_manager({ 'jquery' : true }) }} ```

#### The NotificationManager object ####
The NotificationManager object is meant to manage the show process for all incoming notification, grouping them into channels according to its own configuration and the configuration provided for FryDryNotificationBundle.
Consider, for example, the following configuration:

```
fry_dry_notification:
	
	...

	entities:
		message:
			class: YourAppBundle\Entity\Message
			channel: "message"
			redirect_router_path: message_show
			notification_message: message_received
		comment:
			class: YourAppBundle\Entity\Comment
			redirect_router_path: comment_show
			notification_message: comment_posted
	
```

First, notice that we have not provided a channel for comment entity. This means that all notifications related to this entity will be routed to the 'default' notification channel.
Imagine that we have in our base layout a classi navbar like the following:

```
...

<nav>
	<li id="profile">Profile</li>
	...
	<li id="messages">Messages</li>
	<li id="notifications">Notifications</li>
	...
<nav>

...
```

Our goal is to bind our navbar items to the notification system configured so far, so let's create a javascript file that will be included in our base layout (in order to receive notification in every page of our application)
and place the following code:

```
var notificationManager = new NotificationManager();

```

The notificationManager instance now will take in charge the task to check in a loop if there are notification for the currently logged user but, in order to do so, we need
to give to this object at least one UINotificationSubscriber.
In fact the UINotificationSubscriber instances can split the data collected by NotificationManger objects they are related to, applying modifications (e.g. show a badge icon with a number within it)
to the DOM elements which they are binded to.
In our example, we can now instantiate a UINotificationSubscriber to the `<li id="#notifications">...</li>` element like this:

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
        // do something with list object
        list.each(function(){
        	
        });
    }
};

```
