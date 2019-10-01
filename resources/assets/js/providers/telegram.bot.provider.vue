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
<script>
    export default {

        getUrl (src) {
            return '/admin/telegram' + (src && src.length ? '/' + src : '');
        },

        getMe () {
            return Vue.axios.get(this.getUrl('getMe'));
        },

        sendMessage (options) {
            return Vue.axios.post(this.getUrl('message/send'), options);
        },

        getWebhookInfo () {
            return Vue.axios.get(this.getUrl('webhook'));
        },

        setWebhook (params) {
            return Vue.axios.put(this.getUrl('webhook/1'), params);
        },

        deleteWebhook () {
            return Vue.axios.delete(this.getUrl('webhook/1'));
        },
    }
</script>
