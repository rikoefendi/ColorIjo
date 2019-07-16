import Vue from 'vue'
import VueToast from 'vue-toast-notification';
import {EventBus} from './event-bus.js'
Vue.component('media-manager', require('./MediaManager.vue').default)
Vue.component('media-featured', require('./Featured.vue').default)

Vue.use(VueToast, {position: 'top-right'});
EventBus.$on('choosedImage', image => {
    let chooseImage = document.getElementById('choosedImage');
    choosedImage.value = JSON.stringify(image)
})
