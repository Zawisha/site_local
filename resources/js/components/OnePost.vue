<template>
    <div class="container login_register_form_container">
        <button type="button" class="btn btn-success textarea_admin" v-on:click="back()" >На главную</button>
        <div class="row justify-content-center">
       <h1>{{ title }}</h1>
            <div class="col-12">{{ description }}</div>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {
            title:'',
            description:''
        }
    },
    props:['q_id'],
    mounted() {
        this.get_post_info();
    },
    created() {
    },
    methods: {

        back()
        {
            Vue.router.push({name:'main'});
        },

        get_post_info()
        {
            this.posts=[];
            axios
                .post('/get_post_info',{
                    id:this.q_id
                })
                .then(({ data }) => (
                       this.title=(data[0]['title']),
                       this.description=(data[0]['description'])
                    ),

                )
        }


    }
}
</script>
