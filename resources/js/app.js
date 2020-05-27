require('./bootstrap');
import VueNativeSock from 'vue-native-websocket'

if($("#app").length) {
  window.Vue = require('vue');

  const files = require.context('./', true, /\.vue$/i)
  files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

  Vue.use(VueNativeSock, "wss://ws.katakuna.cc", { format: 'json' });

  const app = new Vue({
      el: '#app',
  });
}
