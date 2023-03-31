<template>
  <a @click.prevent="close">
    <v-overlay v-if="url" z-index="10">
      <v-img
          contain max-height="90vh" max-width="90vw" alt=""
          :src="url"
      ></v-img>
    </v-overlay>
  </a>
</template>

<script>
export default {
  props: ['url'],

  methods: {
    close: function () {
      this.$emit('update:url', undefined);
    },
  }
};
</script>
