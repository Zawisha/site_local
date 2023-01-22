<template>
    <div class="container cont_wrap">
        <div class="container top_button_menu">
    <button type="button" class="btn btn-info textarea_admin" v-on:click="back()" >На главную</button>
        </div>
<!--    <div class="container login_register_form_container">-->
<!--        <h1>Редактирование технологии</h1>-->
<!--        <div>-->
<!--            Список технологий-->
<!--        </div>-->
<!--        <select v-model="technology">-->
<!--            <option v-for="tech in technologies" :value="tech.id" v-on:click="show_text()">{{ tech.techno }}</option>-->
<!--        </select>-->
<!--        <div>Количество {{ count_technology }} </div>-->
<!--        <div>-->
<!--            <div>Текст рассылки</div>-->
<!--            <textarea class="form-control textarea_admin" rows="3"  name="text" v-model="text_technology" placeholder="Введите текст рассылки"> </textarea>-->
<!--            <button type="button" class="btn btn-success btn-block procedure_button textarea_admin" v-on:click="save_text()" >Сохранить текст</button>-->
<!--        </div>-->
<!--    </div>-->
        <div class="container login_register_form_container">
            <h1>Добавление технологии</h1>
            <div>Название технологии</div>
            <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="new_technology" placeholder="Введите название новой технологии"></textarea>
            <div>
                <div>Текст рассылки</div>
                <textarea class="form-control textarea_admin" rows="3"  name="text" v-model="new_text_technology" placeholder="Введите текст новой технлогии"> </textarea>
                <button type="button" class="btn btn-info btn-block procedure_button textarea_admin" v-on:click="new_save_text()" >Сохранить технологию</button>
            </div>
        </div>
        <div class="container login_register_form_container">
            <h1>Добавление пользователя</h1>
            <div>Введите данные пользователя технологии</div>
            <div>Телефон</div>
            <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="new_user_telephone" placeholder="Телефон"></textarea>
            <div>api_id</div>
            <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="api_id" placeholder="api_id"></textarea>
            <div>api_hash</div>
            <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="api_hash" placeholder="api_hash"></textarea>
            <div>
                <button type="button" class="btn btn-warning btn-block procedure_button textarea_admin" v-on:click="save_new_tel_user()" >Сохранить пользователя</button>
            </div>
        </div>
        <div class="container login_register_form_container">
            <h1>Добавление старых пользователей</h1>
            <div>Введите username телеги старых пользователя в колонку</div>
            <textarea class="form-control textarea_admin" rows="10"  name="text" v-model="add_old_users_var" placeholder="Список пользователей в колонку"></textarea>
            <div>
                <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="add_old_users()" >Добавить пользователей</button>
            </div>
            <!--                Список технологий-->
                        <select v-model="channel_technology">
                            <option v-for="tech in technologies" :value="tech.id">{{ tech.techno }}</option>
                        </select>
        </div>


<!--        <div class="container login_register_form_container">-->
<!--            <h1>Добавление группы</h1>-->
<!--            <div>Добавьте название группы</div>-->
<!--            <textarea class="form-control textarea_admin" rows="10"  name="text" v-model="group_name" placeholder="название группы"></textarea>-->
<!--            <div>-->
<!--                <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="add_group_name()" >Добавить группу</button>-->
<!--            </div>-->
<!--        </div>-->

<!--        <div class="container login_register_form_container">-->
<!--            <h1>Добавление номера в группу</h1>-->
<!--            <div class="row">-->
<!--            <div class="col-3">-->
<!--            <div>Выберите телефон</div>-->
<!--            <select v-model="phone_to_group">-->
<!--                <option v-for="phone in phone_list">{{ phone.phone }}</option>-->
<!--            </select>-->
<!--            <div>Выберите группу</div>-->
<!--            <select v-model="group_to_add">-->
<!--                <option v-for="group in group_list" v-bind:value="group.id" v-on:click="empty_arr()">{{ group.group_name }}</option>-->
<!--            </select>-->
<!--            </div>-->

<!--            <div class="col-9">-->
<!--            <ul id="example-1">-->
<!--                <li v-for="phones in group_list_old" class="admin_phones">-->
<!--                    {{ phones.phone }}-->
<!--                    <button type="button" class="btn btn-danger" v-on:click="delete_number(phones.phone )">Удалить номер</button>-->
<!--                </li>-->
<!--            </ul>-->
<!--            </div>-->
<!--        </div>-->
<!--            <div>-->
<!--                <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="add_phone_to_group()" >Добавить номер в группу</button>-->
<!--            </div>-->
<!--        </div>-->

