var widgets = [
    {
        type: 'analytics',

    }
];

new Vue({
    el: '#app',
    data: {
        navigationItems: [
            'aa', 'bb', 'cc'
        ]
    },
    components: {
        'modal': require('./components/modal.js')
    }
});