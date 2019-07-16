import Vue from 'vue'
import VueToast from 'vue-toast-notification';
import {EventBus} from '../../vendor/colorijo/media-manager/resources/assets/js/event-bus.js'
Vue.component('media-manager', require('../../vendor/colorijo/media-manager/resources/assets/js/MediaManager.vue').default)
Vue.component('media-featured', require('../../vendor/colorijo/media-manager/resources/assets/js/Featured.vue').default)

Vue.use(VueToast, {position: 'top-right'});
EventBus.$on('choosedImage', image => {
    let chooseImage = document.getElementById('choosedImage');
    choosedImage.value = JSON.stringify(image)
})

