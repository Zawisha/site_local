<template>
    <div class="container login_register_form_container">
        <button type="button" class="btn btn-success textarea_admin" v-on:click="back()" >На главную</button>
        <div class="row justify-content-center">
       <h3 class="col-12">Всего {{ count_all }}</h3>
       <h3 class="col-12">Осталось в партии {{ yet_cust }}</h3>
            <button type="button" class="btn btn-success textarea_admin col-4 cust_button"  v-on:click="delete_cust(customers[customer_next_counter].id)" >Убрать</button>
            <button type="button" class="btn btn-success textarea_admin col-4 cust_button_add" v-on:click="add_cust(customers[customer_next_counter].id,customers[customer_next_counter].text )" >Добавить</button>
            <div v-if="customers[customer_next_counter]" class="col-12">{{ customers[customer_next_counter].text }}</div>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {
            title:'',
            description:'',
            count_all:0,
            customers:[],
            customer_next_counter:0,
            yet_cust:0
        }
    },

    mounted() {
        this.get_post_info(this.customers);
    },
    created() {
        this.onKeyDown = this.onKeyDown.bind(this);
        document.addEventListener('keydown', this.onKeyDown);
    },
    methods: {
        back()
        {
            Vue.router.push({name:'main'});
        },
        onKeyDown(e) {
            //левая
            if(e.keyCode===37)
            {
                this.delete_cust(this.customers[this.customer_next_counter].id)
            }
            //правая
            if(e.keyCode===39)
            {
                this.add_cust(this.customers[this.customer_next_counter].id,this.customers[this.customer_next_counter].text )
            }
        },
        delete_cust(id)
        {
          this.customer_next_counter++;
          this.yet_cust=this.customers.length-this.customer_next_counter;
            axios
                .post('/delete_cust',{
                    id:id
                })
          this.check_empty_cust();
        },
        add_cust(id,text)
        {
            this.customer_next_counter++;
            this.yet_cust=this.customers.length-this.customer_next_counter;
            axios
                .post('/add_cust',{
                    id:id,
                    text:text,
                })
            this.check_empty_cust()

        },
        check_empty_cust()
        {
            if(this.customers.length===this.customer_next_counter)
            {
                alert('Закончил партию')
            }
        },
        get_post_info(inp)
        {
            this.posts=[];
            axios
                .post('/get_processing',{
                })
                .then(({ data }) => (
                        this.count_all=data.count,
                            data.customers.forEach(function(entry) {
                                inp.push({
                                    id:entry.id,
                                    text:entry.text,
                                });
                            })
                    )
                );
        }


    }
}
</script>
