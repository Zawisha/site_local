import OnePost from "./components/OnePost";

require('./bootstrap');

window.Vue = require('vue');
import Vue from 'vue'
 import axios from 'axios';

import Api from './api.js';
window.api = new Api();
import VueRouter from 'vue-router';
Vue.use(VueRouter);
window.Event = new Vue;
import Auth from './auth.js';
window.auth = new Auth();
import App from './components/App';
import Main from './components/Main'
import Login from './components/Login'
import Register from './components/Register'
import Admin from './components/Admin'
import GetTelegramUsers from './components/GetTelegramUsers'
import SendTelegramMessage from './components/SendTelegramMessage'
import TelegramHistory from './components/TelegramHistory'
import TestComponent from './components/TestComponent'
import Processing from './components/Processing'
import VkComponent from './components/VkComponent'
import VkSendComponent from './components/VkSendComponent'
import VkMessageComponent from "./components/VkMessageComponent";
import VkHandComponent from "./components/VkHandComponent";
import ContentComponent from "./components/ContentComponent";
import TelegramSendingComponent from "./components/TelegramSendingComponent";
 // axios.defaults.baseURL = 'http://localhost:/api';
 axios.defaults.baseURL = 'http://site2.loc:/api';

const router = new VueRouter({

    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'main',
            component: Main
        },
        {
            path: '/content',
            name: 'content',
            component: ContentComponent
        },
        {
            path: '/vk_hand',
            name: 'vk_hand',
            component: VkHandComponent
        },
        {
            path: '/vk_message',
            name: 'vk_message',
            component: VkMessageComponent
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/register',
            name: 'register',
            component: Register
        },
        {
            path: '/question',
            name: 'question',
            component: OnePost,
            props: true
        },
        {
            path: '/get_telegram_users',
            name: 'get_telegram_users',
            component: GetTelegramUsers
        },
        {
            path: '/processing',
            name: 'processing',
            component: Processing
        },
        {
            path: '/search_telegram',
            name: 'search_telegram',
            component: TelegramHistory
        },
        {
            path: '/send_telegram_message',
            name: 'send_telegram_message',
            component: SendTelegramMessage
        },
        {
            path: '/test',
            name: 'test',
            component: TestComponent
        },
        {
            path: '/vk',
            name: 'vk',
            component: VkComponent
        },
        {
            path: '/vk_send',
            name: 'vk_send',
            component: VkSendComponent
        },
        {
            path: '/telegram_sending',
            name: 'telegram_sending',
            component: TelegramSendingComponent
        },
        {
            path: '/admin',
            name: 'admin',
            component: Admin,
            //
            // beforeEnter: (to, from, next) => {
            //     if(auth.user){
            //         let is_admin_ent =''
            //         axios
            //             .post('/is_admin',{
            //             user_id:auth.user.id
            //             }).then(response => {
            //             if(response.data.length != 0)
            //             {
            //                 is_admin_ent=response.data[0].status
            //                 if(is_admin_ent==1)
            //                 {
            //                     next({ })
            //
            //                 }
            //                 else
            //                 {
            //                     next({ path: '/' })
            //                 }
            //             }
            //
            //         })
            //     }
            //     else {
            //         next({ path: '/' })
            //     }
            // },
        }
    ]
})
Vue.router = router;
const app = new Vue({
    el: '#app',
    components: {App},
    router,
});
