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
    import Vue from 'vue'

    export default {
        getUrl (src) {
            return '/admin/cases' + (src && src.length ? '/' + src : '');
        },
        search (options) {
            return Vue.axios.get(this.getUrl(), options);
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
