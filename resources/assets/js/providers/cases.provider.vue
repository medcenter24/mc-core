<!--
  - Copyright (c) 2017.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<script>
    import Vue from 'vue'

    export default {
        getUrl (src) {
            return '/admin/cases' + (src && src.length ? '/' + src : '');
        },
        search (options) {
            return Vue.axios.get(this.getUrl(), options)
        },
        getReportHtml (refNum) {
            return Vue.axios.get(this.getUrl('report'), {params: {ref: refNum}});
        },
        downloadPdf (refNum) {
            return Vue.axios.get(this.getUrl('pdf'), {params: {ref: refNum}, responseType: 'arraybuffer'})
                .then(response => {
                    let blob = new Blob([response.data], { type:   'application/pdf' } );
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'report_' + refNum + '.pdf';
                    link.click();
                });
        },
        getHistory (refNum) {
            return Vue.axios.get(this.getUrl('history'), {params: {ref: refNum}});
        }
    }
</script>
