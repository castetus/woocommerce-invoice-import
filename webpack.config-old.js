const path = require('path');

module.exports = {
  entry: "./src/index.js", // webpack entry point. Module to start building dependency graph
  output: {
    path: path.resolve(__dirname, 'dist'), // Folder to store generated bundle
    filename: 'bundle.js',  // Name of generated bundle after build
    publicPath: '/' // public URL of the output directory when referenced in a browser
  },
   module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          // use: {
          //   loader: 'babel-loader'
          // }
        },
        {
         test: /\.vue$/,
         loader: 'vue-loader'
       },
       {
        test: /\.s(c|a)ss$/,
        use: [
          'vue-style-loader',
          'css-loader',
          {
            loader: 'sass-loader',
            // Requires sass-loader@^7.0.0
            options: {
              implementation: require('sass'),
              fiber: require('fibers'),
              indentedSyntax: true // optional
            },
            // Requires sass-loader@^8.0.0
            options: {
              implementation: require('sass'),
              sassOptions: {
                fiber: require('fibers'),
                indentedSyntax: true // optional
              },
            },
          },
        ],
      },
      ]
   },
//    plugins: [  // Array of plugins to apply to build chunk
//     new HtmlWebpackPlugin({
//         template: __dirname + "/src/public/index.html",
//         inject: 'body'
//     })
// ],
   resolve: {
      alias: {
        'vue$': 'vue/dist/vue.esm.js'
      },
      extensions: ['*', '.js', '.vue', '.json']
    },
}