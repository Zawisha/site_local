<template>
    <div class="container">
        <div class="row justify-content-center">
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
                technology_counter_arr:[ { city: 'Москва', count:0, technology:28 }, { city: 'Санкт-Петербург', count:0, technology:24 },]
            }
        },

        mounted() {
            this.get_list_of_search_group(this.group_list)
        },
        methods: {
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
