import './bootstrap';
import {createApp} from 'vue'
import app from './components/app.vue'
import router from './components/router/index.js'

createApp(app).use(router).mount("#app")
