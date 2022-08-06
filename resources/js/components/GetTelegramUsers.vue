<template>
    <div class="container">
        <div class="container top_button_menu">
            <button type="button" class="btn btn-info textarea_admin" v-on:click="back()" >На главную</button>
        </div>
        <div class="row">
        <div class="col-6">
            <div>Выберите номер телефона</div>
            <select v-model="phone">
                <option v-for="tel_phone in telegram_phones">{{ tel_phone.phone }}</option>
            </select>
            <div class="menu_row">
                Код авторизации
                <div><input v-model="auth_code" placeholder="Код авторизации"></div>
            </div>
            <div class="menu_row">
                Ссылка на канал типа @channel
               <div><input v-model="channel" placeholder="Канал"></div>
            </div>
            <div>
                Список технологий
            </div >
            <select v-model="technology">
                <option v-for="tech in technologies" :value="tech.id">{{ tech.techno }}</option>
            </select>
        </div>
        <div class="col-6">
            <div>
        <button type="button" class="btn btn-info textarea_admin" v-on:click="autorization()" >Авторизация в телеграм</button>
        </div>
            <div>
            <button type="button" class="btn btn-info textarea_admin" v-on:click="send_code()" >Отправить код</button>
            </div>
                <div>
        <button type="button" class="btn btn-info textarea_admin" v-on:click="get_users()" >Получить пользователей канала</button>
                </div>
                    <div>
        <button type="button" class="btn btn-info textarea_admin" v-on:click="get_random_users()" >Получить случайных пользователей канала</button>
                    </div>
            <div>
                <button type="button" class="btn btn-info textarea_admin" v-on:click="delete_NO_users()" >УБрать всех юзеров которым невозможно отправить сообщение . С NO в бд </button>
            </div>
<!--            <div>-->
<!--                <button type="button" class="btn btn-info textarea_admin" v-on:click="test()" >Пригласить пользователей в группу </button>-->
<!--            </div>-->
        </div>
        </div>
        <div>
            <ul id="example-1">
                <li v-for="dot in dot_res_arr" class="admin_phones">
                    {{ dot.phone }}
                </li>
            </ul>
        </div>
        <div class="container dotekanie">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-success textarea_admin" v-on:click="pre_dotekanie(channels_name)" >Скрипт дотекания</button>
                </div>
                <div class="col-12">
                    Номер телефона использую Bremen. Выбирать ничего не надо. Менять его в коде. Просто нажимаешь на кнопку.
                </div>
                <div class="col-12">
                    <li v-for="(item,i) in channels_name" :key="item.id"> {{ item.channel }} {{ item.technology_name }} {{ item.numb }}</li>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
           telegram_phones:[],
           phone:'',
           auth_code:'',
           channel:'',
            technology:'',
            technologies:[],
            flag:0,
            dot_counter:0,
            dot_res_arr:[],
            channels_name:[]
        }
    },
    mounted() {
        this.get_phones(this.telegram_phones)
        this.get_technology(this.technologies)
    },
   created() {
    },
    methods: {
        // test()
        // {
        //     axios
        //         .post('/invite_users',{
        //             phone:"+17015104126",
        //         })
        //         .then(({ data }) => {
        //               console.log(data)
        //             },
        //         )
        // },
        delete_NO_users()
        {
            axios
                .post('/delete_NO_users',{
                })
                .then(({ data }) => {
                        alert(data)
                    },
                )
        },
        back()
        {
            Vue.router.push({name:'main'});
        },
        pre_dotekanie(inp)
        {
            axios
                .post('/get_channels',{
                })
                .then(({ data }) => {
                    data.forEach(function(entry) {
                        inp.push({
                            id:entry.id,
                            channel:entry.channel_name,
                            technology_id:entry.technology_id,
                            technology_name:entry.get_tech.technology,
                            numb:0
                        });
                    })
                           this.dotekanie();
                    },
                )
        },
        dotekanie()
        {
            axios
                .post('/dotekanie',{
                    phone:"+17015104126",
                    counter:this.dot_counter,
                })
                .then(({ data }) => {
                    if (data == 'done') {
                        alert('Дотекание окончено')
                    } else if (data < 10) {
                        console.log(typeof data);
                        console.log(data)
                        this.channels_name[this.dot_counter]['numb']=(this.channels_name[this.dot_counter]['numb'])+(data);
                        this.dot_counter++;
                       this.dotekanie();
                    } else {
                        console.log(typeof data);
                        console.log(data)
                        this.channels_name[this.dot_counter]['numb']=(this.channels_name[this.dot_counter]['numb'])+(data);
                       this.dotekanie();
                    }
                    },
                )
        },

        send_message()
        {
            axios
                .post('/send_message',{
                    phone:this.phone,
                })
        },
        get_users()
        {
            axios
                .post('/get_users',{
                    phone:this.phone,
                    channel:this.channel,
                    technology:this.technology,
                })
                .then(({ data }) => (
                        alert(data)
                    ),
                )
        },
        get_random_users()
        {
            axios
                .post('/get_random_users',{
                    phone:this.phone,
                    channel:this.channel,
                    technology:this.technology,
                })
                .then(({ data }) => (
                        console.log(data),
                            this.get_random_users()
                    ),
                )
        },
        send_code()
        {
            axios
                .post('/send_code',{
                    phone:this.phone,
                    auth_code:this.auth_code
                })
                .then(({ data }) => (
                        alert(data)
                    ),
                )
        },
        autorization()
        {
            axios
                .post('/autorization',{
                    phone:this.phone
                })
                .then(({ data }) => (
                        alert(data)
                    ),
                )
        },
        get_phones(inp)
        {
            axios
                .post('/get_phones',{
                })
                .then(({ data }) => (
                        data.forEach(function(entry) {
                            inp.push({
                                id:entry.id,
                                phone:entry.phone,
                                api_id:entry.api_id,
                                api_hash:entry.api_hash
                            });
                        })

                    ),
                )

        },
        get_technology(inp)
        {
            axios
                .post('/get_technology',{
                })
                .then(({ data }) => (
                        data.forEach(function(entry) {
                            inp.push({
                                id:entry.id,
                                techno:entry.technology

                            });
                        })

                    ),
                )

        },
        set_phone()
        {
            console.log(this.phone)
        }
    }
}
</script>
