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