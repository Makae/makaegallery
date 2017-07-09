var events = {
    event_listeners : {},
    addListener : function(event, callback) {
        if(typeof this.event_listeners[event] == 'undefined')
            this.event_listeners[event] = [];
        this.event_listeners[event].push(callback);
    },

    removeListener : function(event, callback) {
        if(typeof this.event_listeners[event] == 'undefined')
            return;

        for(var i in this.event_listeners[event])
            if(this.event_listeners[event][i] === callback)
                return this.event_listeners[event].splice(i, 1);
    },

    trigger : function(event, data) {
        if(typeof this.event_listeners[event] == 'undefined')
            return;
        for(var i in this.event_listeners[event]) {
            if(!this.event_listeners[event].hasOwnProperty(i))
                continue;

            this.event_listeners[event][i]({e: event, data: data});
        }
    }
};