'use strict';

module.exports = {
    entry: './assets/src/core.js',
    output: {
        filename: './assets/js/core.js'
    },
    resolve: {
        alias: {
            'vue': __dirname + '/assets/src/vendor/vue.js',
            'lodash': __dirname + '/assets/src/vendor/lodash.min.js'
        }
    }
    // module: {
    //     loaders: {
    //
    //     }
    // }
};
