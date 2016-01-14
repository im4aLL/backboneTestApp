var Router = Backbone.Router.extend({
    routes: {
        '': 'home',
        'test': 'test',
        'page/:page': 'page'
    },

    test: function(){
        console.log('test routing!')
    },

    page: function(page){
        console.log('You are in page: ' + page);
    }
});


var router = new Router();
router.on('route:home', function(){
    console.log('testing backbone router!');
});

Backbone.history.start();
