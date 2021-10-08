/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);

import Vue from "vue";
import ElementUI from "element-ui";

import App from './App.vue';

Vue.use(ElementUI);
const bus = new Vue({});

window.Vue.prototype.$routeLaravel = function(nameRoute = "", params = {}) {
    if (
      typeof window.Laravel.routes === "undefined" ||
      window.Laravel.routes === null
    ) {
      console.error("Routes undefined in app vue");
      return;
    } else {
      let item = window.Laravel.routes.find(item => item.name === nameRoute);
  
      if (typeof item === "undefined" || item.uri === null) {
        console.error("Not route exist");
        return;
      }
  
      var url = item.uri;
  
      for (var indice in params) {
        let reg = new RegExp("{" + indice + "}", "g");
        url = url.replace(reg, params[indice]);
      }
  
      return url;
    }
  };

Object.defineProperty(Vue.prototype, "$bus", {
    get() {
      return this.$root.bus;
    }
  });

const app = new Vue({
    el: '#app',
    render: h => h(App),
    data: {
        bus: bus
    },
    created() {
        console.log("App was mounted");
    }
});
