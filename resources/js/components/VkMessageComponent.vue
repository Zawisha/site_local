<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 invite_users">
                <div>
                    <button type="button" class="btn col-12 btn-secondary" v-on:click="send_inv_message()" >Отправить сообщения </button>
                </div>
            </div>
            <div class="col-12 invite_users">
                <div>
                    <button type="button" class="btn col-12 btn-primary" v-on:click="send_inv_message_second()" >Отправить вторые сообщения </button>
                </div>
            </div>
            <div class="col-12">
                <div v-for="tech in vk_akks_arr" class="">
                    {{ tech.number }}
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
                vk_akks_arr:[
                     { number: '79387898196', token:'vk1.a.BNuAUoo6Mwj-X5EfLik27wgahy_KHdiD-kI56plqKVNomJ4UienmkcdMX8Og0FEOrDj61L720oR22fdnadiEcp36W8_nhNHAYH9zm1TpkzYWMl4Zi9QdyeUfIAig5LprsuNtz9SRzp6t625xY32Epztj4iA2Dtv1ZeNTIK73g_WVDokEwbNEod_L6znSvj6k', },
                    //b { number: '79916677748', token:'vk1.a.tAt3YvJhMt3ZS_KTa_K0deulSzbZXGYHf_zlClW0UMx6tpZ1BzLzUD1xsGogWl9cTnoZcISl_MPA895MooYiaicuFspEY3RTSq-7OQLbT8ouBhATL9y_T7hvEroHmQTlrTzSGCyimEXCz19TRQ1MJZQA17mef9A928LQRUVURmMqtLUDSEaFPFYAgrijoQUD', },
                     { number: '79812505315', token:'vk1.a.aLKEunuhN2mfWqoOu05sjYF6rAuXDuuyD7wXwObTRMMsT4CvPSqJTjIvPjoYG3f07jSy41lNZTRimqORNH6vzZVfWTHdFhTov19LhzyQ-QOixKvEmM8NqjdUyKMaH2QVCVyZiIImbgdE92xipeKugQe2ie_c_1j0R_t3sQ7jcru6u05uAcrCkHsBEP9b4W_d', },
                     { number: '79298667708', token:'vk1.a.R-oPzS0kWv_vVYNmt-QLLu38OzZ-keOTpGoBHkkzi9FFDmz0-vFjU6fS0wIqUnjgWEhF4LJcGy-y_1P95H9ZS7oJQlnecRYOaRywvugzVyPA8wV2RU5NpZdV4j2ai1XwF1Sbqo1tdt9AY24zqAmQOUUv928DG50rT0prtbHYXa84eEP50-EfVtiPyfrDZsyS', },
                     ]
            }
        },

        mounted() {
            //this.get_list_of_search_group(this.group_list)
        },
        methods: {
            send_inv_message_second()
            {
                  if(this.vk_akks_arr.length>0)
                {
                axios
                    .post('/send_inv_message_second',{
                        number:this.vk_akks_arr[0]['number'],
                        token:this.vk_akks_arr[0]['token'],
                    })
                    .then(({ data }) => {
                        this.render_count_arr();
                        this.send_inv_message_second();
                        },
                    )
                }
            },
            send_inv_message()
            {
                   if(this.vk_akks_arr.length>0)
                {
                axios
                    .post('/send_inv_message',{
                        number:this.vk_akks_arr[0]['number'],
                        token:this.vk_akks_arr[0]['token'],
                    })
                    .then(({ data }) => {
                        this.render_count_arr();
                        this.send_inv_message();
                        },
                    )
                }
            },
            render_count_arr()
            {
                this.vk_akks_arr.splice(0, 1);
            }

        }
    }
</script>
