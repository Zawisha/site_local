<template>
    <div class="container login_register_form_container ">

        <div class="col-12 row justify-content-center">Для входа введите почту и пароль</div>
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
        <button type="submit" class="btn btn-primary" v-on:click="login()">Войти</button>
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
        login()
        {
            this.alert=false
            this.email.trim();
            this.password.trim();
            if (this.validEmail(this.email)) {
            if( (this.email!=='')&&(this.password!=='') )
            {
                axios
                    .post('/login',{
                        password:this.password,
                        email:this.email
                    })
                    .then(({ data }) => (
                            auth.login(data.token, data.user),
                                Vue.router.push({name:'main'})
                        )
                    )
                .catch(
                    this.alert=true
                )
            }

            }

        },
        validEmail(email) {
            let re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }

    }
}
</script>
