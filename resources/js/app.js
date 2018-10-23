
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
// Vue.config.devtools = false;
require('./bootstrap');

window.Vue = require('vue');
// routes component*************************************************
import router from './routes'
// end routes*************************************************



// moment js part*************************************************
import moment from 'moment';
// end moment.js*************************************************

// vfrom part validation*************************************************
import { Form, HasError, AlertError } from 'vform'
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)
window.Form=Form;
// end vform
Vue.config.productionTip = false

//sweetalerts
import swal from 'sweetalert2'
window.swal=swal;
const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
window.toast=toast;
//end sweetalert

// progressbar *************************************************
import VueProgressBar from 'vue-progressbar'

Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '2px'
})
// end progressbar*************************************************

window.fire=new Vue();



import gate from './gate';
Vue.prototype.$gate= new gate(window.user);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('home', require('./components/Home.vue'));
Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue')
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue')
);
Vue.component(
    'not-found',
    require('./components/notfound')
);

Vue.component('pagination', require('laravel-vue-pagination'));


const app = new Vue({
    el: '#app',
    router,
});



//filters*************************************************
Vue.filter('firstup',function (text) {
    return text.charAt(0).toUpperCase()+text.slice(1);
});

Vue.filter('mydate',function (created) {
    return moment(created).format('MMMM Do YYYY');
});