<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="alert fade show" :class="'alert-' + conf.type" v-if="conf.shown" role="alert">
        <strong>{{ conf.title }}</strong> {{ conf.message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <hr v-if="conf.additionalInfo">
        <p class="mb-0" v-if="conf.additionalInfo">{{ conf.additionalInfo }}</p>
    </div>
</template>

<script>
    export default {
        name: "alert-component",
        data () {
            return {
                conf: {
                    type: 'success',
                    shown: false,
                    title: 'Title',
                    message: 'Message',
                    additionalInfo: false
                }
            }
        },

        methods: {
            show (conf) {
                this.conf = Object.assign({}, this.conf, conf);
            },
            success (conf) {
                this.conf = Object.assign({}, this.conf, {
                    type: 'success',
                    title: 'Success',
                    message: 'Everything is awesome',
                    shown: true,
                }, conf);
            },
            error (conf) {
                this.conf = Object.assign({}, this.conf, {
                    type: 'danger',
                    title: 'Error',
                    message: 'Something bad happened.',
                    shown: true,
                }, conf);
            },
            httpError (error) {
                let info = '';
                if (error.response.status === 403) {
                    // cors issue
                    info = error.message;
                } else if (error.response.status === 500) {
                    info = error.response.data.message;
                } else if (error.response.status === 422) { // laravel request error
                    info = JSON.stringify(error.response.data, null, 2);
                } else {
                    info = error.message;
                }
                this.error({message: error.toString(), additionalInfo: info});
            },
            warning (conf) {
                this.conf = Object.assign({}, this.conf, {
                    type: 'warning',
                    title: 'Warning',
                    message: 'You need to be careful.',
                    shown: true,
                }, conf);
            },
            info (conf) {
                this.conf = Object.assign({}, this.conf, {
                    type: 'info',
                    title: 'Information',
                    message: 'Something that you need to know.',
                    shown: true,
                }, conf);
            }
        }
    }
</script>

<style scoped>

</style>