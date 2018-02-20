<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
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

        restoreWebhook () {
            return Vue.axios.post(this.getUrl('webhook'));
        }
    }
</script>
