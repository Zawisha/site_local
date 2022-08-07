<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 invite_users">
                <div>Группа вк должна быть добавлена в таблицу vk_groups</div>
                <div>А так же группа вк в таблицу vk_search_group</div>
                <div class="get_posts">
                    <button type="button" class="btn col-12 btn-secondary " v-on:click="get_list_content_group(group_list)" >Получить посты</button>
                </div>
                <div>
                    <button type="button" class="btn col-12 btn-secondary" v-on:click="posts()" >Отправить посты</button>
                </div>
            <div class="col-12">
                <div v-for="post in group_list" class="">
                    {{ post.group_vk_number }}_{{ post.post_vk_number }}
                </div>
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

            }
        },

        mounted() {
           // this.get_list_content_group(this.group_list)
        },
        methods: {
            get_list_content_group(inp)
            {
                axios
                    .post('/get_list_content_group',{
                    })
                    .then(({ data }) => {
                            data.posts_list.forEach(function(entry) {
                                inp.push({
                                    group_vk_number:entry.from_id,
                                    post_vk_number:entry.id
                                });
                            })
                        },
                    )
            },

        posts()
        {
            if(this.group_list.length>0)
            {
                let r = this.group_list.splice(0,1)

                axios({
                    method: "post",
                    url: '/send_to_telegram',
                    headers: {
                        "Content-Type": "application/json"
                    },
                    data: {
                        group_vk_number:r[0]['group_vk_number'],
                        post_vk_number:r[0]['post_vk_number'],
                    }
                })
                    .then(response => {
                       this.posts()
                    })
                    .catch(error => {
                        this.posts()
                    });

            }
        },
        }
    }
</script>
