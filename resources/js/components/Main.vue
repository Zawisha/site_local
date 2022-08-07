<template>
    <div>
          <div class="container login_container">
              <div class="row col-12 d-flex float-left ">
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button" v-on:click="go_get_telegram_users()">Поиск пользователей в группе</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button" v-on:click="go_send_message()">Отправка сообщений</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button" v-on:click="go_admin()">Админка. Редактирование</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button" v-on:click="go_search_telegram()">Поиск в телеграм</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button" v-on:click="go_to_processing()">Обработка</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button vk_butt" v-on:click="go_to_vk()">vk и отправка в телеграм</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button vk_butt" v-on:click="go_to_vk_send()">Рассылка ВК и телега</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button vk_butt" v-on:click="go_to_vk_mes()">сообщения ВК</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button vk_butt" v-on:click="go_to_vk_hand()">сообщения ВК вручную</button>
                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button vk_butt" v-on:click="go_to_content()">контент телеграм из ВК</button>
<!--                  <button  type="button" class="col-2 btn btn-lg btn-outline-dark register_button" v-on:click="go_to_test()">test</button>-->
              </div>
          </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            user:'',
            posts: [],
            is_admin:''
        }
    },
    mounted() {

    },
    created() {
    },
    methods: {
        go_to_content()
        {
            Vue.router.push({name:'content'})
        },
        go_to_vk_hand()
        {
            Vue.router.push({name:'vk_hand'})
        },
        go_to_vk_mes()
        {
            Vue.router.push({name:'vk_message'})

        },
        go_to_test()
        {
            Vue.router.push({name:'test'})

        },
        go_to_vk()
        {
            Vue.router.push({name:'vk'})

        },
        go_to_vk_send()
        {
            Vue.router.push({name:'vk_send'})

        },
        go_to_processing()
        {
            Vue.router.push({name:'processing'})
        },
        go_search_telegram()
        {
            Vue.router.push({name:'search_telegram'})
        },
        go_admin()
        {
            Vue.router.push({name:'admin'})
        },
        go_send_message()
        {
            Vue.router.push({name:'send_telegram_message'})
        },
        go_get_telegram_users()
        {
            Vue.router.push({name:'get_telegram_users'})

        },
        go_login()
        {
            Vue.router.push({name:'login'});

        },
        go_register()
        {
            Vue.router.push({name:'register'});
        },
        logout()
        {
            axios
                .post('/logout',{
                })
                .then(({ data }) => (
                        auth.logout(),
                            Vue.router.push({name:'login'})
                    )
                );
        },
    }
}
</script>
