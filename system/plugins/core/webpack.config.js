'use strict';

module.exports = {
    entry: './assets/src/core.ts',
    output: {
        filename: './assets/js/core.js',
        library: 'Raccoon'
    },
    // resolve: {
    //     alias: {
    //         'vue': __dirname + '/assets/src/vendor/vue.js',
    //         'lodash': __dirname + '/assets/src/vendor/lodash.min.js'
    //     }
    // },
    module: {
        loaders: [
            {test: /\.tsx?$/, loader: "ts-loader"}
        ],
        preLoaders: [
            {test: /\.js$/, loader: "source-map-loader"}
        ]
    },
    externals: {
        "vue": "Vue",
        "axios": "axios"
    }
};
