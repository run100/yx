

// import Vue from 'vue/dist/vue.js'
import Vue from 'vue';
import App from './App.vue'
import VueRouter from 'vue-router';
import Index from './components/Index.vue';
import Register from './components/Register.vue';
import {AlertPlugin, ToastPlugin, BusPlugin} from 'vux';

Vue.use(AlertPlugin);
Vue.use(ToastPlugin);
Vue.use(VueRouter);
Vue.use(BusPlugin);


const routes = [
    { path: '/', component: Index},
    { path: '/register/:type', component: Register },
]

const router = new VueRouter({
    routes
});

new Vue({
    el: '#app',
    template: '<App />',
    router,
    components: {App},
})

