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