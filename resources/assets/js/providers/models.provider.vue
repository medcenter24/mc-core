<!--
  - This program is free software; you can redistribute it and/or
  - modify it under the terms of the GNU General Public License
  - as published by the Free Software Foundation; under version 2
  - of the License (non-upgradable).
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
        getUrl (src)
        {
            return '/admin/system/models' + (src && src.length ? '/' + src : '');
        },
        getModels ()
        {
            return Vue.axios.get(this.getUrl('search'));
        },
        getRelations (modelName) {
            return Vue.axios.post(this.getUrl('relations'), {name: modelName});
        }
    }
</script>
