class Auth {
    constructor() {
        this.token = window.localStorage.getItem('token');

        let userData = window.localStorage.getItem('user');
        this.user = userData ? JSON.parse(userData) : null;


        if (this.token != 'null') {
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            this.getUser();
        }
    }

    login (token, user) {
        window.localStorage.setItem('token', token);
        window.localStorage.setItem('user', JSON.stringify(user));

        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        this.token = token;
        this.user = user;
        Event.$emit('userLoggedIn');
    }
    check () {
        return !! this.token;
    }
    getUser() {
        api.call('get', '/api/get-user')
            .then(({data}) => {
                this.user = data;
            });
    }
    logout()
    {
        window.localStorage.setItem('token', null);
        window.localStorage.setItem('user', null);
        this.token=null;
        this.user=null;
        Event.$emit('userLoggedOut');

    }

}

export default Auth;
