<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="row text-center m-4">
        <div class="col-sm">
            <h3>Threads {{ threads }}</h3>
        </div>
        <div class="col-sm">
            <h3>Messages {{ messages }}</h3>
        </div>
    </div>
</template>

<script>

    import MessengerProvider from "../../providers/messenger.provider";

    export default {
        name: "messages-counts-component",
        data () {
            return {
                threads: 0,
                messages: 0
            }
        },
        created: function () {
            this.refreshCounts()
        },
        methods: {
            refreshCounts () {
                MessengerProvider.getCounts().then(response => {
                    this.threads = response.data.threads;
                    this.messages = response.data.messages;
                });
            }
        }
    }
</script>

<style scoped>

</style>