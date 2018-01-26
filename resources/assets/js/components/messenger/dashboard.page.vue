<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="container white p-2">
        <div class="justify-content-end">
            <button class="btn btn-info" v-on:click="createMsgThread()">Create new thread</button>
        </div>

        <messages-counts-component></messages-counts-component>
        <message-threads-component></message-threads-component>
    </div>
</template>

<script>
    import MessageThreadsComponent from "./threads";
    import MessagesCountsComponent from "./counts";
    import MessengerProvider from "../../providers/messenger.provider";

    export default {
        components: {
            MessagesCountsComponent,
            MessageThreadsComponent
        },
        name: "messenger-dashboard",
        methods: {
            createMsgThread () {
                MessengerProvider.createThread()
                    .then(response => window.location = MessengerProvider.getThreadUrl(response.data.id));
            }
        }
    }
</script>

<style scoped>

</style>