<!--
  - Copyright (c) 2018. 
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="container white p-2 telegram-bot-container">
        <div class="row message-action">
            <div class="col-6">

                <alert-component ref="msgAlert"></alert-component>

                <div class="row mb-1">
                    <div class="col-12">
                        <label for="telegram-id">Telegram ID To <small>You can write to me: 344795925</small></label>
                        <input type="text" id="telegram-id" class="form-control telegram-id"
                               v-model="msgTelegramReceiverID"
                               placeholder="Telegram ID">
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-12">
                        <label for="tlg-message">Text</label>
                        <textarea id="tlg-message" class="form-control message"
                                  placeholder="What do you want to say?"
                                  name="text"
                            v-model="msgText"></textarea>
                    </div>
                </div>
                <button class="btn btn-info send" v-on:click="sendMessage()">Send Message</button>
            </div>
            <div class="col-6">
                <h3>Information from Telegram about Me</h3>
                <p>Telegram ID: <b>{{ myTelegramId }}</b></p>
                <p>Username: <b>{{ myUsername }}</b></p>
                <p>Name: <b>{{ myName }}</b></p>
                <p>Surname: <b>{{ mySurname }}</b></p>
                <p>isBot: <b>{{ myIsBot }}</b></p>
            </div>
        </div>

        <div class="row">
            <div class="col-6">

                <alert-component ref="webhookAlert"></alert-component>

                <div class="row mb-1">
                    <div class="col-12">
                        <label for="webhook" class="label">Change Webhook URL</label>
                        <input type="text" id="webhook" class="form-control" v-model="webhookUrl" placeholder="Enter webhook to change">
                    </div>
                </div>
                <button class="btn btn-info" v-on:click="setWebhook()">Set new webhook</button>
                <button class="btn btn-default" v-on:click="restoreWebhook()">Restore default webhook</button>
            </div>
            <div class="col-6">
                <h3>Information from Telegram about Webhook</h3>
                <p>Url: <b>{{ webhookInfoUrl }}</b> <small class="text-muted">if not set then will be acceptable with getUpdates</small></p>
                <p>With Certificate: <b>{{ webhookInfoCert }}</b></p>
                <p>Pending Count: <b>{{ webhookInfoPendingCount }}</b></p>
                <p>Webhook Max Connections: <b>{{ webhookMaxConnections }}</b></p>
                <p>Webhook Allowed Updates: <b>{{ webhookAllowedUpdates }}</b></p>
                <button class="btn btn-warning" v-if="webhookInfoUrl && webhookInfoUrl.length" v-on:click="deleteWebhook()">Delete webhook</button>
            </div>
        </div>
    </div>
</template>

<script>
    import TelegramBotProvider from '../../providers/telegram.bot.provider'
    import AlertComponent from '../ui/alert'

    export default {
        name: "telegram-bot",
        components: {
            AlertComponent
        },
        data () {
            return {
                msgTelegramReceiverID: '',
                msgText: '',

                myTelegramId: 'Loading...',
                myUsername: '',
                myName: '',
                mySurname: '',
                myIsBot: '',

                webhookUrl: '',

                webhookInfoUrl: '',
                webhookInfoCert: 'Loading...',
                webhookInfoPendingCount: 0,
                webhookMaxConnections: '',
                webhookLastError: '',
                webhookAllowedUpdates: ''
            }
        },
        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.getMe();
            this.getWebhookInfo();
        },
        methods: {
            sendMessage () {
                TelegramBotProvider.sendMessage({
                    receiverId: this.msgTelegramReceiverID,
                    msg: this.msgText
                }).then(response => {
                    this.$refs.msgAlert.success({additionalInfo: response.data});
                }).catch(err => {
                    this.$refs.msgAlert.httpError(err);
                })
            },
            getMe () {
                TelegramBotProvider.getMe().then(response => {
                    this.myTelegramId = response.data.id;
                    this.myUsername = response.data.username;
                    this.myName = response.data.firstName;
                    this.mySurname = response.data.lastName;
                    this.myIsBot = response.data.isBot;
                }).catch(err => {
                    this.$refs.msgAlert.error({additionalInfo: err.toString()})
                });
            },
            setWebhook () {
                TelegramBotProvider.setWebhook({ webhook: this.webhookUrl }).then(response => {
                    if (response.data.status) {
                        this.$refs.webhookAlert.success({message: 'Webhook has been set'});
                    } else {
                        this.$refs.webhookAlert.warning({message: 'Response status is not correct, needs to be investigated'});
                    }
                    this.getWebhookInfo();
                }).catch(err => this.$refs.webhookAlert.httpError(err));
            },
            getWebhookInfo () {
                TelegramBotProvider.getWebhookInfo().then(response => {
                    console.log(response);
                    this.webhookInfoUrl = response.data.webhookUrl;
                    this.webhookInfoCert = response.data.certificate;
                    this.webhookInfoPendingCount = response.data.pendingUpdateCount;
                    this.webhookMaxConnections = response.data.maxConnections;
                    this.webhookLastError = response.data.lastErrorDate;
                    this.webhookAllowedUpdates = response.data.allowedUpdates;
                });
            },
            restoreWebhook() {
                TelegramBotProvider.restoreWebhook().then(response => {
                    if (response.data.status) {
                        this.$refs.webhookAlert.success({message: 'Webhook has been restored to default'});
                    } else {
                        this.$refs.webhookAlert.warning({message: 'Response status is not correct, needs to be investigated'});
                    }
                    this.getWebhookInfo();
                }).catch(err => this.$refs.webhookAlert.httpError(err));
            },
            deleteWebhook() {
                TelegramBotProvider.deleteWebhook().then(response => {
                    if (response.data.status) {
                        this.$refs.webhookAlert.success({message: 'Webhook has been deleted'});
                    } else {
                        this.$refs.webhookAlert.warning({message: 'Response status is not correct, needs to be investigated'});
                    }
                    this.getWebhookInfo();
                }).catch(err => this.$refs.webhookAlert.httpError(err));
            }
        }
    }
</script>

<style scoped>

</style>