<template>
    <SalesLayout>

      
    </SalesLayout>
</template>
  
<style>
table {
    width: 90%;
    border-collapse: collapse;
}

th {
    border-top: 1px solid #333;
    border-bottom: 1px solid #333;
    background: rgb(181, 195, 236);
    padding: 5px;
}

td {
    border-top: 1px solid #333;
    border-bottom: 1px solid #333;
    background: #ffffff;
    padding: 5px;
}
</style>
  
  
  
  
  
<script>
import SalesLayout from "./SalesLayout";
import { Link } from "@inertiajs/inertia-vue";

export default {
    components: { SalesLayout, Link },


    data() {
        return {
          
            loading: {}
        }
    },
    computed: {

    },

    methods: {
     
    }
}
</script>
  