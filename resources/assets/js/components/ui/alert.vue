<!--
  - This program is free software; you can redistribute it and/or
  - modify it under the terms of the GNU General Public License
  - as published by the Free Software Foundation; under version 2
  - of the License (non-upgradable).
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  - You should have received a copy of the GNU General Public License
  - along with this program; if not, write to the Free Software
  - Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
  -
  - Copyright (c) 2019 (original work) MedCenter24.com;
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