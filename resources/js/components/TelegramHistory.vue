<template>
    <div class="container">
        <div class="container top_button_menu">
            <button type="button" class="btn btn-info textarea_admin" v-on:click="back()" >На главную</button>
        </div>
        <div>Добавьте название канала в формате @channel поштучно</div>
        <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="channel" placeholder="название канала"></textarea>
        <div>
            <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="add_channel_tech_history()" >Добавить канал</button>
        </div>
        <div>Добавьте поисковое слово или словосочетание поштучно</div>
        <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="word" placeholder="поисковое слово"></textarea>
        <div>
            <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="add_word_tech_history()" >Добавить слово</button>
        </div>
<!--        <div>-->
<!--            <button type="button" class="btn btn-info textarea_admin" v-on:click="get_telegram_messages()" >Получить телеграм сообщения </button>-->
<!--        </div>-->
<!--        <div>-->
<!--            <button type="button" class="btn btn-info textarea_admin" v-on:click="get_oldest()" >Получить старые телегарм сообщения </button>-->
<!--        </div>-->
<!--        <div>-->
<!--            <button type="button" class="btn btn-info textarea_admin" v-on:click="get_telegram_messages_v()" >Получить вакансии новые </button>-->
<!--        </div>-->
<!--        <div>-->
<!--            <button type="button" class="btn btn-info textarea_admin" v-on:click="get_oldest_v()" >Получить вакансии старые </button>-->
<!--        </div>-->
        <div>
            <button type="button" class="btn btn-info textarea_admin" v-on:click="get_separately_messages()" >Получить вакансии с двойным фильтром </button>
        </div>
        <div>
            <button type="button" class="btn btn-info textarea_admin" v-on:click="get_telegram_messages_new_separately()" >Получить НОВЫЕ вакансии с двойным фильтром </button>
        </div>
        <select v-model="in_work">
            <option  v-on:click="change_in_work()">1</option>
            <option  v-on:click="change_in_work()">2</option>
        </select>
        <div>
            <button type="button" class="btn btn-danger textarea_admin" v-on:click="clear_directory()" >Очистить директорию </button>
        </div>
        <div>
            <div>Добавьте старых клиентов в столбик построчно, без запятых</div>
            <textarea class="form-control textarea_admin" rows="3"  name="text" v-model="old_clients" placeholder="список старых клиентов в столбик"></textarea>
            <button type="button" class="btn btn-info textarea_admin" v-on:click="add_old_clients()" >Добавить старых клиентов в игнор список </button>
        </div>
    </div>
</template>

<script>
import axios from "axios";
    export default {

        data()
        {
            return {
                channel:'',
                word:'',
                old_clients:[],
                oldest_counter:0,
                in_work:1
            }
        },
        mounted() {
        },
        methods: {
            change_in_work()
            {
                console.log(this.in_work)
            },
            add_old_clients()
            {
                axios
                    .post('/add_old_clients',{
                        old_clients:this.old_clients,
                    })
                    .then(({ data }) => (
                            alert('готово')
                        ),
                    )
            },
            back()
            {
                Vue.router.push({name:'main'});
            },
            get_oldest()
            {
                axios
                    .post('/get_oldest',{
                        phone:"+79052759745",
                        flag_to_find:"0"
                    })
                    .then(response => {
                        if(this.oldest_counter!=20)
                        {
                            this.oldest_counter++
                            this.get_oldest()
                        }
                        else
                        {
                            this.oldest_counter=0
                            alert('готово 20 штук')
                        }

                    })

            },
            clear_directory()
            {
                axios
                    .post('/clear_directory',{
                        word:this.word,
                    })
                    .then(({ data }) => (
                            alert('директория очищена')
                        ),
                    )
            },
            add_word_tech_history()
            {
                axios
                    .post('/add_word_tech_history',{
                        word:this.word,
                    })
                    .then(({ data }) => (
                            alert(data)
                        ),
                    )
            },
            add_channel_tech_history()
            {
                axios
                    .post('/add_channel_tech_history',{
                        channel:this.channel,
                    })
                    .then(({ data }) => (
                            alert(data)
                        ),
                    )
            },
            get_telegram_messages()
            {
                axios
                    .post('/get_telegram_messages',{
                         phone:"+79052759745",
                        flag_to_find:"0"
                    })
            },
            get_telegram_messages_v()
            {
                axios
                    .post('/get_telegram_messages',{
                        phone:"+79052759745",
                        flag_to_find:"1"
                    })
            },
            get_oldest_v()
            {
                axios
                    .post('/get_oldest',{
                        phone:"+79052759745",
                        flag_to_find:"1"
                    })
                    .then(response => {
                        if(this.oldest_counter!=20)
                        {
                            this.oldest_counter++
                            this.get_oldest_v()
                        }
                        else
                        {
                            this.oldest_counter=0
                            alert('готово 20 штук')
                        }

                    })

            },
            get_separately_messages()
            {
                axios
                    .post('/get_separately_messages',{
                        phone:"+79052759745",
                        in_work:this.in_work,
                    })
                    .then(response => {
                        if(this.oldest_counter!=20)
                        {
                            this.oldest_counter++
                            this.get_separately_messages()
                        }
                        else
                        {
                            this.oldest_counter=0
                            alert('готово 20 штук')
                        }

                    })
            },
            get_telegram_messages_new_separately()
            {
                axios
                    .post('/get_telegram_messages_new_separately',{
                        phone:"+79052759745",
                        in_work:this.in_work,
                    })
                    .then(response => {
                        if(this.oldest_counter!=20)
                        {
                            this.oldest_counter++
                            this.get_telegram_messages_new_separately()
                        }
                        else
                        {
                            this.oldest_counter=0
                            alert('готово 20 штук')
                        }

                    })
            }
        }
    }
</script>
