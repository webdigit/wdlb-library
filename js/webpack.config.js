const path = require('path')

const NodePolyfillPlugin = require('node-polyfill-webpack-plugin');

module.exports = {
    plugins: [
        new NodePolyfillPlugin()
    ],
    entry: {
        main: path.resolve(__dirname, './library.js'),
        admin: path.resolve(__dirname, './admin.js'),
        tinymceNewContent: path.resolve(__dirname, './wdlb-insertContent.js')
    },
    module: {
        rules: [
            {
                test: /\.(js)$/,
                exclude: /node_modules/,
                use: ['babel-loader']
            }
        ]
    },
    output: {
        path: path.resolve(__dirname, './dist'),
        filename: 'wdlb.[name].bundle.js'
    },
    experiments: {
        topLevelAwait: true
    },
    resolve: {
        fallback: {
            fs: false
        }
    }
}