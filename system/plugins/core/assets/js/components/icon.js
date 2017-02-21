(function() {

    var Icon = {
        render: function(createElement) {
            return createElement(
                'svg',
                { class: 'icon icon--' + this.type, attrs: { 'xmlns': 'http://www.w3.org/2000/svg' } },
                [
                    createElement(
                        'use',
                        { attrs: { 'xlink:href': '/assets/icons.svg#icon-' + this.type } }
                    )
                ]
            )
        },
        props: {
            type: String
        }
    };

    Vue.component('icon', Icon);

}());