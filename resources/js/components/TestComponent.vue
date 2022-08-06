<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                    TEST
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        mounted() {
            this.get_mad()
        },
        methods: {
            get_mad()
            {
                axios
                    .post('/get_mad',{
                        phone:"+17015104126",
                        in_work:1,
                    })
                    .then(response => {
                   console.log('ok111');

                    })

            },
        }
    }
</script>
