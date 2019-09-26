<!--
  - This program is free software; you can redistribute it and/or
  - modify it under the terms of the GNU General Public License
  - as published by the Free Software Foundation; under version 2
  - of the License (non-upgradable).
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  - You should have received a copy of the GNU General Public License
  - along with this program; if not, write to the Free Software
  - Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
  - Copyright (c) 2019 (original work) MedCenter24.com;
  -->

<template>
    <div class="container white p-2">
        <alert-component ref="msgAlert"></alert-component>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Current Configuration</h5>
                        <h6 class="card-subtitle mb-2 text-success" v-if="isActive && !fromInput">Configured</h6>
                        <h6 class="card-subtitle mb-2 text-success" v-if="isActive && fromInput">Configured</h6>
                        <h6 class="card-subtitle mb-2 text-danger" v-if="!isActive">NOT Configured</h6>
                        <p v-if="isActive">Please, make sure that you have slack in the .env log configuration. <br>
                            Systems min error level is <b>{{ errorLevel }}</b>.</p>
                        <p v-if="!isActive">You can't send default messages.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-2">
                <button class="btn btn-dark"
                        v-on:click="send('critical')"
                        :disabled="!isActive && !fromInput">Critical</button>
            </div>
            <div class="col-2">
                <button class="btn btn-danger"
                        v-on:click="send('error')"
                        :disabled="!isActive && !fromInput">Error</button>
            </div>
            <div class="col-2">
                <button class="btn btn-warning"
                        v-on:click="send('warning')"
                        :disabled="!isActive && !fromInput">Warning</button>
            </div>
            <div class="col-2">
                <button class="btn btn-dark"
                        v-on:click="send('debug')"
                        :disabled="!isActive && !fromInput">Debug</button>
            </div>
            <div class="col-2">
                <button class="btn btn-info"
                        v-on:click="send('info')"
                        :disabled="!isActive && !fromInput">Info</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="https://slack.com/intl/en-by/help/articles/115005265063-incoming-webhooks-for-slack">How to create WebHook</a>
            </div>
        </div>
    </div>
</template>

<script>
  import SlackPreviewProvider from '../../providers/slack.preview.provider'
  import AlertComponent from '../ui/alert'

  export default {
    name: "preview-page",
    components: {
      AlertComponent
    },
    data () {
      return {
        isActive: false,
        fromInput: false,
        customWebHook: '',
        errorLevel: 'not set',
      }
    },
    /**
     * Prepare the component (Vue 2.x).
     */
    mounted() {
      this.getInfo();
    },
    methods: {
      getInfo () {
        SlackPreviewProvider.getInfo().then(response => {
          this.isActive = !!response.data.initialized;
          this.errorLevel = response.data.level;
        }).catch(err => {
          this.$refs.msgAlert.error({additionalInfo: err.toString()})
        });
      },
      setWebHook() {
        this.fromInput = !!this.customWebHook.length;
      },
      send(type) {
        SlackPreviewProvider.log({type, webhook: this.customWebHook}).then(response => {
          if (response.data.success) {
            this.$refs.msgAlert.success({additionalInfo: 'Log was created'})
          } else {
            this.$refs.msgAlert.error({additionalInfo: response.data.message})
          }
        }).catch(err => {
          this.$refs.msgAlert.error({additionalInfo: err.toString()})
        });
      }
    }
  }
</script>

<style scoped>

</style>