<template>
    <div class="container login_register_form_container">
        <div class="col-12 row justify-content-center">Регистрация</div>
        <div class="row justify-content-center">
            <div class="form-group col-4">
                <label for="exampleInputEmail1">Email address</label>
                <input v-model="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="form-group col-4">
                <label for="exampleInputPassword1">Password</label>
                <input v-model="password"  type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>
        </div>
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-primary" v-on:click="register()">Зарегистрироваться</button>
        </div>
        <div class="row justify-content-center alert_mes">
            <div v-if="alert" class="col-4 alert alert-danger " role="alert">
                Ошибка входа, проверьте логин и пароль
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            email:'',
            password:'',
            alert:false
        }
    },
    mounted() {

    },
    created() {
    },
    methods: {
        register()
        {
            this.alert=false
            this.email.trim();
            this.password.trim();

            axios
                .post('/register',{
                    password:this.password,
                    email:this.email
                })
                .then(({ data }) => {
                    if (data.status == 'success') {
                        Vue.router.push({name: 'login'})
                    }
                    }
                )
                .catch(
                    this.alert=true
                )


        }

    }
}
</script>
