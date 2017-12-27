<!--
  - Copyright (c) 2017.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<script>
    import Vue from 'vue'
    export default {
        search (options) {
            return Vue.axios.get('/admin/cases', options)
        },
        getReportHtml (refNum) {
            return Vue.axios.get('/admin/cases/report', {params: {ref: refNum}});
        }
    }
</script>
