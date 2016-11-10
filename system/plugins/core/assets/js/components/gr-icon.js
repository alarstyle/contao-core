(function() {

    var Icon = {
        //template: '<svg xmlns="http://www.w3.org/2000/svg" class="rc-icon"><use xlink:href="/assets/icons.svg#icon-file-o" /></svg>',

        render: function(createElement) {
            return createElement(
                'svg',
                { class: 'gr-icon', attrs: { 'xmlns': 'http://www.w3.org/2000/svg' } },
                [
                    createElement(
                        'use',
                        { attrs: { 'xlink:href': '/assets/icons.svg#icon-' + this.name } }
                    )
                ]
            )
        },

        props: {
            name: String
        },

        created: function() {

        }

    };

    Vue.component('gr-icon', Icon);

}());