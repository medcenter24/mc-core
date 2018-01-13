<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">Thread</th>
                <th scope="col">Created at</th>
                <th scope="col">Updated at</th>
                <th scope="col">Deleted at</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="thread in threads">
                <td scope="row">{{ thread.subject }}</td>
                <td>{{ thread.created_at }}</td>
                <td>{{ thread.updated_at }}</td>
                <td>{{ thread.deleted_at }}</td>
                <td>
                    <button class="btn" v-on:click="goToThread(thread.id)">Open</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import MessengerProvider from "../../providers/messenger.provider";

    export default {
        name: "message-threads-component",
        data () {
            return {
                threads: []
            };
        },
        created: function() {
            this.reloadThreads();
        },
        methods: {
            reloadThreads() {
                MessengerProvider.getThreads().then(response => this.threads = response.data);
            },
            goToThread(id) {
                window.location = MessengerProvider.getThreadUrl(id);
            }
        }
    }
</script>

<style scoped>

</style>