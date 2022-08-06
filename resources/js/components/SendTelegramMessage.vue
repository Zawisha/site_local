<template>
    <div class="container">
        <div class="container top_button_menu">
            <button type="button" class="btn btn-info textarea_admin" v-on:click="back()" >На главную</button>
        </div>
        <div class="row">
        <div class="col-6">
            <div>Выберите номер телефона</div>
            <select v-model="phone">
                <option v-for="tel_phone in telegram_phones" v-on:click="null_show_message_counter()">{{ tel_phone.phone }}</option>
            </select>
            <div>
                Осталось в технологии: {{ count_technology }}
            </div>
            <div>
                Отправлено сообщений: {{ show_message_counter }}
            </div>
            <div>
                Код авторизации
              <div>
                  <input v-model="auth_code" placeholder="Код авторизации">
              </div>
            </div>
            <div>
                Список технологий
            </div>
            <select v-model="technology">
                <option v-for="tech in technologies" :value="tech.id" v-on:click="show_count_tech()">{{ tech.techno }}</option>
            </select>
            <div>
                Количество сообщений
               <div>
                   <input v-model="message_counter" placeholder="Количество сообщений">
               </div>
            </div>
        </div>
        <div class="col-6">
            <div>
        <button type="button" class="btn btn-info textarea_admin" v-on:click="autorization()" >Авторизация в телеграм</button>
            </div>
            <div>
        <button type="button" class="btn btn-info textarea_admin" v-on:click="send_code()" >Отправить код</button>
        </div>
            <div>
        <button type="button" class="btn btn-info textarea_admin" v-on:click="send_message()" >Отправить сообщения</button>
        </div>
        </div>
        </div>
        <div>Выберите группу</div>
        <select v-model="group_to_add">
            <option v-for="group in group_list" v-bind:value="group.id" v-on:click="empty_arr()">{{ group.group_name }}</option>
        </select>
        <button type="button" class="btn btn-warning textarea_admin" v-on:click="send_message_to_group()" >Отправить сообщения группе</button>
        <div>
            <ul id="example-1">
                <li v-for="phones in group_list_old" class="admin_phones">
                    {{ phones.phone }}
                    <button type="button" class="btn btn-success textarea_admin" v-on:click="delete_from_sending(phones.id,group_list_old)" >Удалить телефон из рассылки</button>
                </li>
            </ul>
        </div>

        <div>Отправка сообщений по личкам для поиска клиентов</div>
        <div>
            <input v-model="client_id" placeholder="id клиента">
        </div>
        <button type="button" class="btn btn-secondary textarea_admin" v-on:click="send_message_to_group_to_find_cust(1)" >Отправка сообщений по личкам для поиска клиентов ENGLISH</button>
        <button type="button" class="btn btn-success textarea_admin" v-on:click="send_message_to_group_to_find_cust(2)" >Отправка сообщений по личкам для поиска клиентов РУССКИЕ</button>
        <button type="button" class="btn btn-success textarea_admin" v-on:click="test()" >ТЕСТ</button>

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
            technology:'',
            technologies:[],
            message_counter:'20',
            show_message_counter:'0',
            count_technology:'',
            group_to_add:[],
            group_list:[],
            group_list_old:[],
            account_number:0,
            client_id:''
        }
    },
    mounted() {
        this.get_phones(this.telegram_phones)
        this.get_technology(this.technologies)
        this.get_groups(this.group_list)
    },
   created() {
    },
    methods: {
        test()
        {
            // console.log(this.group_list_old[this.account_number]['id']);
            console.log(this.group_list_old);
        },
        delete_from_sending(id,group_list)
        {
            group_list.forEach(function(el, i) {
                if (el.id === id) group_list.splice(i, 1)
            })
        },
        send_message_to_group_to_find_cust(local)
        {
            axios
        .post('/send_message_to_group_to_find_cust', {
            local: local,
            id_client:this.client_id
        })
                .then(({ data }) => (
                        alert(data)
                    ),
                )

            this.client_id='';
        },
        empty_arr()
        {
            this.group_list_old=[];
            this.get_group_list(this.group_list_old);
        },
        send_message_to_group()
        {
            if((this.show_message_counter)==(this.message_counter))
            {
                this.show_message_counter='0';
                this.account_number++;
                if(this.account_number<this.group_list_old.length) {
                    console.log('новый номер телефона ' + this.group_list_old[this.account_number]['phone'])
                }
                else
                {
                    console.log('рассылка окончена');
                }
            }

            if(this.account_number<this.group_list_old.length) {
                axios
                    .post('/send_message_to_group', {
                        id_phone: this.group_list_old[this.account_number]['id'],
                        show_message_counter: this.show_message_counter,
                        technology: this.technology,
                        group_id: this.group_to_add,
                    })
                    .then(({data}) => {
                        console.log('отправлено с телефона' + this.group_list_old[this.account_number]['phone'] + ' сообщение номер ' + this.show_message_counter)
                        this.show_message_counter++
                        this.send_message_to_group()
                        },
                    )
            }
        },
        get_groups(inp)
        {
            axios
                .post('/get_groups',{
                })
                .then(({ data }) => (
                        data.forEach(function(entry) {
                            inp.push({
                                id:entry.id,
                                group_name:entry.group_name
                            });
                        })

                    ),
                )
        },
        get_group_list(inp)
        {
            axios
                .post('/get_group_list',{
                    group_id:this.group_to_add
                })
                .then(({ data }) => (
                        data.forEach(function(entry) {
                            inp.push({
                                id:entry.id,
                                phone:entry.phone
                            });
                        })

                    ),
                )

        },
        show_count_tech()
        {
            axios
                .post('/get_count_technology',{
                    technology:this.technology,
                })
                .then(({ data }) => (
                        this.count_technology=data
                    ),
                )
        },
        get_count_technology()
        {
            axios
                .post('/get_count_technology',{
                    technology:this.technology,
                })
                .then(({ data }) => (
                        this.count_technology=data
                    ),
                )
        },
        null_show_message_counter()
        {
          this.show_message_counter='0';
        },
        back()
        {
            Vue.router.push({name:'main'});
        },
        send_message()
        {
            console.log('SENDING');
            if(this.show_message_counter<(this.message_counter-1))
            {
                console.log('SENDING1');
            axios
                .post('/send_message',{
                    phone:this.phone,
                    technology:this.technology,
                })
                .then(({ data }) =>
                    {
                        if(!(data.match(/Telegram returned an RPC error/)))
                        {
                            this.send_message()
                            console.log('1')
                            console.log(data)
                            this.show_message_counter++;
                        }
                        else
                        {
                            console.log('2')
                            console.log(data)
                        }
                    },
                )
            }
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