<!--        <div class="container login_register_form_container">-->
<!--            <h1>Добавление канала в список каналов</h1>-->
<!--            <div>-->
<!--                Список технологий-->
<!--            </div>-->
<!--            <select v-model="channel_technology">-->
<!--                <option v-for="tech in technologies" :value="tech.id">{{ tech.techno }}</option>-->
<!--            </select>-->
<!--            <div>Добавьте название канала в формате @channel поштучно</div>-->
<!--            <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="channel" placeholder="название канала"></textarea>-->
<!--            <div>-->
<!--                <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="add_channel_tech()" >Добавить канал</button>-->
<!--            </div>-->
<!--        </div>-->
        <div class="container login_register_form_container">
            <h1>Выделение id из username. В формате @username</h1>
            <div>
                <textarea class="form-control textarea_admin" rows="1"  name="text" v-model="username_to_id" placeholder="@username"></textarea>
                <button type="button" class="btn btn-dark btn-block procedure_button textarea_admin" v-on:click="get_ids_users()" >Получить id</button>
            </div>
        </div>

    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            technology:'',
            channel_tech:'',
            channel_technology:'',
            technologies:[],
            channel:'',
            text_technology:'',
            new_technology:'',
            new_text_technology:'',
            count_technology:'',
            new_user_telephone:'',
            api_id:'',
            api_hash:'',
            add_old_users_var:'',
            group_name:'',
            phone_to_group:'',
            group_list:[],
            phone_list:[],
            group_to_add:'',
            group_list_old:[],
            username_to_id:''
        }
    },
    mounted() {
        this.get_technology(this.technologies)
        this.get_phones(this.phone_list)
        this.get_groups(this.group_list)
    },
    created() {
    },
    methods: {
        get_ids_users()
        {
            axios
                .post('/get_ids_users',{
                    phone:"+17015104126",
                    username_to_id:this.username_to_id,
                })
                .then(({ data }) => (
                        alert(data)
                    ),
                )
        },
        add_channel_tech()
        {
            axios
                .post('/add_channel',{
                    technology:this.channel_technology,
                    channel:this.channel,
                })
        },
        delete_number(number)
        {
            axios
                .post('/delete_number',{
                    phone:number,
                })
                .then(({ data }) => (
                        this.empty_arr()
                    ),
                )
        },
        empty_arr()
        {
            this.group_list_old=[];
            this.get_group_list(this.group_list_old);
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
        add_phone_to_group()
        {

           axios
               .post('/add_phone_to_group',{
                   phone:this.phone_to_group,
                   group_id:this.group_to_add
               })
               .then(({ data }) => (
                            this.empty_arr()
                   ),
               )
        },
        add_group_name()
        {
            axios
                .post('/add_group_name',{
                    group_name:this.group_name,
                })
        },
        add_old_users()
        {
            if((this.channel_technology!=='')&&(this.add_old_users_var!==''))
            {
                axios
                    .post('/add_old_users',{
                        add_old_users:this.add_old_users_var,
                        technology_id:this.channel_technology
                    })
                    .then(({ data }) => (
                            alert(data)
                        ),
                    )

            }
            else
            {
                alert('Внимательнее')
            }

        },
        save_new_tel_user()
        {
            axios
                .post('/save_new_tel_user',{
                    new_user_telephone:this.new_user_telephone,
                    api_id:this.api_id,
                    api_hash:this.api_hash,
                })
                .then(({ data }) => (
                        alert(data)
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
        back()
        {
            Vue.router.push({name:'main'});
        },
        save_text()
        {
            axios
                .post('/save_technology_text',{
                    technology:this.technology,
                    technology_text:this.text_technology
                })
                .then(({ data }) => (
                        alert(data)
                    ),
                )
        },
        show_text()
        {
            axios
                .post('/get_technology_text',{
                    technology:this.technology,
                })
                .then(({ data }) => (
                      this.text_technology=data
                    ),
                )
            this.get_count_technology();
        },
        new_save_text()
        {
            axios
                .post('/new_save_technology_text',{
                    new_technology:this.new_technology,
                    new_text_technology:this.new_text_technology
                })
                .then(({ data }) => (
                    alert(data)
                    ),
                )
        }
    }
}
</script>
