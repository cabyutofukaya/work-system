<template>
  <Layout>
    ホーム画面表示
  </Layout>
</template>


<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: [],

  data: () => ({
    loading: {},
  }),

  data() {
    return {

    };
  },

}
</script>
