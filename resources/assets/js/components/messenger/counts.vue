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