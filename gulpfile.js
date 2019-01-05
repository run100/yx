const elixir = require('laravel-elixir');

require('laravel-elixir-vue');

elixir(mix => {
    // mix.webpackConfig({
    //     plugins: ['vux-ui']
    // });
    mix.webpack('main.js');
});