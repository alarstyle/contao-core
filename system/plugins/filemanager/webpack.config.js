'use strict';

module.exports = [

    {
        entry: {
            "file-manager": "./assets/src/components/file-manager.js"
        },
        output: {
            filename: "./assets/js/[name].js",
            library: "FileManager"
        }
    }

];
