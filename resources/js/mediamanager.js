import Vue from 'vue'
import VueToast from 'vue-toast-notification';
import {EventBus} from '../../vendor/colorijo/mediamanager/resources/assets/js/event-bus.js'
import {default as baseAxios} from 'axios'
Vue.component('media-manager', require('../../vendor/colorijo/mediamanager/resources/assets/js/MediaManager.vue').default)
Vue.component('media-featured', require('../../vendor/colorijo/mediamanager/resources/assets/js/Featured.vue').default)

Vue.use(VueToast, {position: 'top-right'});
EventBus.$on('choosedImage', image => {
    let chooseImage = document.getElementById('choosedImage');
    choosedImage.value = JSON.stringify(image)
})
const methodsObj = Object.assign({}, ['get', 'post', 'put', 'delete', 'patch'])
var axios = {}
Object.keys(methodsObj).forEach(e => {
    Object.defineProperty(axios, methodsObj[e], {
        value: Object.values(methodsObj)[e] = (uri, a = '', b = '') => {
            if(navigator.onLine){
                return baseAxios[methodsObj[e]](uri, a, b)
            }else{
                return Promise.reject({
                    response: {
                        status: 'offline',
                        data: {
                            msg: `You're is offline`
                        }
                    }
                })
            }
        }
    })
})
const plugin = {
    install () {
        Vue.axios = axios
        Vue.prototype.$axios = axios
    }
}
//
Vue.use(plugin)
