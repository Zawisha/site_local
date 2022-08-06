<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 invite_users">
                <div>
                    <button type="button" class="btn col-12 btn-secondary" v-on:click="showAddgroup()" >Инструкция как добавить группу в поиск</button>
                </div>
                <div v-if="showInstruction">
                    1) В таблицу vk_search_group заносишь номер группы технологию и последний пост
                    2) В таблицу vk_posts тоже самое
                </div>
            </div>
            <div class="col-12 invite_users">
                <div>
                    <button type="button" class="btn col-12 btn-secondary" v-on:click="getVKusers()" >Собрать пользователей кому будем отправлять сообщения </button>
                </div>
            </div>
            <div class="col-12">
                <div v-for="tech in technology_counter_arr" class="">
                    {{ tech.city }}:{{ tech.count }}
                </div>
            </div>
            <div class="col-12">
                <div v-for="group in group_list" class="">
                        {{ group.group_id }}
                </div>
            </div>
            <div class="col-12 invite_users">
                <div>
                    <button type="button" class="btn col-12 btn-secondary" v-on:click="showVKusers(vk_free_users_list)" >Показать пользователей кому не отправлял сообщение</button>
                </div>
                <div class="col-12">
                    <div v-for="(user,index) in vk_free_users_list" class="col-6 row us_show_list">
                       <div class="col-6 d-flex justify-content-center align-items-center">
                           https://vk.com/id{{ user.user_id }}
                       </div>
                        <div class="col-4">
                            <button type="button" class="btn col-12 btn-primary" v-on:click="deleteshowVKusers(index,user.user_id)">Сделано</button>
                        </div>
                    </div>
                </div>
            </div>
<!--            <textarea-->
<!--                placeholder="Paste your content here"-->
<!--                cols="30"-->
<!--                rows="2"-->
<!--                id="clipboard-area"-->
<!--                v-model="clip_text"-->
<!--            ></textarea>-->
        </div>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        data() {
            return {
                group_list:[],
                number_in_array:0,
                group:'',
                technology_id:'',
                incr_tech_counter:0,
                technology_counter_arr:[ { city: 'Санкт-Петербург', count:0, technology:24 },],
                vk_free_users_list:[],
                vk_text:[],
                clip_text:'',
                mess_counter:0,
                showInstruction:false
            }
        },

        mounted() {
            this.get_list_of_search_group(this.group_list)
            this.get_vk_text_buy(this.vk_text)
        },
        methods: {
            showAddgroup()
            {
              this.showInstruction=!this.showInstruction
            },
            get_vk_text_buy(inp)
            {
                axios
                    .post('/get_vk_text_buy',{
                    })
                    .then(({ data }) => {
                            data.messages.forEach(function(entry) {
                                inp.push({
                                    message_text:entry.message_text
                                });
                            })
                        },
                    )
            },
            deleteshowVKusers(index,user_id)
            {
                var input = document.createElement('input');
                input.setAttribute('value', this.vk_text[this.mess_counter]['message_text']);
                document.body.appendChild(input);
                input.select();
                var result = document.execCommand('copy');
                document.body.removeChild(input);
                  this.vk_free_users_list.splice(index, 1);
                 axios
                     .post('/deleteshowVKusers',{
                         user_id:user_id,
                      })
                this.mess_counter=Number(this.mess_counter)+Number(1);
                if(this.mess_counter==20)
                {
                    this.mess_counter=0;
                }
                window.open(
                    'https://vk.com/id'+user_id,
                    '_blank' // <- This is what makes it open in a new window.
                );
                return result;
            },
            showVKusers(inp)
            {
                axios
                    .post('/showVKusers',{
                        technology_id:'24',
                    })
                    .then(({ data }) => {
                        data.user_list.forEach(function(entry) {
                            inp.push({
                                user_id:entry.user_id
                            });
                        })
                        },
                    )
            },
            get_list_of_search_group(inp)
            {
                axios
                    .post('/get_list_of_search_group',{
                    })
                    .then(({ data }) => {
                            data.groups.forEach(function(entry) {
                                inp.push({
                                    id:entry.id,
                                    group_id:entry.group_id,
                                    technology_id:entry.technology_id,
                                    last_post:entry.last_post
                                });
                            })

                        },
                    )
            },
            getVKusers()
            {
                if(this.group_list.length>0)
                {
                axios
                    .post('/getVKusers',{
                        group_id:this.group_list[0]['group_id'],
                        technology_id:this.group_list[0]['technology_id'],
                    })
                    .then(({ data }) => {
                        this.incr_tech_counter=data.count
                        this.render_count_arr();
                        this.getVKusers();

                        },
                    )
                }
            },
            render_count_arr()
            {
                for (let i = 0; i <this.technology_counter_arr.length ; i++) {
                   if(this.technology_counter_arr[i].technology==this.group_list[0]['technology_id'])
                   {
                       this.technology_counter_arr[i].count=this.incr_tech_counter
                   }
                }
                this.group_list.splice(0, 1);
            }

        }
    }
</script>
