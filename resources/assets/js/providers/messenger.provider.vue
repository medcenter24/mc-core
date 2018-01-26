<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<script>
    import Vue from 'vue'

    export default {
        getUrl (src)
        {
            return '/admin/messenger' + (src && src.length ? '/' + src : '');
        },
        getThreads ()
        {
            return Vue.axios.get(this.getUrl('thread'));
        },
        createThread ()
        {
            return Vue.axios.post(this.getUrl('thread'));
        },
        getThreadUrl (id)
        {
            return this.getUrl('thread/' + id);
        },
        getCounts ()
        {
            return Vue.axios.get(this.getUrl('thread/counts'));
        }
    }
</script>
