<template>
    <div class="container">
        <div class="row justify-content-center">

            <div class="container login_register_form_container">
                <div v-if="show_instruction">
                    Инструкция по созданию новых групп:
                   <div>
                       1) Создаёшь канал Авторынок Москва 1
                       2) Описание   Здесь можно купить и продать авто и комплектующие в Москве. По любым вопросам обращаться в администрацию канала @zawisha
                       3) Ссылка     avto_rynok_moskva_1
                       4) Ставишь лого в папке avto_logo
                       5) Ставишь в список каналов в контроллере в массив TelegramUsersController  363 строчка
                       6) Вступаешь в каналы ботами
                       7) добавляешь администраторов      Управление каналом Администраторы
                       8) Добавляешь группы в базу
                       https://vk.com/moscow_autorynok 122606077 Москва
                       https://vk.com/autosalon_spb 59970928 Питер
                       https://vk.com/prodazha_avto_novosibirsk 161880778 Новосиб
                       https://vk.com/autorynok116 103955552 Казань
                       https://vk.com/ekb_auto196 101416214 Екатеринбург
                   </div>
                </div>
                <button type="button" class="tn btn-dark btn-block procedure_button textarea_admin" v-on:click="show_instruction_f()" >Показать инструкцию по созданию групп пошагово </button>
                <div>Добавление группы ВК к каналу телеграм. Для этого введи в поля id группы ВК без минуса например 67580761 и название привязанного телеграм канала в формате @belarus_online</div>
                <div>
                    <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="vk_group" placeholder="Группа вк в формате числа группы без минуса, например 67580761"></textarea>
                    <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="telegram_channel" placeholder="Название канала телеграм в формате @belarus_online"></textarea>
                    <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="add_vk_group_to_channel()" >Добавить группу к каналу</button>
                </div>
                <h1>
                        {{ response_add_vk_to_channel }}
                </h1>
            </div>
            <div class="container login_register_form_container">
                <div> Добавление поста в каналы телеграм. В столбик ссылки на посты в формате https://vk.com/feed?w=wall-18923125_1354473</div>
                <div>
                    <textarea class="form-control textarea_admin" rows="4"  name="text" v-model="post_list_one" placeholder="Формат https://vk.com/feed?w=wall-18923125_1354473"></textarea>
                    <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="add_post_to_list()" >Добавить пост в список</button>
                </div>
                <div>
                   <div>
                       {{ added_message_post_list }}
                   </div>
                    <div v-for="post in post_list" class="vk_list_text">
                        {{ post }}
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div>
                        <button type="button" class="tn btn-dark btn-block procedure_button textarea_admin" v-on:click="posts()" >Сделать посты </button>
                    </div>
                    <div v-for="resp in response_post_list" class="vk_list_text">
                        {{ resp }}
                    </div>
                </div>
            </div>
            <div class="col-md-12 invite_users">
                <div>
                    <div>
                        <button type="button" class="btn btn-secondary" v-on:click="invite_users()" >Пригласить пользователей в группу </button>
                    </div>
                    <div>
                        <div>Количество инвайтов с этого номера</div>
                        <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="number_local_counter" placolder="Количество инвайтов с этого номера"></textarea>
                        <div>Текущий номер</div>
                        <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="number" placolder="Текущий номер"></textarea>
                    </div>
                    <div>
                        <div>Текущая группа</div>
                        <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="group" placeholder="Текущая группа"></textarea>
                    </div>
                    <div>
                        <div>Текущая технология</div>
                        <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="technology_id" placeholder="Текущая технология"></textarea>
                    </div>
                    <div class="bottom_Vk">
                        крестов крылов якушев самойлов
                        ['+79086887535','+79626348703','+79055176921','+79771510768'],
                        ['https://t.me/avto_rynok_moskva_2','https://t.me/avto_rynok_moskva_3','https://t.me/avto_rynok_russia','https://t.me/avto_rynok_moskva_1']
                        ['28','28','28','28']
                    </div>
                </div>
            </div>
            <div class="col-md-12 invite_users">
                <div>
                    <button type="button" class="btn btn-secondary" v-on:click="getVKusers()" >Собрать пользователей кому будем отправлять сообщения </button>
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
                vk_group:'',
                telegram_channel:'',
                response_add_vk_to_channel:'',
                post_list:new Array(),
                response_post_list:new Array(),
                post_list_one:'',
                added_message_post_list:'',
                show_instruction:false,
                number_in_array:0,
                number:'+79626348703',
                group:'https://t.me/avto_rynok_moskva_2',
                //счётчик для одного телефона
                number_local_counter:0,
                //id технологии
                technology_id:'28'
            }
        },

        mounted() {

        },
        methods: {
            getVKusers()
            {
                axios
                    .post('/getVKusers',{
                        technology_id:'28',
                        post_id:'https://vk.com/moscow_autorynok?w=wall-122606077_457628',
                    })
                    .then(({ data }) => {
                            // if(data.success==='yes')
                            // {
                            //     this.number_local_counter++
                            //     if(this.number_local_counter<40)
                            //     {
                            //         console.log('ok')
                            //         this.invite_users()
                            //     }
                            // }
                            // else
                            // {
                            //     if(data.message.rpc==='PEER_FLOOD')
                            //     {
                            //         alert('ОШИБКА СПАМА')
                            //     }
                            //     else
                            //     {
                            //         if(data.message==='200 users')
                            //         {
                            //             alert('в группе 200 человек')
                            //         }
                            //         else
                            //         {
                            //             this.invite_users()
                            //         }
                            //     }
                            // }
                        },
                    )
            },
            invite_users()
            {
                axios
                    .post('/invite_users',{
                        phone:this.number,
                        group:this.group,
                        technology_id:this.technology_id,
                        first_reg:this.number_local_counter
                    })
                    .then(({ data }) => {
                        if(data.success==='yes')
                        {
                            this.number_local_counter++
                            if(this.number_local_counter<40)
                            {
                                console.log('ok')
                                this.invite_users()
                            }
                        }
                        else
                        {
                            if(data.message.rpc==='PEER_FLOOD')
                            {
                                alert('ОШИБКА СПАМА')
                            }
                            else
                            {
                                if(data.message==='200 users')
                                {
                                    alert('в группе 200 человек')
                                }
                                else
                                {
                                    this.invite_users()
                                }
                            }
                        }
                        },
                    )
            },
            show_instruction_f()
            {
              this.show_instruction=(!this.show_instruction)
            },
            add_post_to_list()
            {
                if(this.post_list_one!='')
                {
                    let flag=false;
                    for (let i = 0; i < this.post_list.length; i++) {
                        if (this.post_list[i] === this.post_list_one) {
                            this.added_message_post_list='Пост уже существует'
                            flag=true;
                        }
                    }
                if(!flag)
                {
                    this.post_list.push(this.post_list_one);
                    this.added_message_post_list='Добавлено, длина списка '+this.post_list.length
                    this.post_list_one=''
                }
                }
                else
                {
                    this.added_message_post_list='Пустое поле'
                }
            },
            posts()
            {
                if(this.post_list.length>0)
                {
               let r = this.post_list.splice(0,1)

                    axios({
                        method: "post",
                        url: '/send_to_telegram',
                        headers: {
                            "Content-Type": "application/json"
                        },
                        data: {
                            post_id:r[0],
                        }
                    })
                        .then(response => {
                            const serverResponse = response.data;
                            // do sth ...
                        })
                        .catch(error => {
                            console.log(error);
                        });

               // axios
                //   .post('/send_to_telegram',
                   //    {
                   //        post_id:r[0],
                   //    })
                  // .then(response => {
                    //   console.log(response.data.todo)
                   //   this.posts()
                  // })
                }
            },
            add_vk_group_to_channel()
            {
                axios
                    .post('/add_vk_group_to_channel',{
                        vk_group:this.vk_group,
                        telegram_channel:this.telegram_channel,
                    })
                    .then(response => {
                        this.response_add_vk_to_channel=response.data.message
                        if(response.data.todo)
                        {
                            if(response.data.todo == 'clear_field')
                            {

                            }
                        }
                    })
            }
        }
    }
</script>
