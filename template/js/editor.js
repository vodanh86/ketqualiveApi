var LED = window.LED || {};

LED = {
    page: {
        setup: function(){

        },
        create: function(){},
        rename: function(){},
        save: function(){},
        preview: function(){},
        duplicate: function(){},
        remove: function(){}
    },
    toolbar: {
        setup: function(){},
        show: function(section){}
    },
    block: {
        setup: function(){},
        add: function(){},
        rename: function(){},
        duplicate: function(){},
        move: function(){},
        remove: function(){}
    },
    group: {
        setup: function(){},
        add: function(){},
        rename: function(){},
        duplicate: function(){},
        move: function(){},
        resize: function(){},
        remove: function(){}
    },
    element: {
        setup: function(){},
        add: function(){},
        rename: function(){},
        duplicate: function(){},
        move: function(){},
        resize: function(){},
        remove: function(){}
    },
    library: {
        show: function(){},
        close: function(){}
    }
};

LED.page.setup();
LED.toolbar.setup();
LED.block.setup();
LED.group.setup();
LED.element.setup();