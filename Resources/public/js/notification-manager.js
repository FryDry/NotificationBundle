
NotificationManager = function(options) {

    var $ = jQuery;

    this.notificationGetUrl = $('#frydry-notification-setting').attr('data-get-url');
    this.notificationCheckLoop = null;
    this.notificationCheckInterval = 5;
    this.autoStart = true;
    this.subscribers = [];

    for (key in options) {
        if (this.hasOwnProperty(key)) {
            this[key] = options[key];
        }
    }

    this.startCheckingForNotifications = function() {
        var that = this;
        this.checkForNotifications();
        this.notificationCheckLoop = setInterval(function() { return that.checkForNotifications()}, this.notificationCheckInterval * 1000)
    }

    this.stopCheckingForNotifications = function() {
        clearInterval(this.notificationCheckLoop);
    }

    this.checkForNotifications = function() {
        var url = this.notificationGetUrl;
        var that = this;
        $.ajax({
            url: url,
            dataType: 'json',
            success: function(response) {
                that.updateSubscribers(response)
            }
        });
    }

    this.updateSubscribers = function(data) {
        for (channel in data) {
            for (subscriber in this.subscribers) {
                if (this.subscribers[subscriber].channel == channel) {
                    if (this.subscribers[subscriber].count != data[channel].count) {
                        this.subscribers[subscriber].count = data[channel].count;
                        this.subscribers[subscriber].onUpdate();
                    }
                }
            }
        }
    }

    this.addUINotificationSubscriber = function(params) {
        if (params.channel !== undefined) {
            this.subscribers[params.channel] = new UINotificationSubscriber(this, params);
        }
    }

    if (this.autoStart) {
        this.startCheckingForNotifications();
    }
};

UINotificationSubscriber = function(notificationManager, params) {

    var $ = jQuery;
    var that = this;

    if (notificationManager === undefined || !(notificationManager instanceof NotificationManager)) {
        throw new Error('No notification manager instance passed to UINotificationSubscriber constructor');
    }

    this.notificationManager = notificationManager,
    this.UIelement = params.UIElement;
    this.channel = params.channel;

    this.updateListUrl = $('#frydry-notification-setting').length > 0 ? $('#frydry-notification-setting').attr('data-get-list-by-channel-url') : '/';
    this.updateListUrl = this.UIelement.attr('data-update-list-url') !== undefined ? this.UIelement.attr('data-update-list-url') : this.updateListUrl;
    this.updateListUrl = params.updateListUrl !== undefined ? params.updateListUrl : this.updateListUrl;


    this.onUpdate = function() {
        var callback = params.onUpdate || function(){};
        return callback(this.UIelement, { name : this.channel, count : this.count });
    }

    this.onClick = function(event) {
        var callback = params.onClick || function(){};
        this._onClick();
        return callback(event, this);
    };

    this._onClick = function() {
        $.ajax({
            url : this.updateListUrl,
            data : { channel: this.channel },
            success: function(data) {
                that.setListItems(data);
                that.createAndDispatchEvent('listItemsUpdated', that.list);
            }
        });
    }

    this.dispatchEvent = function(event) {
        event.target = this;
        if (this.listeners[event.type] instanceof Array){
            var listeners = this.listeners[event.type];
            for (var i=0, len=listeners.length; i < len; i++){
                listeners[i].call(this, event);
            }
        } else {
            this.listeners[event.type].call(this, event);
        }
    }

    this.createAndDispatchEvent = function(type, data) {
        event = new CustomEvent(type, data);
        event.initEvent(type, true, true);
        this.dispatchEvent(event);
    }

    this.addEventListener = function(type, listener) {
        if (this.listeners[type] === undefined) {
            this.listeners[type] = [];
        }
        this.listeners[type] = listener;
    }

    this.addEventListener('listItemsUpdated', function(event){
        var callback = params.onListUpdate || function(){};
        return callback(this.list);
    });

    $(this.UIelement).click(function(event) {
        return that.onClick(event);
    });
};

UINotificationSubscriber.prototype = {

    constructor: UINotificationSubscriber,

    notificationManager: null,
    UIelement: null,
    channel: 'default',
    count : null,
    list : [],
    listeners : {},

    onUpdate: function () {},
    onClick: function () {},

    getUIElement : function() { return this.UIelement; },
    setUIElement : function(element) { this.UIelement = element; return this; },

    getChannel : function() { return this.channel; },
    setChannel : function(channel) { this.channel = channel; return this; },

    getCount : function() { return this.count },
    setCount : function(count) { this.count = count; return this.count },

    setListItems : function(items) {
        this.list = items;
    }
};

//UINotificationSubscriber.prototype.constructor = UINotificationSubscriber;