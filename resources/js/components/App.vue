<template>
    <div>
            <router-view></router-view>
    </div>
</template>

<script>
export default {
    data() {
        return {

            user:'',
        }
    },
    mounted() {
        this.user = auth.user;
        Event.$on('userLoggedIn', () => {
            this.user = auth.user;
        });
        // if(this.user)
        {
            Event.$on('userLoggedOut', () => {
                this.user = auth.user;
            });
        }
    },
    created() {
    },
    methods: {

    }
}
</script>
