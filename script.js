// Collection
$.ajaxPrefilter(function( options ) {
    options.url = "api" + options.url;
});

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

var TodoListCollection = Backbone.Collection.extend({
    url: '/index.php'
});

// Model
var TodoListModel = Backbone.Model.extend({
    urlRoot: '/index.php'
});

// View
var TodoLists = Backbone.View.extend({
    el: '.page',
    render: function(){
        var self = this;
        var todoListCollection = new TodoListCollection();
        todoListCollection.fetch({
            success: function(todoLists){
                var data = { lists : todoListCollection.models };
                var html = _.template($('#todolist-template').html());
                self.$el.html(html(data));
            }
        });
    }
});

var TodoListForm = Backbone.View.extend({
    el: '.page',
    render: function(options){
        var self = this;
        if(options && options.id) {
            self.todo = new TodoListModel({ id : options.id });
            self.todo.url = function() {
                return this.urlRoot + '/' + this.id + '?fakerestapi=' + this.id;
            };
            self.todo.fetch({
                success: function(todo){
                    var html = _.template($('#todolist-form-template').html());
                    self.$el.html( html({ list: todo }) );
                }
            });
        }
        else {
            var html = _.template($('#todolist-form-template').html());
            self.$el.html( html({ list: null }) );
        }
    },
    events: {
        'submit .todolist-form': 'saveTodo',
        'click .delete': 'deleteTodo'
    },
    saveTodo: function(event){
        event.preventDefault();
        var form = event.currentTarget;

        var todo = new TodoListModel();
        todo.save($(form).serializeObject(), {
            // dataType: "text",
            success: function(){
                router.navigate('', { trigger: true });
            }
        });
    },
    deleteTodo: function(){
        this.todo.destroy({
            success: function(){
                router.navigate('', { trigger: true });
            }
        });
    }
});

// Router
var Router = Backbone.Router.extend({
    routes: {
        '': 'home',
        'new': 'new',
        'edit/:id': 'update'
    }
});

var router = new Router();
var todoLists = new TodoLists();
var todoListForm = new TodoListForm();

router.on('route:home', function(){
    todoLists.render();
});
router.on('route:new', function(){
    todoListForm.render();
});
router.on('route:update', function(id){
    todoListForm.render({ id: id });
});

Backbone.history.start();
